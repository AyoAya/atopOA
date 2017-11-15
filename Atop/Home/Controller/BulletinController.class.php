<?php
namespace Home\Controller;

use Think\Model;
use Think\Page;

class BulletinController extends AuthController
{


    public function index()
    {

        $model = new model();

        $where = 'status=2 OR (auth_person regexp "^'.session('user')['id'].'," OR auth_person regexp ",'.session('user')['id'].'$" OR auth_person regexp ",'.session('user')['id'].',") 
                 ';


        $count= $model->table(C('DB_PREFIX') . 'bulletin ')
                      ->where($where)
                      ->order('create_time DESC')
                      ->count();

        # print_r($count);

        $page = new Page($count, 10);
        $page->setConfig('prev', '<span aria-hidden="true">上一页</span>');
        $page->setConfig('next', '<span aria-hidden="true">下一页</span>');
        $page->setConfig('first', '<span aria-hidden="true">首页</span>');
        $page->setConfig('last', '<span aria-hidden="true">尾页</span>');

        $bulletin= $model->table(C('DB_PREFIX') . 'bulletin ')
                         ->where($where)
                         ->order('create_time DESC')
                         ->limit($page->firstRow . ',' . $page->listRows)
                         ->select();

        foreach ($bulletin as $key=>&$value){
            $value['nickname'] = $model->table(C('DB_PREFIX').'user')->field('nickname')->where('id ='.$value['create_person'])->find()['nickname'];
        }

        foreach ($bulletin as $key=>&$value){
            $value['review'] = $model->table(C('DB_PREFIX') . 'bulletin_review')->field('person')->where('b_id ='.$value['id'])->select();
            foreach ($value['review'] as $k=>&$v){
                $value['reviewPerson'][] = $v['person'];
            }
            array_push($value['reviewPerson'],$value['create_person']);

        }

        if (C('PAGE_STATUS_INFO')) {
            $page->setConfig('theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%');
        }

        if (I('get.p')) {
            $this->assign('pageNumber', I('get.p') - 1);
        }

        $pageShow = $page->show();

        $this->assign('bulletin', $bulletin);
        $this->assign('page', $pageShow);
        $this->display();
    }


    public function add(){

        $p = explode(',',session('user')['post']);

        if(in_array(1781,$p)){

            $model = new model();
            $model->startTrans();

            if (IS_POST) {

                $post = I('post.', '', false);

                # bulletin表数据
                $bulletin['title'] = $post['introduction'];
                $bulletin['atta'] = $post['attachment'];
                $bulletin['content'] = str_replace("\n", "<br>", $post['context']);
                $bulletin['create_person'] = session('user')['id'];
                $bulletin['create_time'] = time();
                # 多个部门或单个部门
                if ($post['department'][0] != 'all') {

                    $tmpDepartment = '';

                    foreach ($post['department'] as $key => &$value) {
                        $tmpDepartment .= $value . ',';
                    }

                    $bulletin['department'] = substr($tmpDepartment, 0, strlen($tmpDepartment) - 1);

                # 所有部门
                } else {
                    $bulletin['department'] = 'all';
                }

                # 收集首页有权限的人
                if ($post['vice']) {
                    $tmpAuth = $post['vice'];
                    $a[0] = $post['manager'];
                    $b[0] = session('user')['id'];
                    $tmpAuthPerson = array_merge($a,$b,$tmpAuth);
                }else{
                    $a[0] = $post['manager'];
                    $b[0] = session('user')['id'];
                    $tmpAuthPerson = array_merge($a,$b);
                }
                $_authPerson = '';
                foreach ($tmpAuthPerson as $key=>&$value){
                    $_authPerson .= $value . ',';
                }
                $bulletin['auth_person'] = substr($_authPerson, 0, strlen($_authPerson) - 1);

                $bull_id = $model->table(C('DB_PREFIX') . 'bulletin')->add($bulletin);

                $data['title'] = $post['introduction'];
                $data['context'] = $post['context'];
                $data['id'] = $bull_id;

                if($bulletin['department'] != 'all'){
                    $tmpA = explode(',',$bulletin['department']);
                    foreach ($tmpA as $k=>&$v){
                        $data['department'][] = $model->table(C('DB_PREFIX').'department')->field('name')->where('id ='.$v)->find();
                    }
                }else{
                    $data['department'] = 'all';
                }

                if ($bull_id) {
                    # 判断是否有副总审批
                    if ($post['vice']) {

                        foreach ($post['vice'] as $key => &$value) {
                            $review['b_id'] = $bull_id;
                            $review['level'] = 1;
                            $review['person'] = $value;
                            $review_id = $model->table(C('DB_PREFIX') . 'bulletin_review')->add($review);
                            if (!$review_id) {
                                $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                            }
                            # 待办
                            $todoList['matter_name'] = ' [公告栏] 《' . $data['title'] . '》公告审批';
                            $todoList['url'] = $_SERVER['HTTP_HOST'] . "/Bulletin/detail/id/" . $bull_id;
                            $todoList['who'] = $value;

                            $this->insertNewMatter($todoList);
                            # 邮件推送
                            $emailData = $model->table(C('DB_PREFIX').'user')->where('id ='.$value)->field('nickname,email,id')->find();
                            $data['nickname'] = $emailData['nickname'];

                            $this->pushEmail('ADD',$emailData['email'],$data,session('user')['email']);
                        }

                        $rw['b_id'] = $bull_id;
                        $rw['level'] = 2;
                        $rw['person'] = $post['manager'];

                        $rw_id = $model->table(C('DB_PREFIX') . 'bulletin_review')->add($rw);

                        if ($rw_id) {
                            $model->commit();
                            $this->ajaxReturn(['flag' => $bull_id, 'msg' => '提交数据成功！']);
                        } else {
                            $model->rollback();
                            $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                        }

                    } else {

                        $review['b_id'] = $bull_id;
                        $review['level'] = 1;
                        $review['person'] = $post['manager'];

                        # 邮件推送
                        $emailData = $model->table(C('DB_PREFIX').'user')->where('id ='.$post['manager'])->field('nickname,email,id')->find();
                        $data['nickname'] = $emailData['nickname'];

                        $review_id = $model->table(C('DB_PREFIX') . 'bulletin_review')->add($review);

                        if (!$review_id) {
                            $model->rollback();
                            $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                        } else {
                            # 添加待办
                            $todoList['matter_name'] = ' [公告栏] 《' . $data['title'] . '》公告审批';
                            $todoList['url'] = $_SERVER['HTTP_HOST'] . "/Bulletin/detail/id/" . $bull_id;
                            $todoList['who'] = $post['manager'];
                            $this->insertNewMatter($todoList);

                            $this->pushEmail('ADD',$emailData['email'],$data,session('user')['email']);
                            $model->commit();
                            $this->ajaxReturn(['flag' => $bull_id, 'msg' => '提交数据成功！']);
                        }

                    }
                } else {
                    $model->rollback();
                    $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                }

            } else {

                # 总经理信息
                $manager = $model->table(C('DB_PREFIX') . 'user a,' . C('DB_PREFIX') . 'userlevel b')->field('a.nickname,a.id,a.email')->where('a.level = 7 AND a.level = b.id')->select();
                # 部门信息
                $department = $model->table(C('DB_PREFIX') . 'department')->field('id,name')->select();
                # 副总
                $vice = $model->table(C('DB_PREFIX') . 'user a,' . C('DB_PREFIX') . 'userlevel b')->field('a.nickname,a.id,a.email')->where('a.level = 6 AND a.level = b.id')->select();

                $this->assign('manager', $manager);
                $this->assign('department', $department);
                $this->assign('vice', $vice);
                $this->display();
            }
        }else{
            $this->error('没有权限！');
        }


    }


    public function detail()
    {

        $model = new model();
        $model->startTrans();

        $id = I('get.id');

        if (IS_POST) {

            $post = I('post.');
            # 下一步操作人
            $nextLevel = $model->table(C('DB_PREFIX').'bulletin_review')->where('b_id ='.$post['bid'] . ' AND level =' . ($post['level'] + 1))->select();

            $data = $model->table(C('DB_PREFIX').'bulletin a,'.C('DB_PREFIX').'user b')
                          ->field('a.content,a.id,a.title,a.create_person,a.create_time,a.status,a.atta,a.department,b.nickname,b.email')
                          ->where('a.id ='.$post['bid'].' AND a.create_person = b.id')
                          ->find();

            if($data['department'] == 'all'){
                $bulletinPersonData = $model->table(C('DB_PREFIX').'user')->field('nickname,email,id')->where('id <> 1 AND state = 1')->select();
            }else{
                $departmentArr = explode(",", $data['department']);
                $in['department'] = array('in',$departmentArr);
                $bulletinPersonData = $model->table(C('DB_PREFIX').'user')->field('nickname,email,id')->where('id <> 1 AND state = 1 AND department in (' . $data['department'] . ')')->select();
            }
            $bulletinPerson = [];
            foreach ($bulletinPersonData as $key=>&$value){
                $bulletinPerson[] = $value['email'];
            }

            $bulletinEmailData['title'] = $data['title'];
            $bulletinEmailData['content'] = $data['content'];
            $bulletinEmailData['manager'] = $data['nickname'];
            $bulletinEmailData['create_time'] = $data['create_time'];
            $bulletinEmailData['atta'] = json_decode($data['atta'],true);
            $bulletinEmailData['id'] = $data['id'];

            $all = $model->table(C('DB_PREFIX').'bulletin_review a,'.C('DB_PREFIX') . 'user b')
                         ->field('a.person,b.nickname,b.email')
                         ->where('a.person = b.id AND a.b_id ='.$post['bid'])
                         ->select();

            # 参与审批的所有人邮箱(抄送人)
            $allPersonEmail = [];
            foreach ($all as $key=>&$value){
                $allPersonEmail[] = $value['email'];
            }

            $tmpCreatePersonEmail[] = $data['email'];
            $allPersonEmail = array_merge($tmpCreatePersonEmail,$allPersonEmail);

             # print_r($allPersonEmail);
             # die();

            # 创建人(收件人)
            $email = $model->table(C('DB_PREFIX').'bulletin a,'.C('DB_PREFIX').'user b')
                           ->field('b.nickname,b.email,b.id')
                           ->where('a.id ='.$post['bid'].' AND a.create_person = b.id')
                           ->find();
            $emailPerson[] = $email['email'];
            $data['nickname'] = $email['nickname'];
            $data['log'] = $post['context'];

            # log表数据
            $log['type'] = $post['type'];
            $log['log'] = $post['context'];
            $log['b_id'] = $post['bid'];
            $log['person'] = session('user')['id'];
            $log['time'] = time();
            $log['r_id'] = $post['rid'];

            $log_id = $model->table(C('DB_PREFIX') . 'bulletin_log')->add($log);

            if ($log_id) {
                # 通过操作
                if ($post['type'] == 'pass') {

                    $review['state'] = 'success';
                    $review_id = $model->table(C('DB_PREFIX') . 'bulletin_review')->where('id =' . $post['rid'])->save($review);

                    # 当前步骤操作人
                    $nowLevel = $model->table(C('DB_PREFIX') . 'bulletin_review')->where('b_id =' . $post['bid'] . ' AND level =' . $post['level'])->select();
                    $countLevel = $model->table(C('DB_PREFIX') . 'bulletin_review')->where('b_id =' . $post['bid'] . ' AND level =' . $post['level'])->count();

                    if ($review_id) {
                        $num = 0;
                        foreach ($nowLevel as $key => &$value) {
                            if ($value['state'] != 'default') {
                                $num += 1;
                            }
                        }

                        # 当前步骤全部通过
                        if ($num == $countLevel) {

                            # 添加待办
                            $todoList['matter_name'] = ' [公告栏] 《' . $data['title'] . '》公告审批';
                            $todoList['url'] = $_SERVER['HTTP_HOST'] . "/Bulletin/detail/id/" . $post['bid'];

                            # 存在下一步
                            if ($nextLevel) {

                                $bulletin['step'] = $nowLevel[0]['level'] + 1;
                                $bulletin_id = $model->table(C('DB_PREFIX') . 'bulletin')->where('id =' . $post['bid'])->save($bulletin);

                                if ($bulletin_id) {

                                    foreach ($nextLevel as $k => &$v) {
                                        $todoList['who'] = $v['person'];
                                        $this->insertNewMatter($todoList);
                                    }

                                    $this->markMatterAsDoneSpecifyUserState($todoList['url']);
                                    $model->commit();
                                    $this->pushEmail('PASS',$emailPerson,$data,$allPersonEmail);
                                    $this->ajaxReturn(['flag' => 1, 'msg' => '提交数据成功！']);
                                } else {
                                    $model->rollback();
                                    $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                                }

                                # 不存在下一步
                            } else {

                                $bulletin['status'] = 2;
                                $bulletin_id = $model->table(C('DB_PREFIX') . 'bulletin')->where('id =' . $post['bid'])->save($bulletin);

                                if ($bulletin_id) {
                                    $this->markMatterAsDoneSpecifyUserState($todoList['url']);
                                    $model->commit();
                                    $this->pushEmail('SUCCESS',$emailPerson,$data,$allPersonEmail);
                                    $this->BulletinEmail($bulletinPerson,$bulletinEmailData,$allPersonEmail);
                                    $this->ajaxReturn(['flag' => 1, 'msg' => '提交数据成功！']);
                                } else {
                                    $model->rollback();
                                    $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                                }

                            }

                        } else {
                            $todoList['url'] = $_SERVER['HTTP_HOST'] . "/Bulletin/detail/id/" . $post['bid'];
                            $model->commit();
                            $this->markMatterAsDoneSpecifyUserState($todoList['url']);
                            $this->pushEmail('PASS',$emailPerson,$data,$allPersonEmail);
                            $this->ajaxReturn(['flag' => 1, 'msg' => '提交数据成功！']);
                        }

                    } else {
                        $model->rollback();
                        $this->ajaxReturn(['flag' => 0, 'msg' => '提交数据失败！']);
                    }

                    # 拒绝操作
                } else {
                    $review['state'] = 'refuse';
                    $review_id = $model->table(C('DB_PREFIX') . 'bulletin_review')->where('id =' . $post['rid'])->save($review);

                    if ($review_id) {

                        $bulletin['status'] = 0;
                        $bulletin_id = $model->table(C('DB_PREFIX') . 'bulletin')->where('id =' . $post['bid'])->save($bulletin);

                        if ($bulletin_id) {
                            $todoList['url'] = $_SERVER['HTTP_HOST'] . "/Bulletin/detail/id/" . $post['bid'];
                            $this->markMatterAsDoneSpecifyURL($todoList['url']);
                            $model->commit();
                            $this->pushEmail('REFUSE',$emailPerson,$data,$allPersonEmail);
                            $this->ajaxReturn(['flag' => 1, 'msg' => '提交数据成功！']);
                        } else {
                            $model->rollback();
                            $this->ajaxReturn(['flag' => 0, 'msg' => '操作失败！']);
                        }

                    } else {
                        $model->rollback();
                        $this->ajaxReturn(['flag' => 0, 'msg' => '提交数据失败！']);
                    }
                }

            } else {
                $model->rollback();
                $this->ajaxReturn(['flag' => 0, 'msg' => '提交数据失败！']);
            }

        } else {

            # bulletin数据
            $bulletin = $model->table(C('DB_PREFIX') . 'bulletin a,' . C('DB_PREFIX') . 'user b')
                              ->field('b.nickname,a.id,a.title,a.create_time,a.step,a.create_person,a.atta,a.status,a.content,a.department')
                              ->where('a.create_person = b.id AND a.id =' . $id . ' AND a.status = 1')
                              ->select();

            # bulletin数据
            $bulletinContent = $model->table(C('DB_PREFIX') . 'bulletin a,' . C('DB_PREFIX') . 'user b')
                                     ->field('b.nickname,a.id,a.title,a.create_time,a.step,a.create_person,a.atta,a.status,a.content,a.department')
                                     ->where('a.create_person = b.id AND a.id =' . $id)
                                     ->select();

            foreach ($bulletinContent as $key => &$value) {
                $value['atta'] = json_decode($value['atta'], true);

                if( $value['department'] != 'all' ){
                    $value['department'] = explode(',',$value['department']);
                }

            }

            foreach ($bulletin as $key => &$value) {
                $value['atta'] = json_decode($value['atta'], true);

                # 当前可操作人(当前步骤)
                $nowPerson = $model->table(C('DB_PREFIX') . 'bulletin_review')->field('person')->where('level =' . $value['step'] . ' AND state = "default"  AND b_id =' . $id)->select();

            }

            foreach ($bulletinContent[0]['department'] as $k=>&$v){
                $bulletinContent[0]['departmentName'][] = $model->table(C('DB_PREFIX').'department')->field('name')->where('id ='.$v)->find();
            }


            foreach ($nowPerson as $k => &$v) {
                $tmpArr[] = $v['person'];
            }

            # 审批人数据
            $review = $model->table(C('DB_PREFIX') . 'bulletin_review')->where('b_id =' . $id)->select();
            $create_person = $model->table(C('DB_PREFIX') . 'bulletin')->field('create_person')->where('id =' . $id)->select();

            foreach ($review as $k => &$v) {
                $tmpAll[] = $v['person'];
            }

            array_push($tmpAll, $create_person[0]['create_person']);

            foreach ($review as $key => &$value) {

                $value['count'] = $model->table(C('DB_PREFIX') . 'bulletin_review')->where('b_id =' . $id . ' AND level=' . $value['level'])->count();

                $value['nickname'] = $model->table(C('DB_PREFIX') . 'user')->field('nickname')->find($value['person'])['nickname'];
                $value['log'] = $model->table(C('DB_PREFIX') . 'bulletin_log')->where('b_id =' . $id . ' AND r_id =' . $value['id'])->find();
            }

            # 当前操作人
            $nowStepPerson = $model->table(C('DB_PREFIX') . 'bulletin_review')->where('b_id =' . $id . ' AND person =' . session('user')['id'])->find();

            $this->assign('review', $review);
            $this->assign('person', $tmpArr);
            $this->assign('all', $tmpAll);
            $this->assign('nowStepPerson', $nowStepPerson);
            $this->assign('bulletin', $bulletin);
            $this->assign('bulletins', $bulletinContent);
            $this->display();
        }

    }


    /**
     * 附件上传
     */
    # 上传附件
    public function upload()
    {

        $result = upload(I('post.PATH'));

        $this->ajaxReturn($result);
    }

    /**
     * 邮件通知
     */
    # 邮件推送
    public function pushEmail( $type,$address, $data, $cc = []){

        $http_host = $_SERVER['HTTP_HOST'];
        extract($data);

        $subject = ' [公告栏] 《' . $data['title'] . '》公告审批';

        switch ( $type ){

            case 'ADD':

                $call = $data['nickname'];

                $body = '<p>Dear '.$call.',</p>
<p>['.session('user')['nickname'].'] 发起了<b>《'.$data['title'].'》</b>的新公告，发布范围：';

        if($data['department'] == 'all'){
            $body .= '<span style="padding: 5px;">全公司</span>';
        }else{
            foreach ($data['department'] as $k=>&$v){
                $body .= '<span style="padding: 5px;">'.$v['name'].'</span>';
            }
        }

$body .= '，请及时处理。</p>
<p>详情请点击链接：<a href="http://'.$http_host.'/Bulletin/detail/id/'.$data['id'].'" target="_blank">http://'.$http_host.'/Bulletin/detail/id/'.$data['id'].'</a></p>

';

                break;
            case 'PASS':

                $call = $data['nickname'];

                $body = '<p>Dear '.$call.',</p>
<p>['.session('user')['nickname'].'] 通过了您发起的<b>《'.$data['title'].'》</b>公告审批。</p>
<p class="title">备注信息：'.$data['log'].'</p>
<p>详情请点击链接：<a href="http://'.$http_host.'/Bulletin/detail/id/'.$data['id'].'" target="_blank">http://'.$http_host.'/Bulletin/detail/id/'.$data['id'].'</a></p>

';

                break;
            case 'SUCCESS':

                $call = $data['nickname'];

                $body = '<p>Dear '.$call.',</p>
<p>['.session('user')['nickname'].'] 通过了您发起的<b>《'.$data['title'].'》</b>公告审批，公告已发布。</p>
<p class="title">备注信息：'.$data['log'].'</p>
<p>详情请点击链接：<a href="http://'.$http_host.'/Bulletin/detail/id/'.$data['id'].'" target="_blank">http://'.$http_host.'/Bulletin/detail/id/'.$data['id'].'</a></p>

';

                break;
            case 'REFUSE':

                $call = $data['nickname'];

                $body = '<p>Dear '.$call.',</p>
<p>['.session('user')['nickname'].'] 拒绝了您发起的<b>《'.$data['title'].'》</b>公告审批。</p>
<p class="title">备注信息：'.$data['log'].'</p>
<p>详情请点击链接：<a href="http://'.$http_host.'/Bulletin/detail/id/'.$data['id'].'" target="_blank">http://'.$http_host.'/Bulletin/detail/id/'.$data['id'].'</a></p>

';

                break;

        }


        # 检查邮件发送结果
        if (empty($cc)) {
            $result = send_Email($address, '', $subject, $body );
            if ($result != 1) {
                $this->ajaxReturn(['flag' => 0, 'msg' => '邮件发送失败']);
            }
        } else {
            $result = send_Email($address, '', $subject, $body, $cc);   # $cc
            if ($result != 1) {
                $this->ajaxReturn(['flag' => 0, 'msg' => '邮件发送失败']);
            }
        }


    }


    # 邮件推送
    public function BulletinEmail( $address, $data, $cc = []){

        $http_host = $_SERVER['HTTP_HOST'];
        extract($data);

        $subject = ' [公告栏] 《' . $data['title'] . '》公告发布';

            $call = 'All';

            $body = '<div><p>Dear '.$call.',</p>
<p>['.$data['manager'].'] 发布了新公告<b>《'.$data['title'].'》</b></p>
<p style="margin-top: 20px;">详情请点击链接：<a href="http://'.$http_host.'/Bulletin/detail/id/'.$data['id'].'" target="_blank">http://'.$http_host.'/Bulletin/detail/id/'.$data['id'].'</a></p>
</div>
';

        # 检查邮件发送结果
        if (empty($cc)) {
            $result = send_Email($address, '', $subject, $body );
            if ($result != 1) {
                $this->ajaxReturn(['flag' => 0, 'msg' => '邮件发送失败']);
            }
        } else {
            $result = send_Email($address, '', $subject, $body, $cc);   # $cc
            if ($result != 1) {
                $this->ajaxReturn(['flag' => 0, 'msg' => '邮件发送失败']);
            }
        }


    }













}

