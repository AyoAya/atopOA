<?php
namespace home\Controller;
use Home\Controller\AuthController;
use Think\Page;
/**
 * 样品管理
 * @author Fulwin
 * 2016-11-1
 */
class SampleController extends AuthController {

    //初始化页面
    public function index(){
        $sample = D('Sample');
        //销售部门权限
        $user = M('User');
        $levelReport = $user->field('level,report,department')->find(session('user')['id']);
        if($levelReport['department']==4){
            if($levelReport['level']<=4){
                $reportList = $user->field('id,account,report')->where('report='.session('user')['id'])->select();
                $in = '';
                if(!empty($reportList)){
                    foreach($reportList as $key=>&$value){
                        $in .= $value['id'].',';
                    }
                    $condition = 's.uid=u.id AND s.uid IN('.session('user')['id'].','.substr($in,0,-1).')';
                }else{
                    $condition = 's.uid=u.id AND s.uid IN('.session('user')['id'].')';
                }
            }else{
                $condition = 's.uid=u.id';
            }
        }else{
            $condition = 's.uid=u.id';
        }
        //搜索
        if( I('get.search') && !empty(I('get.search')) ){
            $condition .= ' AND CONCAT(s.totalorder,s.saleperson) LIKE "%'.I('get.search').'%"';
        }
        $groupList = $sample->table('__USER__ u,__SAMPLE__ s')->where($condition)->group('totalorder')->select();
        $count = count($groupList);
        $page = new Page($count,15);
        $page->setConfig('prev','上一页');
        $page->setConfig('next','下一页');
        $page->setConfig('first','首页');
        $page->setConfig('last','尾页');
        if(C('PAGE_STATUS_INFO')){
            $page->setConfig ( 'theme', '<li><a href="javascript:void(0);">当前%NOW_PAGE%/%TOTAL_PAGE%</a></li>  %FIRST% %UP_PAGE% %LINK_PAGE% %DOWN_PAGE% %END%' );
        }
        $newResult = $sample->table('__USER__ u,__SAMPLE__ s')
                       ->field('s.totalorder,s.createtime,s.uid,u.nickname,u.face')
                       ->where($condition)
                       ->group('s.totalorder')
                       ->order('s.id DESC')
                       ->limit($page->firstRow.','.$page->listRows)
                       ->select();
        foreach($newResult as $key=>&$value){
            $value['total'] = $sample->where('totalorder="'.$value['totalorder'].'"')->count();
            $value['child'] = $sample->relation(true)->where('totalorder="'.$value['totalorder'].'"')->select();
        }
        # print_r($newResult);
        foreach($newResult as $key=>&$value){
            $str = '';
            $errorstr = '';
            foreach($value['child'] as $k=>&$v){
                if( $v['f_status']==1 ){
                    $str .= 'Y';
                }else{
                    $str .= 'N';
                }
                if( $v['s_status']==2 || $v['w_status']==2 || $v['c_status']==2 || $v['y_status']==2 || $v['f_status']==2 ){
                    $errorstr .= 'N';
                }
            }
            if( substr_count($errorstr,'N') == count($value['child']) ){
                $value['color'] = '#d9534f';
            }
            # print_r($errorstr.'<br>');
            if( !isset($value['color']) ){
                if( strpos($str,'N') !== false ){
                    $value['label'] = '#428bca';
                }else{
                    $value['label'] = '#5cb85c';
                }
            }
        }
        # print_r($newResult);
        $show = $page->show();
        if(I('get.p')){
            $this->assign('pagenumber',I('get.p')-1);
        }
        if(I('get.search')){
            $this->assign('search',I('get.search'));
        }
        $this->assign('department',$levelReport);
        $this->assign('empty','<div class="empty-data"><p><i class="icon-info-sign empty-icon"></i></p><p>没有数据</p></div>');
        $this->assign('limitsize',C('LIMIT_SIZE'));
        $this->assign('page',$show);
        # print_r($newResult);
        $this->assign('newResult',$newResult);
        $this->display();
    }
    
    //样品添加页面初始化
    public function sample(){
        $user = D('User');
        //只允许销售部门访问该页面
        $department = $user->field('department')->find(session('user')['id'])['department'];
        if($department!=4){
            echo '<div style="width:170px;height:260px;background:url('.__ROOT__.'/Public/home/img/low_power.png) no-repeat;position:fixed;top:50%;left:50%;margin-left:-73.5px;margin-top:-150px;"><span onclick="history.back();" style="position:absolute;bottom:0;left:50%;width:66px;margin-left:-60px;cursor:pointer;padding:10px 20px;text-align:center;background:#777;color:#fff;border-radius:4px;"> 返回 </span></div>';
            exit;
        }
        $Productrelationships = M('Productrelationships');
        /*$types = array();
        $Productrelationships = M('Productrelationships');
        $ProductrelationshipsTypeList = $Productrelationships->distinct(true)->field('type')->select();
        $firstType = $ProductrelationshipsTypeList[0]['type'];
        $ProductrelationshipsPnList = $Productrelationships->field('p.pn,p.id pid,u.nickname,u.id uid,p.manager')->table('__USER__ u,__PRODUCTRELATIONSHIPS__ p')->where('p.type="'.$firstType.'" AND p.manager=u.id')->select();*/

        $filterArr = array();
        $filterArr['filterType'] = $Productrelationships->distinct(true)->field('type')->order('type')->select();
        $filterArr['filterWavelength'] = $Productrelationships->distinct(true)->field('wavelength')->order('wavelength')->select();
        $filterArr['filterConnector'] = $Productrelationships->distinct(true)->field('connector')->select();
        $filterArr['filterCasetemp'] = $Productrelationships->distinct(true)->field('casetemp')->select();
        $filterArr['filterReach'] = $Productrelationships->distinct(true)->field('reach')->select();
        $filterData = $Productrelationships->field('p.pn,p.id pid,u.nickname,p.wavelength,p.reach,p.connector,p.casetemp,p.type,u.id uid,p.manager')->table('__USER__ u,__PRODUCTRELATIONSHIPS__ p')->where('p.manager=u.id')->select();

        //print_r($filterArr);



        $result = $user->relation(true)->select();
        $this->assign('filter',$filterArr);
        $this->assign('filterdata',$filterData);
        /*$this->assign('typeList',$ProductrelationshipsTypeList);
        $this->assign('pnList',$ProductrelationshipsPnList);
        $this->assign('user',$result);*/
        $this->display();
    }

    //数据筛选
    public function filter(){
        if(!IS_POST) return;
        //print_r($_POST);
        $arr = array();
        $other = array();
        $Productrelationships = M('Productrelationships');
        $condition = 'p.manager=u.id';
        foreach(I('post.data') as $key=>&$value){
            if(!empty($value['value'])){
                if($value['name']=='type'){
                    $other['name'] = $value['name'];
                    $other['value'] = $value['value'];
                }
                $arr[$value['name']] = $value['value'];
                $condition .= ' AND p.'.$value['name'].'="'.$value['value'].'"';
            }
        }
        //print_r($condition);
        /*if($condition == 'p.manager=u.id'){
            $filterArr = array();
            $filterArr['filterType'] = $Productrelationships->distinct(true)->field('type')->order('type')->select();
            $filterArr['filterWavelength'] = $Productrelationships->distinct(true)->field('wavelength')->order('wavelength')->select();
            $filterArr['filterConnector'] = $Productrelationships->distinct(true)->field('connector')->select();
            $filterArr['filterCasetemp'] = $Productrelationships->distinct(true)->field('casetemp')->select();
            $filterArr['filterReach'] = $Productrelationships->distinct(true)->field('reach')->select();
            $filterData = $Productrelationships->field('p.pn,p.id pid,u.nickname,p.wavelength,p.reach,p.connector,p.casetemp,p.type,u.id uid,p.manager')->table('__USER__ u,__PRODUCTRELATIONSHIPS__ p')->where('p.manager=u.id')->select();
            print_r($filterArr);
        }*/
        //print_r($arr);
        $filterData = $Productrelationships->field('p.pn,p.id pid,u.nickname,p.wavelength,p.reach,p.connector,p.casetemp,p.type,u.id uid,p.manager')->table('__USER__ u,__PRODUCTRELATIONSHIPS__ p')->where($condition)->select();
        //print_r($other);

        if(!empty($other) && is_array($other)){
            if($other['name']=='type' && $other['value']=='XFP DWDM'){
                foreach($filterData as $key=>&$value){
                    $value['wave_length'] = $value['wavelength'];
                    $value['wavelength'] = 'CH'.substr($value['pn'],4,2);
                }
            }
        }
        if(!empty($other) && is_array($other)){
            if($other['name']=='type' && $other['value']=='SFP DWDM'){
                foreach($filterData as $key=>&$value){
                    $value['wave_length'] = $value['wavelength'];
                    $value['wavelength'] = 'CH'.substr($value['pn'],4,2);
                }
            }
        }
        if(!empty($other) && is_array($other)){
            if($other['name']=='type' && $other['value']=='SFP+ DWDM'){
                foreach($filterData as $key=>&$value){
                    $value['wave_length'] = $value['wavelength'];
                    $value['wavelength'] = 'CH'.substr($value['pn'],5,2);
                }
            }
        }
        $result['condition'] = $arr;
        $result['data'] = $filterData;
        //print_r($result);
        $this->ajaxReturn($result);
    }

    //样品添加页面上传附件
    public function uploadAttachment(){
        if(!IS_POST) return;
        $info = FileUpload('/SampleAttachment/',0,1);
        if(!$info) return false;
        $newname = $info['Filedata']['savename'];
        $filetype = $info['Filedata']['ext'];
        $filePath = '/Uploads'.$info['Filedata']['savepath'].$info['Filedata']['savename'];
        $arr['type'] = $filetype;
        $arr['NewFileName'] = $newname;
        $arr['FileSavePath'] = $filePath;
        $this->ajaxReturn($arr);
        exit;
    }

    //删除附件
    public function removeFile(){
        if(!IS_POST) return;
        if(!I('post.filepath') || I('post.filepath')=='') return;
        //检测文件路径是否包含中文，如果存在中文则转换编码
        if(preg_match("/[\x7f-\xff]/", '.'.I('post.filepath'))){
            $filePath = iconv('UTF-8','GB2312', '.'.I('post.filepath'));
        }else{
            $filePath = '.'.I('post.filepath');
        }
        if(file_exists($filePath)){
            unlink($filePath);
        }
        if(!file_exists($filePath)){
            echo 'true';
        }else{
            echo 'false';
        }
    }

    //根据产品类型联动产品
    public function changeProductType(){
        if(!IS_POST) return;
        if(empty(I('post.type'))) return;
        $type = I('post.type');
        $Productrelationships = M('Productrelationships');
        $ProductrelationshipsPnList = $Productrelationships->field('p.pn,p.id pid,u.nickname,u.id uid,p.manager')->table('__USER__ u,__PRODUCTRELATIONSHIPS__ p')->where('p.type="'.$type.'" AND p.manager=u.id')->select();
        if($ProductrelationshipsPnList){
            $this->ajaxReturn($ProductrelationshipsPnList);
        }
    }
    
    //样品详情页面
    public function details(){
        //print_r($_SERVER);
        if(!I('get.order'))return;
        $sample = D('Sample');
        $user = M('User');
        //检测当前访问该页面的用户是否属于销售部门
        $department = $user->field('department,level')->find(session('user')['id']);
        if($department['department']==4){
            //比较当前用户职位级别
            if($department['level']<=4){
                $ArrID = array();
                //如果是销售部门，检测当前订单是否属于该销售及该销售的下属
                $reportList = $user->field('id,account,report')->where('report='.session('user')['id'])->select();
                foreach($reportList as $key=>&$value){
                    array_push($ArrID,$value['id']);
                }
                array_push($ArrID,session('user')['id']);
                $condition['order'] = I('get.order');
                $salepersonUid = $sample->where($condition)->select()[0]['uid'];
                //对比数据，如果失败则没有权限访问该页面
                if(!in_array($salepersonUid,$ArrID)){
                    echo '<div style="width:170px;height:260px;background:url('.__ROOT__.'/Public/home/img/low_power.png) no-repeat;position:fixed;top:50%;left:50%;margin-left:-73.5px;margin-top:-150px;"><span onclick="history.back();" style="position:absolute;bottom:0;left:50%;width:66px;margin-left:-60px;cursor:pointer;padding:10px 20px;text-align:center;background:#777;color:#fff;border-radius:4px;"> 返回 </span></div>';
                    exit;
                }
            }
        }
        //将用户数据注入模板
        $users = $user->field('id,account,nickname,report,position,department')->select();
        $notice = M('notice');
        $map['who'] = session('user')['id'];
        $map['sampleorder'] = I('get.order');
        $map['status'] = 0;
        $noticelist = $notice->field('id')->where($map)->select();
        if($noticelist){
            foreach($noticelist as $key=>&$value){
                $saveStatus = $notice->save(array('id'=>$value['id'],'status'=>1,'viewtime'=>time()));
            }
        }
        $map['order'] = I('get.order');
        $sampleID = $sample->field('id')->where($map)->select()[0];
        $result = $sample->relation(true)->find($sampleID['id']);
        //获取指派人姓名
        foreach($result as $key=>&$value){
            if($key=='k_id'){
                $result['k_name'] = $user->field('nickname')->find($value)['nickname'];
            }
            if($key=='f_id'){
                $result['f_name'] = $user->field('nickname')->find($value)['nickname'];
            }
            if($key=='y_id'){
                $result['y_name'] = $user->field('nickname')->find($value)['nickname'];
            }
            if($key=='c_id'){
                $result['c_name'] = $user->field('nickname')->find($value)['nickname'];
            }
            if($key=='w_id'){
                $result['w_name'] = $user->field('nickname')->find($value)['nickname'];
            }
            if($key=='aid'){
                $result['a_name'] = $user->field('nickname')->find($value)['nickname'];
            }
            if($key=='attachment'){
                if(!empty($value)){
                    $value = array('filepath'=>$value,'filename'=>getBasename($value),'type'=>getExtension($value));
                }
            }
            //获取备注数据并且保留文本格式（换行）
            if(in_array($key,array('s_comment','w_comment','c_comment','y_comment','f_comment','k_comment'))){
                $value = unserialize($value);
                if(isset($value['save']) && !empty($value['save']) && is_array($value['save'])){
                    $this->assign('lastSave',$value['save'][count($value['save'])-1]);
                    foreach($value['save'] as $k=>&$v){
                        $v['message'] = replaceEnterWithBr($v['message']);
                    }
                    $value['save'] = array_reverse($value['save']);
                }
                if(isset($value['success']) && !empty($value['success']) && is_array($value['success'])){
                    if(empty($value['success'][0]['message'])){
                        $value['success'] = '';
                    }else{
                        $this->assign('lastSuccess',$value['success'][count($value['success'])-1]);
                        $value['success'][0]['message'] = replaceEnterWithBr($value['success'][0]['message']);
                    }
                }
                if(isset($value['error']) && !empty($value['error']) && is_array($value['error'])){
                    if(empty($value['error'][0]['message'])){
                        $value['error'] = '';
                    }else{
                        $this->assign('lastError', $value['error'][count($value['error']) - 1]);
                        $value['error'][0]['message'] = replaceEnterWithBr($value['error'][0]['message']);
                    }
                }
            }
        }
        if($result['s_status']=='' || $result['s_status']==3 && $result['w_status']=='' && $result['c_status']=='' && $result['y_status']=='' && $result['f_status']=='' && $result['k_status']==''){
            $result['state'] = '审核中';
            $result['color'] = 0;
        }else if($result['s_status']==2 && $result['w_status']=='' && $result['c_status']=='' && $result['y_status']=='' && $result['f_status']=='' && $result['k_status']==''){
            $result['state'] = '审核未通过';
            $result['color'] = 2;
        }else if($result['s_status']==1 && $result['w_status']=='' || $result['w_status']==3 && $result['c_status']=='' && $result['y_status']=='' && $result['f_status']=='' && $result['k_status']==''){
            $result['state'] = '物料准备中';
            $result['color'] = 0;
        }else if($result['s_status']==1 && $result['w_status']==2 && $result['c_status']=='' && $result['y_status']=='' && $result['f_status']=='' && $result['k_status']==''){
            $result['state'] = '物料准备未完成';
            $result['color'] = 2;
        }else if($result['s_status']==1 && $result['w_status']==1 && $result['c_status']=='' || $result['c_status']==3 && $result['y_status']=='' && $result['f_status']=='' && $result['k_status']==''){
            $result['state'] = '样品制作中';
            $result['color'] = 0;
        }else if($result['s_status']==1 && $result['w_status']==1 && $result['c_status']==2 && $result['y_status']=='' && $result['f_status']=='' && $result['k_status']==''){
            $result['state'] = '样品制作未完成';
            $result['color'] = 2;
        }else if($result['s_status']==1 && $result['w_status']==1 && $result['c_status']==1 && $result['y_status']=='' || $result['y_status']==3 && $result['f_status']=='' && $result['k_status']==''){
            $result['state'] = '样品测试中';
            $result['color'] = 0;
        }else if($result['s_status']==1 && $result['w_status']==1 && $result['c_status']==1 && $result['y_status']==2 && $result['f_status']=='' && $result['k_status']==''){
            $result['state'] = '样品测试未通过';
            $result['color'] = 2;
        }else if($result['s_status']==1 && $result['w_status']==1 && $result['c_status']==1 && $result['y_status']==1 && $result['f_status']=='' || $result['f_status']==3 && $result['k_status']==''){
            $result['state'] = '等待发货';
            $result['color'] = 0;
        }else if($result['s_status']==1 && $result['w_status']==1 && $result['c_status']==1 && $result['y_status']==1 && $result['f_status']==2 && $result['k_status']==''){
            $result['state'] = '发货失败';
            $result['color'] = 2;
        }else if($result['s_status']==1 && $result['w_status']==1 && $result['c_status']==1 && $result['y_status']==1 && $result['f_status']==1 && $result['k_status']=='' || $result['k_status']==3){
            $result['state'] = '待反馈';
            $result['color'] = 0;
        }else if($result['s_status']==1 && $result['w_status']==1 && $result['c_status']==1 && $result['y_status']==1 && $result['f_status']==1 && $result['k_status']==2 || $result['k_status']==1){
            $result['state'] = '已反馈';
            $result['color'] = 3;
        }else{
            $result['state'] = 'error';
            $result['color'] = 2;
        }
        $lastArr = array();
        //将最后一条日志注入模板，如果一条日志都没有则注入空
        foreach($result as $key=>&$value){
            if(in_array($key,array('s_comment','w_comment','c_comment','y_comment','f_comment','k_comment'))){
                if(is_array($value) && !empty($value)){
                    if( isset($value['success']) && !empty($value['success']) && !isset($value['error']) && !isset($value['save']) ){

                        $lastLogStr['message'] = $value['success'][0]['message'];
                        $lastLogStr['time'] = $value['success'][0]['time'];
                        $lastLogStr['manager'] = $value['success'][0]['manager'];

                    }elseif( isset($value['success']) && !empty($value['success']) && !isset($value['error']) && isset($value['save']) ){

                        $lastLogStr['message'] = $value['success'][0]['message'];
                        $lastLogStr['time'] = $value['success'][0]['time'];
                        $lastLogStr['manager'] = $value['success'][0]['manager'];

                    }elseif( isset($value['success']) && empty($value['success']) && !isset($value['error']) && isset($value['save']) ){

                        $lastLogStr['message'] = $value['save'][0]['message'];
                        $lastLogStr['time'] = $value['save'][0]['time'];
                        $lastLogStr['manager'] = $value['save'][0]['manager'];

                    }elseif( !isset($value['success']) && isset($value['error']) && !empty($value['error']) && !isset($value['save']) ){

                        $lastLogStr['message'] = $value['error'][0]['message'];
                        $lastLogStr['time'] = $value['error'][0]['time'];
                        $lastLogStr['manager'] = $value['error'][0]['manager'];

                    }elseif( !isset($value['success']) && isset($value['error']) && !empty($value['error']) && isset($value['save']) ){

                        $lastLogStr['message'] = $value['error'][0]['message'];
                        $lastLogStr['time'] = $value['error'][0]['time'];
                        $lastLogStr['manager'] = $value['error'][0]['manager'];

                    }elseif( !isset($value['success']) && isset($value['error']) && empty($value['error']) && isset($value['save']) ){

                        $lastLogStr['message'] = $value['save'][0]['message'];
                        $lastLogStr['time'] = $value['save'][0]['time'];
                        $lastLogStr['manager'] = $value['save'][0]['manager'];

                    }elseif( !isset($value['success']) && !isset($value['error']) && isset($value['save']) ){

                        $lastLogStr['message'] = $value['save'][0]['message'];
                        $lastLogStr['time'] = $value['save'][0]['time'];
                        $lastLogStr['manager'] = $value['save'][0]['manager'];

                    }
                    array_push($lastArr,$lastLogStr);
                }
            }
        }
        $lastLogStr = array();
        $lastArray = $lastArr[count($lastArr)-1];
        /*if( isset($lastArray['success']) && !empty($lastArray['success']) && !isset($lastArray['error']) && !isset($lastArray['save']) ){
            $lastLogStr['message'] = $lastArray['success'][0]['message'];
            $lastLogStr['time'] = $lastArray['success'][0]['time'];
            $lastLogStr['manager'] = $lastArray['success'][0]['manager'];
        }elseif( isset($lastArray['success']) && !empty($lastArray['success']) && !isset($lastArray['error']) && isset($lastArray['save']) ){
            $lastLogStr['message'] = $lastArray['success'][0]['message'];
            $lastLogStr['time'] = $lastArray['success'][0]['time'];
            $lastLogStr['manager'] = $lastArray['success'][0]['manager'];
        }elseif( isset($lastArray['success']) && empty($lastArray['success']) && !isset($lastArray['error']) && isset($lastArray['save']) ){
            $lastLogStr['message'] = $lastArray['save'][count($lastArray['save'])-1]['message'];
            $lastLogStr['time'] = $lastArray['save'][count($lastArray['save'])-1]['time'];
            $lastLogStr['manager'] = $lastArray['save'][count($lastArray['save'])-1]['manager'];
        }elseif( !isset($lastArray['success']) && isset($lastArray['error']) && !empty($lastArray['error']) && !isset($lastArray['save']) ){
            $lastLogStr['message'] = $lastArray['success'][0]['message'];
            $lastLogStr['time'] = $lastArray['success'][0]['time'];
            $lastLogStr['manager'] = $lastArray['success'][0]['manager'];
        }elseif( !isset($lastArray['success']) && isset($lastArray['error']) && !empty($lastArray['error']) && isset($lastArray['save']) ){
            $lastLogStr['message'] = $lastArray['error'][0]['message'];
            $lastLogStr['time'] = $lastArray['error'][0]['time'];
            $lastLogStr['manager'] = $lastArray['error'][0]['manager'];
        }elseif( !isset($lastArray['success']) && isset($lastArray['error']) && empty($lastArray['error']) && isset($lastArray['save']) ){
            $lastLogStr['message'] = $lastArray['save'][count($lastArray['save'])-1]['message'];
            $lastLogStr['time'] = $lastArray['save'][count($lastArray['save'])-1]['time'];
            $lastLogStr['manager'] = $lastArray['save'][count($lastArray['save'])-1]['manager'];
        }elseif( !isset($lastArray['success']) && !isset($lastArray['error']) && isset($lastArray['save']) ){
            $lastLogStr['message'] = $lastArray['save'][count($lastArray['save'])-1]['message'];
            $lastLogStr['time'] = $lastArray['save'][count($lastArray['save'])-1]['time'];
            $lastLogStr['manager'] = $lastArray['save'][count($lastArray['save'])-1]['manager'];
        }else{
            $lastLogStr = '';
        }*/
        if(!empty($result)){
            if(strtotime(date('Y-m-d',$result['a_date'])) > strtotime($result['d_date'])){
                $this->assign('deliveryDateError','延期');
            }else{
                $this->assign('deliveryDateSuccess','正常');
            }
        }
        # print_r($lastArray);
        $this->assign('lastLog',$lastArray);
        //将附件注入模板
        $sampleAttachment = M('Sampleattachment');
        $attachementList = $sampleAttachment->where('sample_order="'.$result['totalorder'].'"')->select()[0];
        $attachment = json_decode($attachementList['attachment'],true)['attachment'];
        $this->assign('noAttachment','<div class="noAttachment"><h1 class="icon-info-sign icon-no-attachment"></h1><p class="attachment-first-text">没有附件</p><p>No Attachment</p></div>');
        $this->assign('attachment',$attachment);
        $this->assign('users',$users);
        $this->assign('sample',$result);
        $this->display();
    }

    //样品单总览
    public function overview(){
        if(!empty(I('get.overview')) && !I('get.overview')) return;
        $overview = I('get.overview');
        $sample = D('Sample');
        $user = M('User');
        //检测当前访问该页面的用户是否属于销售部门
        $department = $user->field('department,level')->find(session('user')['id']);
        if($department['department']==4){
            //比较当前用户职位级别
            if($department['level']<=4){
                $ArrID = array();
                //如果是销售部门，检测当前订单是否属于该销售及该销售的下属
                $reportList = $user->field('id,account,report')->where('report='.session('user')['id'])->select();
                foreach($reportList as $key=>&$value){
                    array_push($ArrID,$value['id']);
                }
                array_push($ArrID,session('user')['id']);
                $condition['totalorder'] = I('get.overview');
                $ArrUid = $sample->where($condition)->select();
                if(!empty($ArrUid)){
                    $salepersonUid = $ArrUid[0]['uid'];
                }
                //对比数据，如果失败则没有权限访问该页面
                if(!in_array($salepersonUid,$ArrID)){
                    echo '<div style="width:170px;height:260px;background:url('.__ROOT__.'/Public/home/img/low_power.png) no-repeat;position:fixed;top:50%;left:50%;margin-left:-73.5px;margin-top:-150px;"><span onclick="history.back();" style="position:absolute;bottom:0;left:50%;width:66px;margin-left:-60px;cursor:pointer;padding:10px 20px;text-align:center;background:#777;color:#fff;border-radius:4px;"> 返回 </span></div>';
                    exit;
                }
            }
        }
        $overviewData = $sample->relation(true)->where('totalorder="'.$overview.'"')->select();

        //print_r($overviewData);
        foreach($overviewData as $key=>&$value){
            //echo $value['aid'];
            /*$value['a_name'] = $user->field('nickname')->find($value['aid'])['nickname'];
            $value['w_name'] = $user->field('nickname')->find($value['w_id'])['nickname'];
            $value['c_name'] = $user->field('nickname')->find($value['c_id'])['nickname'];
            $value['y_name'] = $user->field('nickname')->find($value['y_id'])['nickname'];
            $value['f_name'] = $user->field('nickname')->find($value['f_id'])['nickname'];
            $value['k_name'] = $user->field('nickname')->find($value['uid'])['nickname'];*/
            /*if(in_array($key,array('s_comment','c_comment','y_comment'))){
                print_r($value);
                $overviewData[$key] = replaceEnterWithBr($value);
            }*/
            //获取备注数据并且保留文本格式（换行）
            foreach($value as $k=>&$v){
                if($k=='k_id'){
                    $value['k_name'] = $user->field('nickname')->find($v)['nickname'];
                }
                if($k=='f_id'){
                    $value['f_name'] = $user->field('nickname')->find($v)['nickname'];
                }
                if($k=='y_id'){
                    $value['y_name'] = $user->field('nickname')->find($v)['nickname'];
                }
                if($k=='c_id'){
                    $value['c_name'] = $user->field('nickname')->find($v)['nickname'];
                }
                if($k=='w_id'){
                    $value['w_name'] = $user->field('nickname')->find($v)['nickname'];
                }
                if($k=='aid'){
                    $value['a_name'] = $user->field('nickname')->find($v)['nickname'];
                }
                if(in_array($k,array('s_comment','w_comment','c_comment','y_comment','f_comment','k_comment'))){
                    $v = unserialize($v);
                    if(isset($v['save']) && !empty($v['save']) && is_array($v['save'])){
                        $this->assign('lastSave',$v['save'][count($value['save'])-1]);
                        foreach($v['save'] as $a=>&$b){
                            $b['message'] = replaceEnterWithBr($b['message']);
                        }
                        $v['save'] = array_reverse($b['save']);
                    }
                    if(isset($v['success']) && !empty($v['success']) && is_array($v['success'])){
                        if(empty($v['success'][0]['message'])){
                            $v['success'] = '';
                        }else{
                            $this->assign('lastSuccess',$v['success'][count($v['success'])-1]);
                            $v['success'][0]['message'] = replaceEnterWithBr($v['success'][0]['message']);
                        }
                    }
                    if(isset($v['error']) && !empty($v['error']) && is_array($v['error'])){
                        if(empty($v['error'][0]['message'])){
                            $v['error'] = '';
                        }else{
                            $this->assign('lastError', $v['error'][count($v['error']) - 1]);
                            $v['error'][0]['message'] = replaceEnterWithBr($v['error'][0]['message']);
                        }
                    }
                }
            }
        }
        //print_r($overviewData);
        foreach($overviewData as $key=>&$value){
                if($value['s_status']=='' || $value['s_status']==3 && $value['w_status']=='' && $value['c_status']=='' && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                    $value['state'] = '审核中';
                    $value['color'] = 0;
                }else if($value['s_status']==2 && $value['w_status']=='' && $value['c_status']=='' && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                    $value['state'] = '审核未通过';
                    $value['color'] = 2;
                }else if($value['s_status']==1 && $value['w_status']=='' || $value['w_status']==3 && $value['c_status']=='' && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                    $value['state'] = '物料准备中';
                    $value['color'] = 0;
                }else if($value['s_status']==1 && $value['w_status']==2 && $value['c_status']=='' && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                    $value['state'] = '物料准备未完成';
                    $value['color'] = 2;
                }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']=='' || $value['c_status']==3 && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                    $value['state'] = '样品制作中';
                    $value['color'] = 0;
                }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==2 && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                    $value['state'] = '样品制作未完成';
                    $value['color'] = 2;
                }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']=='' || $value['y_status']==3 && $value['f_status']=='' && $value['k_status']==''){
                    $value['state'] = '样品测试中';
                    $value['color'] = 0;
                }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==2 && $value['f_status']=='' && $value['k_status']==''){
                    $value['state'] = '样品测试未通过';
                    $value['color'] = 2;
                }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==1 && $value['f_status']=='' || $value['f_status']==3 && $value['k_status']==''){
                    $value['state'] = '等待发货';
                    $value['color'] = 0;
                }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==1 && $value['f_status']==2 && $value['k_status']==''){
                    $value['state'] = '发货失败';
                    $value['color'] = 2;
                }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==1 &&  $value['f_status']==1 && $value['k_status']=='' || $value['k_status']==3){
                    $value['state'] = '待反馈';
                    $value['color'] = 0;
                }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==1 && $value['f_status']==1 && $value['k_status']==2 || $value['k_status']==1){
                    $value['state'] = '已反馈';
                    $value['color'] = 3;
                }else{
                    $result['state'] = 'error';
                    $result['color'] = 2;
                }
        }
        //print_r($overviewData);
        //将附件注入模板
        $sampleAttachment = M('Sampleattachment');
        $attachementList = $sampleAttachment->where('sample_order="'.$overview.'"')->select()[0];
        $attachment = json_decode($attachementList['attachment'],true)['attachment'];
        $this->assign('noAttachment','<div class="noAttachment"><h1 class="icon-info-sign icon-no-attachment"></h1><p class="attachment-first-text">没有附件</p><p>No Attachment</p></div>');
        $this->assign('attachment',$attachment);
        $this->assign('totalorder',$overview);
        $this->assign('overview',$overviewData);
        $this->assign('sample',$overviewData);
        //print_r($overviewData);
        $this->display();
    }


    public function tmpSampleData(){
        $first_day = getCurMonthFirstDay('2017-1');
        $last_day = getCurMonthLastDay('2017-1');

        $sample_model = D('Sample');

        $map['createtime'] = array(array('EGT',strtotime($first_day)),array('ELT',strtotime($last_day)),'AND');

        $sample_result = $sample_model->relation(true)->where( $map )->order('createtime ASC')->select();

        foreach($sample_result as $key=>&$value){
            if($value['s_status']=='' || $value['s_status']==3 && $value['w_status']=='' && $value['c_status']=='' && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '审核中';
                $value['color'] = 0;
            }else if($value['s_status']==2 && $value['w_status']=='' && $value['c_status']=='' && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '审核未通过';
                $value['color'] = 2;
            }else if($value['s_status']==1 && $value['w_status']=='' || $value['w_status']==3 && $value['c_status']=='' && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '物料准备中';
                $value['color'] = 0;
            }else if($value['s_status']==1 && $value['w_status']==2 && $value['c_status']=='' && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '物料准备未完成';
                $value['color'] = 2;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']=='' || $value['c_status']==3 && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '样品制作中';
                $value['color'] = 0;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==2 && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '样品制作未完成';
                $value['color'] = 2;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']=='' || $value['y_status']==3 && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '样品测试中';
                $value['color'] = 0;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==2 && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '样品测试未通过';
                $value['color'] = 2;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==1 && $value['f_status']=='' || $value['f_status']==3 && $value['k_status']==''){
                $value['state'] = '等待发货';
                $value['color'] = 0;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==1 && $value['f_status']==2 && $value['k_status']==''){
                $value['state'] = '发货失败';
                $value['color'] = 2;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==1 &&  $value['f_status']==1 && $value['k_status']=='' || $value['k_status']==3){
                $value['state'] = '待反馈';
                $value['color'] = 0;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==1 && $value['f_status']==1 && $value['k_status']==2 || $value['k_status']==1){
                $value['state'] = '已反馈';
                $value['color'] = 3;
            }else{
                $result['state'] = 'error';
                $result['color'] = 2;
            }
        }

        //echo $sample_model->getLastSql();
        //print_r($sample_result);
        $this->assign('sampledata',$sample_result);
        $this->display();

    }




    //查看所有日志
    public function log(){
        if( !I('get.order') || empty(I('get.order')) ) return;
        $sample = D('Sample');
        $user = M('User');
        $users = $user->field('id,account,nickname')->select();
        $map['order'] = I('get.order');
        $sampleID = $sample->field('id')->where($map)->select()[0];
        $result = $sample->relation(true)->find($sampleID['id']);
        //获取指派人姓名
        foreach($result as $key=>&$value){
            /*if($key=='y_id'){
                $result['y_name'] = $user->field('nickname')->find($value)['nickname'];
            }
            if($key=='c_id'){
                $result['c_name'] = $user->field('nickname')->find($value)['nickname'];
            }
            if($key=='w_id'){
                $result['w_name'] = $user->field('nickname')->find($value)['nickname'];
            }
            if($key=='aid'){
                $result['a_name'] = $user->field('nickname')->find($value)['nickname'];
            }
            if($key=='attachment'){
                if(!empty($value)){
                    $value = array('filepath'=>$value,'filename'=>getBasename($value),'type'=>getExtension($value));
                }
            }*/
            //获取备注数据并且保留文本格式（换行）
            if(in_array($key,array('s_comment','w_comment','c_comment','y_comment','f_comment','k_comment'))){
                $value = unserialize($value);
                if(isset($value['save']) && !empty($value['save']) && is_array($value['save'])){
                    foreach($value['save'] as $k=>&$v){
                        $v['message'] = replaceEnterWithBr($v['message']);
                    }
                }
                if(isset($value['success']) && !empty($value['success']) && is_array($value['success'])){
                    $value['success'][0]['message'] = replaceEnterWithBr($value['success'][0]['message']);
                }
                if(isset($value['error']) && !empty($value['error']) && is_array($value['error'])){
                    $value['error'][0]['message'] = replaceEnterWithBr($value['error'][0]['message']);
                }
            }
        }
        //print_r($result);
        $this->assign('noData','<div class="noData"><h1 class="icon-info-sign icon-no-data"></h1><p class="data-first-text">没有数据</p><p>No Data</p></div>');
        $this->assign('allLog',$result);
        $this->assign('order',I('get.order'));
        $this->display();
    }

    //物流状态
    public function logistics(){
        if( !I('get.order') || empty(I('get.order'))) return;
        $sample = D('Sample');
        $map['order'] = I('get.order');
        $result = $sample->relation(true)->where($map)->select()[0];
        //请求快递100物流查询信息
        $url = 'http://www.kuaidi100.com/query?type='.$result['logistics'].'&postid='.$result['awb'];
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $file_contents = curl_exec($ch);
        curl_close($ch);
        //将请求数据转换为数据并注入模板
        $logisticsData = json_decode($file_contents,true);
        $this->assign('logistics',$logisticsData);
        $this->assign('order',I('get.order'));
        $this->assign('sample',$result);
        $this->display();
    }

    //下载附件
    public function downloadAttachment(){
        //检测文件路径是否包含中文，如果存在中文则转换编码
        if(preg_match("/[\x7f-\xff]/", '.'.I('post.filepath'))){
            $filePath = iconv('UTF-8','GB2312', '.'.I('post.filepath'));
        }else{
            $filePath = '.'.I('post.filepath');
        }
        $fileName = getBasename(I('post.filepath'));
        //检测文件名是否包含中文，如果包含中文则转换编码
        if (preg_match("/[\x7f-\xff]/", $fileName)) {
            $fileName = urlencode($fileName);
        }
        //实例化thinkphp下载类
        ob_end_clean();
        $http = new \Org\Net\Http;
        $http->download($filePath,$fileName);
    }

    //验证样品单号唯一性
    public function checkTotalOrder(){
        if(!IS_POST) return;
        $totalorder = I('totalorder');
        $sample = M('Sample');
        $map['totalorder'] = $totalorder;
        $uniqueResult = $sample->where($map)->select();
        if($uniqueResult){
            $this->ajaxReturn(array('flag'=>0,'msg'=>'样品单号已存在'));
            exit;
        }
    }
    
    //下单
    public function add(){
        if(!IS_POST) return;
        $jsonStr = I('post.allorder','',false);
        $attachmentJsonStr = I('post.attachment','',false);
        $sample = M('Sample');
        $allorder = json_decode($jsonStr,true);

        $notice = M('Notice');
        if(!empty($allorder) && is_array($allorder)){
            $totalOrder = $allorder['allOrder'][0]['totalorder'];
            foreach($allorder as $key=>&$value){
                foreach($value as $k=>&$v){
                    $v['comment'] = preg_replace('/\s+/','&nbsp;',$v['comment']);
                    $v['comment'] = preg_replace('/(<br\/>)+/','<br/>',$v['comment']);
                    $v['order'] = generateOrderNumber();
                    $v['createtime'] = time();
                    $v['uid'] = session('user')['id'];
                    $v['saleperson'] = session('user')['nickname'];
                    //获取产品经理email
                    $user = M('User');
                    $addressNickname = $user->field('email,nickname,level')->find($v['aid']);
                    //获取总监以及测试工程师的邮件作为抄送人
                    $emails = $this->getDirectorAndTestingEmail($user,$v['aid'],$addressNickname['level']);
                    //准备邮件发送数据
                    $subject = '产品[ '.$v['product'].' ]有新的样品订单[ '.$totalOrder.' ]，请您关注！';
                    if(empty($v['comment'])){
                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>有新的样品订单需要您审核，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$v['order'].'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$v['order'].'</a><br><br>样品订单：'.$totalOrder.'<br>产品型号：'.$v['product'].'</p>';
                    }else{
                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>有新的样品订单需要您审核，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$v['order'].'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$v['order'].'</a><br><br>样品订单：'.$totalOrder.'<br>产品型号：'.$v['product'].'<br>备注信息：'.$v['comment'].'</p>';
                    }
                    $address = $addressNickname['email'];
                    $nickname = $addressNickname['nickname'];
                    # 似乎抄送人的邮件一致的话取唯一
                    $send_result = send_Email($address,$nickname,$subject,$body,$emails);
                    //发送邮件
                    if($send_result!=1){
                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                        exit;
                    }
                    //如果有一条数据没有写入成功则返回错误
                    if(!$sample->add($v)){
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                    //将所有提示消息写入数据库
                    $arr['who'] = $v['aid'];
                    if(I('post.operation') && I('post.operation')=='audit'){
                        $arr['message'] = '产品[ '.$v['product'].' ]有新的样品订单[ '.$totalOrder.' ]，请您关注！';
                    }
                    $arr['link'] = '/sampleDetails/'.$v['order'];
                    $arr['pushtime'] = time();
                    $arr['sampleorder'] = $v['order'];
                    $noticeID = $notice->add($arr);
                    if(!$noticeID){
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }
        }
        //将附件数据存入数据库
        if(!empty($attachmentJsonStr)){
            $SampleAttachment = M('Sampleattachment');
            $attachmentData['attachment'] = $attachmentJsonStr;
            $attachmentData['sample_order'] = $totalOrder;
            $attachmentID = $SampleAttachment->add($attachmentData);
            if(!$attachmentID){
                $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                exit;
            }
        }
        $this->ajaxReturn(array('flag'=>1,'msg'=>'下单成功','url'=>$_SERVER['HTTP_HOST'].'/sampleOverview/'.$totalOrder));
        exit;
    }

    //获取部门总监及测试工程师的邮箱
    private function getDirectorAndTestingEmail(&$user,&$aid,&$level){
        $emails = array();
        //获取到部门总监(cade独享)
        if( $level != 5 ){
            $director = $user->field('report')->find($aid)['report'];
            //获取到部门总监邮件
            $directorEmail = $user->field('email')->find($director)['email'];
            array_push($emails,$directorEmail);
        }else{
            $directorEmail = $user->field('email')->find($aid)['email'];
        }
        //获取到测试工程师(刘燕、周树锟)的邮件
        $testPsersons = $user->field('email')->where('id=70')->select();
        foreach($testPsersons as $key=>&$value){
            array_push($emails,$value['email']);
        }
        return $emails;
    }

    //审核
    public function audit(){
        if(!IS_AJAX)return;
        //收集用户提交的数据
        $_assoc = I('post.s_assoc');
        $_status = I('post.s_status');
        $_e_date = I('post.e_date');
        $_comment = I('post.s_comment');
        $_wid = I('post.wid');
        $_uid = I('post.uid');
        $_total_order = I('post.total_order');
        $_product = I('post.product');
        $_order = I('post.order');
        //准备写入数据库的数据
        $comment = array();
        $samples = M('SampleS');
        $arr['s_assoc'] = $_assoc;
        $arr['s_status'] = $_status;
        $arr['s_comment'] = $_comment;
        $arr['s_time'] = time();
        $arr['wid'] = $_wid;
        //获取到要求交期
        $sample = M('Sample');
        $d_date = $sample->field('d_date')->find($_assoc)['d_date'];
        if($d_date==$_e_date){
            $_change = true;
        }else{
            $_change = false;
        }
        //修改预计时间
        $saveEdate = $sample->save(array('id'=>$_assoc,'e_date'=>$_e_date));
        //只在状态为通过或者未通过的情况下执行
        if($_status && in_array($_status,array(1,2))){
            //查看是否存在属于该产品型号的步骤记录
            $sampleIsset = $samples->where('s_assoc="'.$_assoc.'"')->select()[0];
            //如果存在步骤记录则数据追加，反之则新增
            if($sampleIsset){
                if($_status==1){
                    //如果存在更新日志，则将状态追加到备注信息栏
                    $unserialize = unserialize($sampleIsset['s_comment']);
                    $saveComment = $unserialize;
                    $saveComment['success'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    //准备数据，更新操作时间
                    $saveData['id'] = $sampleIsset['id'];
                    $saveData['s_comment'] = serialize($saveComment);
                    $saveData['s_status'] = $_status;
                    $saveData['s_time'] = time();
                    $saveData['wid'] = $_wid;
                    $id = $samples->save($saveData);
                    //将更新日志添加至通知表
                    if($id){
                        $notice = M('Notice');
                        $noticeArrUser = array();
                        array_push($noticeArrUser,$_wid,$_uid);
                        foreach($noticeArrUser as $key=>&$value){
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            $arr['who'] = $value;
                            if($_change){
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您准备物料';
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您准备物料';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您准备物料，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您准备物料，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核已通过';
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核已通过';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核已通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核已通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }else{
                                if($value==$_wid){
                                    $arr['message'] = '样品订单：[ '.$_total_order.' ]，产品：[ '.$_product.' ]，需要您准备物料';
                                    //准备邮件发送数据
                                    $subject = '样品订单：[ '.$_total_order.' ]，产品：[ '.$_product.' ]，需要您准备物料';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您准备物料，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您准备物料，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单：[ ' . $_total_order . ' ]，产品：[ ' . $_product . ' ]，审核已通过并修改了交期时间';
                                    //准备邮件发送数据
                                    $subject = '样品订单：[ ' . $_total_order . ' ]，产品：[ ' . $_product . ' ]，审核已通过并修改了交期时间';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核已通过并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核已通过并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '<br>预计交期：' . $_e_date . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            $noticeIID = $notice->add($arr);
                        }
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }else{
                    //如果审核未通过也将信息通知到销售
                    $unserialize = unserialize($sampleIsset['s_comment']);
                    $saveComment = $unserialize;
                    $saveComment['error'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $saveData['id'] = $sampleIsset['id'];
                    $saveData['s_comment'] = serialize($saveComment);
                    $saveData['s_status'] = $_status;
                    $saveData['s_time'] = time();
                    $saveData['wid'] = $_wid;
                    $id = $samples->save($saveData);
                    //将更新日志添加至通知表
                    if($id){
                        $notice = M('Notice');
                        $addressNickname = M('User')->field('email,nickname')->find($_uid);
                        $arr['who'] = $_uid;
                        $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核未通过';
                        $arr['link'] = '/sampleDetails/' . $_order;
                        $arr['pushtime'] = time();
                        $arr['sampleorder'] = $_order;
                        $noticeIID = $notice->add($arr);
                        //准备邮件发送数据
                        $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核未通过';
                        if (empty($_comment)) {
                            $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核未通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                        } else {
                            $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核未通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '</p>';
                        }
                        $address = $addressNickname['email'];
                        $nickname = $addressNickname['nickname'];
                        //发送邮件
                        $send_result = send_Email($address, $nickname, $subject, $body);
                        if ($send_result != 1) {
                            $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                            exit;
                        }
                        if ($noticeIID) {
                            $this->ajaxReturn(array('flag' => 1, 'msg' => '成功'));
                            exit;
                        } else {
                            $this->ajaxReturn(array('flag' => 0, 'msg' => '错误'));
                            exit;
                        }
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }else{
                //如果不存在步骤记录是执行
                if(I('post.s_status')==1){
                    $comment['success'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $arrSave['s_assoc'] = $_assoc;
                    $arrSave['s_status'] = $_status;
                    $arrSave['s_comment'] = serialize($comment);
                    $arrSave['s_time'] = time();
                    $arrSave['wid'] = $_wid;
                    $id = $samples->add($arrSave);
                    //将更新日志添加至通知表
                    if($id){
                        $notice = M('Notice');
                        $noticeArrUser = array();
                        array_push($noticeArrUser,$_wid,$_uid);
                        foreach($noticeArrUser as $key=>&$value) {
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //print_r($addressNickname);
                            $arr['who'] = $value;
                            if ($_change) {
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，需要您准备物料';
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，需要您准备物料';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单需要您准备物料，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单需要您准备物料，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核已通过';
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核已通过';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核已通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核已通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            } else {
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，需要您准备物料';
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，需要您准备物料';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单需要您准备物料，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单需要您准备物料，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '<br>预计交期：' . $_e_date . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核已通过并修改了交期时间';
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核已通过并修改了交期时间';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核已通过并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核已通过并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '<br>预计交期：' . $_e_date . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }
                            $arr['link'] = '/sampleDetails/' . $_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            $noticeIID = $notice->add($arr);
                            if(!$noticeIID){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }else{
                    $comment['error'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $arrSave['s_assoc'] = $_assoc;
                    $arrSave['s_status'] = $_status;
                    $arrSave['s_comment'] = serialize($comment);
                    $arrSave['s_time'] = time();
                    $arrSave['wid'] = $_wid;
                    $id = $samples->add($arrSave);
                    //将更新日志添加至通知表
                    if($id){
                        $notice = M('Notice');
                        $addressNickname = M('User')->field('email,nickname')->find($_uid);
                        $arr['who'] = $_uid;
                        $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核未通过';
                        $arr['link'] = '/sampleDetails/' . $_order;
                        $arr['pushtime'] = time();
                        $arr['sampleorder'] = $_order;
                        $noticeIID = $notice->add($arr);
                        //准备邮件发送数据
                        $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核未通过';
                        if (empty($_comment)) {
                            $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核未通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                        } else {
                            $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核未通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '</p>';
                        }
                        $address = $addressNickname['email'];
                        $nickname = $addressNickname['nickname'];
                        //发送邮件
                        $send_result = send_Email($address, $nickname, $subject, $body);
                        if ($send_result != 1) {
                            $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                            exit;
                        }
                        if ($noticeIID) {
                            $this->ajaxReturn(array('flag' => 1, 'msg' => '成功'));
                            exit;
                        } else {
                            $this->ajaxReturn(array('flag' => 0, 'msg' => '错误'));
                            exit;
                        }
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }
        }else{
            //查看数据库是否存在该样品的审核信息
            //如果存在则新增备注信息，否则更新备注信息
            //无论是更新还是新增都将通知销售及产品经理
            //为保留备注兼容三种状态，将各状态备注信息作为数组数序列化后存入数据库
            $sampleIsset = $samples->where('s_assoc="'.$_assoc.'"')->select()[0];
            if($sampleIsset){
                $unserialize = unserialize($sampleIsset['s_comment']);
                $saveComment = $unserialize;
                $saveComment['save'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                //print_r($saveComment);
                $saveData['id'] = $sampleIsset['id'];
                $saveData['s_comment'] = serialize($saveComment);
                $id = $samples->save($saveData);
                //将更新日志添加至通知表
                if($id){
                    $notice = M('Notice');
                    $addressNickname = M('User')->field('email,nickname')->find($_uid);
                    $arr['who'] = $_uid;
                    if ($_change) {
                        $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核日志已被' . session('user')['nickname'] . '更新';
                        //准备邮件发送数据
                        $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核日志已被' . session('user')['nickname'] . '更新';
                        if (empty($_comment)) {
                            $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核日志已被' . session('user')['nickname'] . '更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                        } else {
                            $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核日志已被' . session('user')['nickname'] . '更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '</p>';
                        }
                        $address = $addressNickname['email'];
                        $nickname = $addressNickname['nickname'];
                        //发送邮件
                        $send_result = send_Email($address, $nickname, $subject, $body);
                        if ($send_result != 1) {
                            $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                            exit;
                        }
                    } else {
                        $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核日志已被' . session('user')['nickname'] . '更新并修改了交期时间';
                        //准备邮件发送数据
                        $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核日志已被' . session('user')['nickname'] . '更新';
                        if (empty($_comment)) {
                            $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核日志已被' . session('user')['nickname'] . '更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                        } else {
                            $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核日志已被' . session('user')['nickname'] . '更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '<br>预计交期：' . $_e_date . '</p>';
                        }
                        $address = $addressNickname['email'];
                        $nickname = $addressNickname['nickname'];
                        //发送邮件
                        $send_result = send_Email($address, $nickname, $subject, $body);
                        if ($send_result != 1) {
                            $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                            exit;
                        }
                    }
                    $arr['link'] = '/sampleDetails/' . $_order;
                    $arr['pushtime'] = time();
                    $arr['sampleorder'] = $_order;
                    $noticeIID = $notice->add($arr);
                    if ($noticeIID) {
                        $this->ajaxReturn(array('flag' => 1, 'msg' => '成功'));
                        exit;
                    } else {
                        $this->ajaxReturn(array('flag' => 0, 'msg' => '错误'));
                        exit;
                    }
                }
            }else{
                //查看数据库是否存在该样品的审核信息
                //如果存在则新增备注信息，否则更新备注信息
                //无论是更新还是新增都将通知销售及产品经理
                //为保留备注兼容三种状态，将各状态备注信息作为数组数序列化后存入数据库
                $comment['save'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                $arrSave['s_assoc'] = $_assoc;
                $arrSave['s_status'] = $_status;
                $arrSave['s_comment'] = serialize($comment);
                $arrSave['s_time'] = time();
                $arrSave['wid'] = $_wid;
                $id = $samples->add($arrSave);
                //将更新日志添加至通知表
                if($id){
                    $notice = M('Notice');
                    $addressNickname = M('User')->field('email,nickname')->find($_uid);
                    $arr['who'] = $_uid;
                    if ($_change) {
                        $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核日志已被' . session('user')['nickname'] . '更新';
                        //准备邮件发送数据
                        $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核日志已被' . session('user')['nickname'] . '更新';
                        if (empty($_comment)) {
                            $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核日志已被' . session('user')['nickname'] . '更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                        } else {
                            $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核日志已被' . session('user')['nickname'] . '更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '</p>';
                        }
                        $address = $addressNickname['email'];
                        $nickname = $addressNickname['nickname'];
                        //发送邮件
                        $send_result = send_Email($address, $nickname, $subject, $body);
                        if ($send_result != 1) {
                            $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                            exit;
                        }
                    }else{
                        $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核日志已被' . session('user')['nickname'] . '更新并修改了交期时间';
                        //准备邮件发送数据
                        $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，审核日志已被' . session('user')['nickname'] . '更新';
                        if (empty($_comment)) {
                            $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核日志已被' . session('user')['nickname'] . '更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                        } else {
                            $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品订单审核日志已被' . session('user')['nickname'] . '更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '<br>预计交期：' . $_e_date . '</p>';
                        }
                        $address = $addressNickname['email'];
                        $nickname = $addressNickname['nickname'];
                        //发送邮件
                        $send_result = send_Email($address, $nickname, $subject, $body);
                        if ($send_result != 1) {
                            $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                            exit;
                        }
                    }
                    $arr['link'] = '/sampleDetails/' . $_order;
                    $arr['pushtime'] = time();
                    $arr['sampleorder'] = $_order;
                    $noticeIID = $notice->add($arr);
                    if ($noticeIID) {
                        $this->ajaxReturn(array('flag' => 1, 'msg' => '成功'));
                        exit;
                    } else {
                        $this->ajaxReturn(array('flag' => 0, 'msg' => '错误'));
                        exit;
                    }
                }
            }
        }
    }

    //物料准备
    public function metaRial(){
        if(!IS_AJAX)return;
        //收集用户提交的数据
        $_assoc = I('post.w_assoc');
        $_status = I('post.w_status');
        $_e_date = I('post.e_date');
        $_comment = I('post.w_comment');
        $_wid = I('post.cid');
        $_uid = I('post.uid');
        $_total_order = I('post.total_order');
        $_product = I('post.product');
        $_order = I('post.order');
        //准备写入数据库的数据
        $comment = array();
        $samples = M('SampleW');
        $arr['w_assoc'] = $_assoc;
        $arr['w_status'] = $_status;
        $arr['w_comment'] = $_comment;
        $arr['w_time'] = time();
        $arr['cid'] = $_wid;
        //获取到预计交期
        $samplee = M('Sample');
        $e_date = $samplee->field('e_date')->find($_assoc)['e_date'];
        if($e_date==$_e_date){
            $_change = true;
        }else{
            $_change = false;
        }
        //修改预计时间
        $saveEdate = $samplee->save(array('id'=>$_assoc,'e_date'=>$_e_date));
        //只在状态为通过或者未通过的情况下执行
        if($_status && in_array($_status,array(1,2))){
            //查看是否存在属于该产品型号的步骤记录
            $sampleIsset = $samples->where('w_assoc="'.$_assoc.'"')->select()[0];
            //如果存在步骤记录则数据追加，反之则新增
            if($sampleIsset){
                if($_status==1){
                    //如果存在更新日志，则将状态追加到备注信息栏
                    $unserialize = unserialize($sampleIsset['w_comment']);
                    $saveComment = $unserialize;
                    $saveComment['success'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    //准备数据，更新操作时间
                    $saveData['id'] = $sampleIsset['id'];
                    $saveData['w_comment'] = serialize($saveComment);
                    $saveData['w_status'] = $_status;
                    $saveData['w_time'] = time();
                    $saveData['cid'] = $_wid;
                    $id = $samples->save($saveData);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            if($_change){
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您制作样品';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您制作样品';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您制作样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您制作样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else{
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备已完成';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备已完成';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备已完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备已完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }
                            }else{
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您制作样品';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您制作样品';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您制作样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您制作样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，物料准备已完成并修改了交期时间';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，物料准备已完成并修改了交期时间';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>物料准备已完成并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>物料准备已完成并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '<br>预计交期：' . $_e_date . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }else{
                    //如果审核未通过也将信息通知到销售
                    $unserialize = unserialize($sampleIsset['w_comment']);
                    $saveComment = $unserialize;
                    $saveComment['error'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $saveData['id'] = $sampleIsset['id'];
                    $saveData['w_comment'] = serialize($saveComment);
                    $saveData['w_status'] = $_status;
                    $saveData['w_time'] = time();
                    $saveData['cid'] = $_wid;
                    $id = $samples->save($saveData);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备未完成';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备未完成';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备未完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备未完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }else{
                //如果不存在步骤记录是执行
                if($_status==1){
                    $comment['success'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $arrSave['w_assoc'] = $_assoc;
                    $arrSave['w_status'] = $_status;
                    $arrSave['w_comment'] = serialize($comment);
                    $arrSave['w_time'] = time();
                    $arrSave['cid'] = $_wid;
                    $id = $samples->add($arrSave);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            if($_change){
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您制作样品';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您制作样品';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您制作样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您制作样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，物料准备已完成';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，物料准备已完成';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>物料准备已完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>物料准备已完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }else{
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您制作样品';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您制作样品';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您制作样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您制作样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，物料准备已完成并修改了交期时间';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，物料准备已完成并修改了交期时间';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>物料准备已完成并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>物料准备已完成并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '<br>预计交期：' . $_e_date . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }else{
                    $comment['error'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $arrSave['w_assoc'] = $_assoc;
                    $arrSave['w_status'] = $_status;
                    $arrSave['w_comment'] = serialize($comment);
                    $arrSave['w_time'] = time();
                    $arrSave['cid'] = $_wid;
                    $id = $samples->add($arrSave);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备未完成';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备未完成';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备未完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备未完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }
        }else{
            //查看数据库是否存在该样品的审核信息
            //如果存在则新增备注信息，否则更新备注信息
            //无论是更新还是新增都将通知销售及产品经理
            //为保留备注兼容三种状态，将各状态备注信息作为数组数序列化后存入数据库
            $sampleIsset = $samples->where('w_assoc="'.$_assoc.'"')->select()[0];
            if($sampleIsset){
                $unserialize = unserialize($sampleIsset['w_comment']);
                $saveComment = $unserialize;
                $saveComment['save'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                //print_r($saveComment);
                $saveData['id'] = $sampleIsset['id'];
                $saveData['w_comment'] = serialize($saveComment);
                $id = $samples->save($saveData);
                //将更新日志添加至通知表
                if($id){
                    $whoarr = array();
                    $sample = D('Sample');
                    $map['order'] = $_order;
                    $result = $sample->relation(true)->where($map)->select();
                    foreach($result as $key=>&$value){
                        foreach($value as $k=>&$v){
                            if(in_array($k,array('aid','uid'))){
                                if(session('user')['id']!=$v){
                                    array_push($whoarr,$v);
                                }
                            }
                        }
                    }
                    $notice = M('Notice');
                    foreach(array_unique($whoarr) as $key=>&$value){
                        $arr['who'] = $value;
                        if($_change){
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备日志已被'.session('user')['nickname'].'更新';
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备日志已被'.session('user')['nickname'].'更新';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                        }else{
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备日志已被'.session('user')['nickname'].'更新并修改了交期时间';
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备日志已被'.session('user')['nickname'].'更新';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备日志已被'.session('user')['nickname'].'更新并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备日志已被'.session('user')['nickname'].'更新并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                        }
                        $arr['link'] = '/sampleDetails/'.$_order;
                        $arr['pushtime'] = time();
                        $arr['sampleorder'] = $_order;
                        if(!$notice->add($arr)){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                            exit;
                        }
                    }
                    $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                    exit;
                }
            }else{
                //查看数据库是否存在该样品的审核信息
                //如果存在则新增备注信息，否则更新备注信息
                //无论是更新还是新增都将通知销售及产品经理
                //为保留备注兼容三种状态，将各状态备注信息作为数组数序列化后存入数据库
                $comment['save'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                $arrSave['w_assoc'] = $_assoc;
                $arrSave['w_status'] = $_status;
                $arrSave['w_comment'] = serialize($comment);
                $arrSave['w_time'] = time();
                $arrSave['cid'] = $_wid;
                $id = $samples->add($arrSave);
                //将更新日志添加至通知表
                if($id){
                    $whoarr = array();
                    $sample = D('Sample');
                    $map['order'] = $_order;
                    $result = $sample->relation(true)->where($map)->select();
                    foreach($result as $key=>&$value){
                        foreach($value as $k=>&$v){
                            if(in_array($k,array('aid','uid'))){
                                if(session('user')['id']!=$v){
                                    array_push($whoarr,$v);
                                }
                            }
                        }
                    }
                    $notice = M('Notice');
                    foreach(array_unique($whoarr) as $key=>&$value){
                        $arr['who'] = $value;
                        if($_change){
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备日志已被'.session('user')['nickname'].'更新';
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备日志已被'.session('user')['nickname'].'更新';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                        }else{
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备日志已被'.session('user')['nickname'].'更新并修改了交期时间';
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，物料准备日志已被'.session('user')['nickname'].'更新';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备日志已被'.session('user')['nickname'].'更新并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>物料准备日志已被'.session('user')['nickname'].'更新并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                        }
                        $arr['link'] = '/sampleDetails/'.$_order;
                        $arr['pushtime'] = time();
                        $arr['sampleorder'] = $_order;
                        if(!$notice->add($arr)){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                            exit;
                        }
                    }
                    $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                    exit;
                }
            }
        }
    }

    //样品制作
    public function productEngineer(){
        if(!IS_AJAX)return;
        //收集用户提交的数据
        $_assoc = I('post.c_assoc');
        $_status = I('post.c_status');
        $_e_date = I('post.e_date');
        $_comment = I('post.c_comment');
        $_wid = I('post.cid');
        $_uid = I('post.uid');
        $_total_order = I('post.total_order');
        $_product = I('post.product');
        $_order = I('post.order');
        //准备写入数据库的数据
        $comment = array();
        $samples = M('SampleC');
        $arr['c_assoc'] = $_assoc;
        $arr['c_status'] = $_status;
        $arr['c_comment'] = $_comment;
        $arr['c_time'] = time();
        $arr['yid'] = $_wid;
        //获取到要求交期
        $samplee = M('Sample');
        $e_date = $samplee->field('e_date')->find($_assoc)['e_date'];
        if($e_date==$_e_date){
            $_change = true;
        }else{
            $_change = false;
        }
        //修改预计时间
        $saveEdate = $samplee->save(array('id'=>$_assoc,'e_date'=>$_e_date));
        //只在状态为通过或者未通过的情况下执行
        if($_status && in_array($_status,array(1,2))){
            //查看是否存在属于该产品型号的步骤记录
            $sampleIsset = $samples->where('c_assoc="'.$_assoc.'"')->select()[0];
            //如果存在步骤记录则数据追加，反之则新增
            if($sampleIsset){
                if($_status==1){
                    //如果存在更新日志，则将状态追加到备注信息栏
                    $unserialize = unserialize($sampleIsset['c_comment']);
                    $saveComment = $unserialize;
                    $saveComment['success'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    //准备数据，更新操作时间
                    $saveData['id'] = $sampleIsset['id'];
                    $saveData['c_comment'] = serialize($saveComment);
                    $saveData['c_status'] = $_status;
                    $saveData['c_time'] = time();
                    $saveData['yid'] = $_wid;
                    $id = $samples->save($saveData);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        array_push($whoarr,$_wid);
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            if($_change){
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您测试样品';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您测试样品';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您测试样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您测试样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品已制作完成';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品已制作完成';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品已制作完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品已制作完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }else{
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您测试样品';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您测试样品';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您测试样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您测试样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品已制作完成并更新了交期时间';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品已制作完成并更新了交期时间';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品已制作完成并更新了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品已制作完成并更新了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '<br>预计交期：' . $_e_date . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }else{
                    //如果审核未通过也将信息通知到销售
                    $unserialize = unserialize($sampleIsset['c_comment']);
                    $saveComment = $unserialize;
                    $saveComment['error'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $saveData['id'] = $sampleIsset['id'];
                    $saveData['c_comment'] = serialize($saveComment);
                    $saveData['c_status'] = $_status;
                    $saveData['c_time'] = time();
                    $saveData['yid'] = $_wid;
                    $id = $samples->save($saveData);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品制作未完成';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品制作未完成';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品制作未完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品制作未完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }else{
                //如果不存在步骤记录是执行
                if($_status==1){
                    $comment['success'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $arrSave['c_assoc'] = $_assoc;
                    $arrSave['c_status'] = $_status;
                    $arrSave['c_comment'] = serialize($comment);
                    $arrSave['c_time'] = time();
                    $arrSave['yid'] = $_wid;
                    $id = $samples->add($arrSave);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        array_push($whoarr,$_wid);
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            if($_change){
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您测试样品';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您测试样品';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您测试样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您测试样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品制作已完成';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品制作已完成';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品制作已完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品制作已完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }else{
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您测试样品';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您测试样品';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您测试样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您测试样品，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品制作已完成并更新了交期时间';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品制作已完成并更新了交期时间';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品制作已完成并更新了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品制作已完成并更新了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '<br>预计交期：' . $_e_date . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }else{
                    $comment['error'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $arrSave['c_assoc'] = $_assoc;
                    $arrSave['c_status'] = $_status;
                    $arrSave['c_comment'] = serialize($comment);
                    $arrSave['c_time'] = time();
                    $arrSave['yid'] = $_wid;
                    $id = $samples->add($arrSave);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品制作未完成';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品制作未完成';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品制作未完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品制作未完成，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }
        }else{
            //查看数据库是否存在该样品的审核信息
            //如果存在则新增备注信息，否则更新备注信息
            //无论是更新还是新增都将通知销售及产品经理
            //为保留备注兼容三种状态，将各状态备注信息作为数组数序列化后存入数据库
            $sampleIsset = $samples->where('c_assoc="'.$_assoc.'"')->select()[0];
            if($sampleIsset){
                $unserialize = unserialize($sampleIsset['c_comment']);
                $saveComment = $unserialize;
                $saveComment['save'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                //print_r($saveComment);
                $saveData['id'] = $sampleIsset['id'];
                $saveData['c_comment'] = serialize($saveComment);
                $id = $samples->save($saveData);
                //将更新日志添加至通知表
                if($id){
                    $whoarr = array();
                    $sample = D('Sample');
                    $map['order'] = $_order;
                    $result = $sample->relation(true)->where($map)->select();
                    foreach($result as $key=>&$value){
                        foreach($value as $k=>&$v){
                            if(in_array($k,array('aid','uid','w_id'))){
                                if(session('user')['id']!=$v){
                                    array_push($whoarr,$v);
                                }
                            }
                        }
                    }
                    $notice = M('Notice');
                    foreach(array_unique($whoarr) as $key=>&$value){
                        $arr['who'] = $value;
                        if($_change){
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品制作日志已被'.session('user')['nickname'].'更新';
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品制作日志已被'.session('user')['nickname'].'更新';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品制作日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品制作日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                        }else{
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品制作日志已被'.session('user')['nickname'].'更新并修改了预计交期时间';
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品制作日志已被'.session('user')['nickname'].'更新并修改了预计交期时间';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品制作日志已被'.session('user')['nickname'].'更新并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品制作日志已被'.session('user')['nickname'].'更新并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                        }
                        $arr['link'] = '/sampleDetails/'.$_order;
                        $arr['pushtime'] = time();
                        $arr['sampleorder'] = $_order;
                        if(!$notice->add($arr)){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                            exit;
                        }
                    }
                    $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                    exit;
                }
            }else{
                //查看数据库是否存在该样品的审核信息
                //如果存在则新增备注信息，否则更新备注信息
                //无论是更新还是新增都将通知销售及产品经理
                //为保留备注兼容三种状态，将各状态备注信息作为数组数序列化后存入数据库
                $comment['save'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                $arrSave['c_assoc'] = $_assoc;
                $arrSave['c_status'] = $_status;
                $arrSave['c_comment'] = serialize($comment);
                $arrSave['c_time'] = time();
                $arrSave['yid'] = $_wid;
                $id = $samples->add($arrSave);
                //将更新日志添加至通知表
                if($id){
                    $whoarr = array();
                    $sample = D('Sample');
                    $map['order'] = $_order;
                    $result = $sample->relation(true)->where($map)->select();
                    foreach($result as $key=>&$value){
                        foreach($value as $k=>&$v){
                            if(in_array($k,array('aid','uid','w_id'))){
                                if(session('user')['id']!=$v){
                                    array_push($whoarr,$v);
                                }
                            }
                        }
                    }
                    $notice = M('Notice');
                    foreach(array_unique($whoarr) as $key=>&$value){
                        $arr['who'] = $value;
                        if($_change){
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品制作日志已被'.session('user')['nickname'].'更新';
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品制作日志已被'.session('user')['nickname'].'更新';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品制作日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品制作日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                        }else{
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品制作日志已被'.session('user')['nickname'].'更新并修改了交期时间';
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品制作日志已被'.session('user')['nickname'].'更新并修改了预计交期时间';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品制作日志已被'.session('user')['nickname'].'更新并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品制作日志已被'.session('user')['nickname'].'更新并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                        }
                        $arr['link'] = '/sampleDetails/'.$_order;
                        $arr['pushtime'] = time();
                        $arr['sampleorder'] = $_order;
                        if(!$notice->add($arr)){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                            exit;
                        }
                    }
                    $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                    exit;
                }
            }
        }
    }





    //邮件发送[测试成功]
    /*public function submitEmail(){
        send_Email('vinty_email@163.com','蒋明','标题','正文<a href="http://'.$_SERVER['HTTP_HOST'].'/index.php/home/sample/index">这里是链接</a>');
    }*/

    //样品详情页面上传测试报告
    public function uploadTestReport(){
        if(!IS_POST) return;
        $info = FileUpload('/TestReport/',0,1);
        if(!$info) return false;
        $newname = $info['Filedata']['savename'];
        $filetype = $info['Filedata']['ext'];
        $filePath = '/Uploads'.$info['Filedata']['savepath'].$info['Filedata']['savename'];
        $arr['type'] = $filetype;
        $arr['NewFileName'] = $newname;
        $arr['FileSavePath'] = $filePath;
        $this->ajaxReturn($arr);
        exit;
    }


    //样品测试
    public function testEngineer(){
        if(!IS_AJAX)return;
        //收集用户提交的数据
        $_assoc = I('post.y_assoc');
        $_status = I('post.y_status');
        $_comment = I('post.y_comment');
        $_wid = I('post.fid');
        $_uid = I('post.uid');
        $_e_date = I('post.e_date');
        $_total_order = I('post.total_order');
        $_product = I('post.product');
        $_order = I('post.order');
        //准备写入数据库的数据
        $comment = array();
        $samples = M('SampleY');
        $arr['y_assoc'] = $_assoc;
        $arr['y_status'] = $_status;
        $arr['y_comment'] = $_comment;
        $arr['y_time'] = time();
        $arr['fid'] = $_wid;
        //获取到要求交期
        $samplee = M('Sample');
        $e_date = $samplee->field('e_date')->find($_assoc)['e_date'];
        if($e_date==$_e_date){
            $_change = true;
        }else{
            $_change = false;
        }
        //修改预计时间
        $saveEdate = $samplee->save(array('id'=>$_assoc,'e_date'=>$_e_date));
        //只在状态为通过或者未通过的情况下执行
        if($_status && in_array($_status,array(1,2))){
            //查看是否存在属于该产品型号的步骤记录
            $sampleIsset = $samples->where('y_assoc="'.$_assoc.'"')->select()[0];
            //如果存在步骤记录则数据追加，反之则新增
            if($sampleIsset){
                if($_status==1){
                    //如果存在更新日志，则将状态追加到备注信息栏
                    $unserialize = unserialize($sampleIsset['y_comment']);
                    $saveComment = $unserialize;
                    $saveComment['success'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    //准备数据，更新操作时间
                    $saveData['id'] = $sampleIsset['id'];
                    $saveData['y_comment'] = serialize($saveComment);
                    $saveData['y_status'] = $_status;
                    $saveData['y_time'] = time();
                    $saveData['fid'] = $_wid;
                    $id = $samples->save($saveData);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id','c_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            if($_change){
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您发货';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您发货';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品测试已通过';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品测试已通过';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品测试已通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品测试已通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }else{
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您发货';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您发货';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品测试已通过并修改了交期时间';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品测试已通过并修改了交期时间';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品测试已通过并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品测试已通过并修改了交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '<br>预计交期：' . $_e_date . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }else{
                    //如果审核未通过也将信息通知到销售
                    $unserialize = unserialize($sampleIsset['y_comment']);
                    $saveComment = $unserialize;
                    $saveComment['error'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $saveData['id'] = $sampleIsset['id'];
                    $saveData['y_comment'] = serialize($saveComment);
                    $saveData['y_status'] = $_status;
                    $saveData['y_time'] = time();
                    $saveData['fid'] = $_wid;
                    $id = $samples->save($saveData);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id','c_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品测试未通过';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品测试未通过';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品测试未通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品测试未通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }else{
                //如果不存在步骤记录是执行
                if($_status==1){
                    $comment['success'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $arrSave['y_assoc'] = $_assoc;
                    $arrSave['y_status'] = $_status;
                    $arrSave['y_comment'] = serialize($comment);
                    $arrSave['y_time'] = time();
                    $arrSave['fid'] = $_wid;
                    $id = $samples->add($arrSave);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id','c_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            if($_change){
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您发货';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您发货';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品测试已通过';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品测试已通过';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品测试已通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品测试已通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }else{
                                if($value==$_wid){
                                    $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您发货';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，需要您发货';
                                    if(empty($_comment)){
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                                    }else{
                                        $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单需要您发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address,$nickname,$subject,$body);
                                    if($send_result!=1){
                                        $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                        exit;
                                    }
                                }else {
                                    $arr['message'] = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品测试已通过并修改了预计交期时间';
                                    //获取发送用户的邮箱和姓名
                                    $addressNickname = M('User')->field('email,nickname')->find($value);
                                    //准备邮件发送数据
                                    $subject = '样品订单[ ' . $_total_order . ' ]，产品[ ' . $_product . ' ]，样品测试已通过并修改了预计交期时间';
                                    if (empty($_comment)) {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品测试已通过并修改了预计交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '</p>';
                                    } else {
                                        $body = '<p style="margin-bottom: 20px;">Dear ' . $addressNickname['nickname'] . ',</p><p>样品测试已通过并修改了预计交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '">http://' . $_SERVER['HTTP_HOST'] . '/sampleDetails/' . $_order . '</a><br><br>样品订单：' . $_total_order . '<br>产品型号：' . $_product . '<br>备注信息：' . $_comment . '<br>预计交期：' . $_e_date . '</p>';
                                    }
                                    $address = $addressNickname['email'];
                                    $nickname = $addressNickname['nickname'];
                                    //发送邮件
                                    $send_result = send_Email($address, $nickname, $subject, $body);
                                    if ($send_result != 1) {
                                        $this->ajaxReturn(array('flag' => 0, 'msg' => $send_result));
                                        exit;
                                    }
                                }
                            }
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }else{
                    $comment['error'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $arrSave['y_assoc'] = $_assoc;
                    $arrSave['y_status'] = $_status;
                    $arrSave['y_comment'] = serialize($comment);
                    $arrSave['y_time'] = time();
                    $arrSave['fid'] = $_wid;
                    $id = $samples->add($arrSave);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id','c_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品测试未通过';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品测试未通过';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品测试未通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品测试未通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }
        }else{
            //查看数据库是否存在该样品的审核信息
            //如果存在则新增备注信息，否则更新备注信息
            //无论是更新还是新增都将通知销售及产品经理
            //为保留备注兼容三种状态，将各状态备注信息作为数组数序列化后存入数据库
            $sampleIsset = $samples->where('y_assoc="'.$_assoc.'"')->select()[0];
            if($sampleIsset){
                $unserialize = unserialize($sampleIsset['y_comment']);
                $saveComment = $unserialize;
                $saveComment['save'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                //print_r($saveComment);
                $saveData['id'] = $sampleIsset['id'];
                $saveData['y_comment'] = serialize($saveComment);
                $id = $samples->save($saveData);
                //将更新日志添加至通知表
                if($id){
                    $whoarr = array();
                    $sample = D('Sample');
                    $map['order'] = $_order;
                    $result = $sample->relation(true)->where($map)->select();
                    foreach($result as $key=>&$value){
                        foreach($value as $k=>&$v){
                            if(in_array($k,array('aid','uid','w_id','c_id'))){
                                if(session('user')['id']!=$v){
                                    array_push($whoarr,$v);
                                }
                            }
                        }
                    }
                    $notice = M('Notice');
                    foreach(array_unique($whoarr) as $key=>&$value){
                        $arr['who'] = $value;
                        if($_change){
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品测试日志已被'.session('user')['nickname'].'更新';
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品测试日志已被'.session('user')['nickname'].'更新';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品测试日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品测试日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                        }else{
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品测试日志已被'.session('user')['nickname'].'更新并修改了预计交期时间';
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品测试日志已被'.session('user')['nickname'].'更新并修改了预计交期时间';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品测试日志已被'.session('user')['nickname'].'更新并修改了预计交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品测试日志已被'.session('user')['nickname'].'更新并修改了预计交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                        }
                        $arr['link'] = '/sampleDetails/'.$_order;
                        $arr['pushtime'] = time();
                        $arr['sampleorder'] = $_order;
                        if(!$notice->add($arr)){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                            exit;
                        }
                    }
                    $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                    exit;
                }
            }else{
                //查看数据库是否存在该样品的审核信息
                //如果存在则新增备注信息，否则更新备注信息
                //无论是更新还是新增都将通知销售及产品经理
                //为保留备注兼容三种状态，将各状态备注信息作为数组数序列化后存入数据库
                $comment['save'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                $arrSave['y_assoc'] = $_assoc;
                $arrSave['y_status'] = $_status;
                $arrSave['y_comment'] = serialize($comment);
                $arrSave['y_time'] = time();
                $arrSave['fid'] = $_wid;
                $id = $samples->add($arrSave);
                //将更新日志添加至通知表
                if($id){
                    $whoarr = array();
                    $sample = D('Sample');
                    $map['order'] = $_order;
                    $result = $sample->relation(true)->where($map)->select();
                    foreach($result as $key=>&$value){
                        foreach($value as $k=>&$v){
                            if(in_array($k,array('aid','uid','w_id','c_id'))){
                                if(session('user')['id']!=$v){
                                    array_push($whoarr,$v);
                                }
                            }
                        }
                    }
                    $notice = M('Notice');
                    foreach(array_unique($whoarr) as $key=>&$value){
                        $arr['who'] = $value;
                        if($_change){
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品测试日志已被'.session('user')['nickname'].'更新';
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品测试日志已被'.session('user')['nickname'].'更新';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品测试日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品测试日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                        }else{
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品测试日志已被'.session('user')['nickname'].'更新并修改了预计交期时间';
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品测试日志已被'.session('user')['nickname'].'更新并修改了预计交期时间';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品测试日志已被'.session('user')['nickname'].'更新并修改了预计交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品测试日志已被'.session('user')['nickname'].'更新并修改了预计交期时间，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'<br>预计交期：'.$_e_date.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                        }
                        $arr['link'] = '/sampleDetails/'.$_order;
                        $arr['pushtime'] = time();
                        $arr['sampleorder'] = $_order;
                        if(!$notice->add($arr)){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                            exit;
                        }
                    }
                    $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                    exit;
                }
            }
        }
    }

    //发货
    public function delivery(){
        if(!IS_AJAX)return;
        //收集用户提交的数据
        $_assoc = I('post.f_assoc');
        $_status = I('post.f_status');
        $_comment = I('post.f_comment');
        $_wid = I('post.fid');
        $_uid = I('post.uid');
        $_logistics = I('post.logistics');
        $_awb = I('post.awb');
        $_total_order = I('post.total_order');
        $_product = I('post.product');
        $_order = I('post.order');
        $_attachment = I('post.attachment');
        //准备写入数据库的数据
        $comment = array();
        $samples = M('SampleF');
        $arr['f_assoc'] = $_assoc;
        $arr['f_status'] = $_status;
        $arr['logistics'] = $_logistics;
        $arr['awb'] = $_awb;
        $arr['f_comment'] = $_comment;
        $arr['f_time'] = time();
        $arr['attachment'] = $_attachment;
        $arr['kid'] = $_wid;
        //修改实际时间
        if($_status==1){
            //获取到要求交期
            $samplee = M('Sample');
            $saveEdate = $samplee->save(array('id'=>$_assoc,'a_date'=>time()));
        }
        //只在状态为通过或者未通过的情况下执行
        if($_status && in_array($_status,array(1,2))){
            //查看是否存在属于该产品型号的步骤记录
            $sampleIsset = $samples->where('f_assoc="'.$_assoc.'"')->select()[0];
            //如果存在步骤记录则数据追加，反之则新增
            if($sampleIsset){
                if($_status==1){
                    //如果存在更新日志，则将状态追加到备注信息栏
                    $unserialize = unserialize($sampleIsset['f_comment']);
                    $saveComment = $unserialize;
                    $saveComment['success'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    //准备数据，更新操作时间
                    $saveData['id'] = $sampleIsset['id'];
                    $saveData['f_comment'] = serialize($saveComment);
                    $saveData['f_status'] = $_status;
                    $saveData['logistics'] = $_logistics;
                    $saveData['awb'] = $_awb;
                    $saveData['attachment'] = $_attachment;
                    $saveData['f_time'] = time();
                    $saveData['kid'] = $_wid;
                    $id = $samples->save($saveData);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id','c_id','y_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品已发货';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品已发货';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品已发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品已发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }else{
                    //如果审核未通过也将信息通知到销售
                    $unserialize = unserialize($sampleIsset['f_comment']);
                    $saveComment = $unserialize;
                    $saveComment['error'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $saveData['id'] = $sampleIsset['id'];
                    $saveData['f_comment'] = serialize($saveComment);
                    $saveData['f_status'] = $_status;
                    $saveData['f_time'] = time();
                    $saveData['kid'] = $_wid;
                    $id = $samples->save($saveData);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id','c_id','y_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品未发货';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品未发货';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品未发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品未发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }else{
                //如果不存在步骤记录是执行
                if($_status==1){
                    $comment['success'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $arrSave['f_assoc'] = $_assoc;
                    $arrSave['f_status'] = $_status;
                    $arrSave['logistics'] = $_logistics;
                    $arrSave['awb'] = $_awb;
                    $arrSave['f_comment'] = serialize($comment);
                    $arrSave['f_time'] = time();
                    $arrSave['attachment'] = $_attachment;
                    $arrSave['kid'] = $_wid;
                    $id = $samples->add($arrSave);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id','c_id','y_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品已发货';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品已发货';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品已发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品已发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }else{
                    $comment['error'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $arrSave['f_assoc'] = $_assoc;
                    $arrSave['f_status'] = $_status;
                    $arrSave['f_comment'] = serialize($comment);
                    $arrSave['f_time'] = time();
                    $arrSave['kid'] = $_wid;
                    $id = $samples->add($arrSave);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id','c_id','y_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品未发货';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品未发货';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品未发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品未发货，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }
        }else{
            //查看数据库是否存在该样品的审核信息
            //如果存在则新增备注信息，否则更新备注信息
            //无论是更新还是新增都将通知销售及产品经理
            //为保留备注兼容三种状态，将各状态备注信息作为数组数序列化后存入数据库
            $sampleIsset = $samples->where('f_assoc="'.$_assoc.'"')->select()[0];
            if($sampleIsset){
                $unserialize = unserialize($sampleIsset['f_comment']);
                $saveComment = $unserialize;
                $saveComment['save'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                //print_r($saveComment);
                $saveData['id'] = $sampleIsset['id'];
                $saveData['f_comment'] = serialize($saveComment);
                $id = $samples->save($saveData);
                //将更新日志添加至通知表
                if($id){
                    $whoarr = array();
                    $sample = D('Sample');
                    $map['order'] = $_order;
                    $result = $sample->relation(true)->where($map)->select();
                    foreach($result as $key=>&$value){
                        foreach($value as $k=>&$v){
                            if(in_array($k,array('aid','uid','w_id','c_id','y_id'))){
                                if(session('user')['id']!=$v){
                                    array_push($whoarr,$v);
                                }
                            }
                        }
                    }
                    $notice = M('Notice');
                    foreach(array_unique($whoarr) as $key=>&$value){
                        $arr['who'] = $value;
                        $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品发货日志已被'.session('user')['nickname'].'更新';
                        $arr['link'] = '/sampleDetails/'.$_order;
                        $arr['pushtime'] = time();
                        $arr['sampleorder'] = $_order;
                        //获取发送用户的邮箱和姓名
                        $addressNickname = M('User')->field('email,nickname')->find($value);
                        //准备邮件发送数据
                        $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品发货日志已被'.session('user')['nickname'].'更新';
                        if(empty($_comment)){
                            $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品发货日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                        }else{
                            $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品发货日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                        }
                        $address = $addressNickname['email'];
                        $nickname = $addressNickname['nickname'];
                        //发送邮件
                        $send_result = send_Email($address,$nickname,$subject,$body);
                        if($send_result!=1){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                            exit;
                        }
                        if(!$notice->add($arr)){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                            exit;
                        }
                    }
                    $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                    exit;
                }
            }else{
                //查看数据库是否存在该样品的审核信息
                //如果存在则新增备注信息，否则更新备注信息
                //无论是更新还是新增都将通知销售及产品经理
                //为保留备注兼容三种状态，将各状态备注信息作为数组数序列化后存入数据库
                $comment['save'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                $arrSave['f_assoc'] = $_assoc;
                $arrSave['f_status'] = $_status;
                $arrSave['logistics'] = $_logistics;
                $arrSave['awb'] = $_awb;
                $arrSave['f_comment'] = serialize($comment);
                $arrSave['f_time'] = time();
                $arrSave['kid'] = $_wid;
                $id = $samples->add($arrSave);
                //将更新日志添加至通知表
                if($id){
                    $whoarr = array();
                    $sample = D('Sample');
                    $map['order'] = $_order;
                    $result = $sample->relation(true)->where($map)->select();
                    foreach($result as $key=>&$value){
                        foreach($value as $k=>&$v){
                            if(in_array($k,array('aid','uid','w_id','c_id','y_id'))){
                                if(session('user')['id']!=$v){
                                    array_push($whoarr,$v);
                                }
                            }
                        }
                    }
                    $notice = M('Notice');
                    foreach(array_unique($whoarr) as $key=>&$value){
                        $arr['who'] = $value;
                        $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品发货日志已被'.session('user')['nickname'].'更新';
                        $arr['link'] = '/sampleDetails/'.$_order;
                        $arr['pushtime'] = time();
                        $arr['sampleorder'] = $_order;
                        //获取发送用户的邮箱和姓名
                        $addressNickname = M('User')->field('email,nickname')->find($value);
                        //准备邮件发送数据
                        $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，样品发货日志已被'.session('user')['nickname'].'更新';
                        if(empty($_comment)){
                            $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品发货日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                        }else{
                            $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品发货日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                        }
                        $address = $addressNickname['email'];
                        $nickname = $addressNickname['nickname'];
                        //发送邮件
                        $send_result = send_Email($address,$nickname,$subject,$body);
                        if($send_result!=1){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                            exit;
                        }
                        if(!$notice->add($arr)){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                            exit;
                        }
                    }
                    $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                    exit;
                }
            }
        }
    }

    //反馈
    public function feedback(){
        if(!IS_AJAX)return;
        //收集用户提交的数据
        $_assoc = I('post.k_assoc');
        $_status = I('post.k_status');
        $_comment = I('post.k_comment');
        $_total_order = I('post.total_order');
        $_product = I('post.product');
        $_order = I('post.order');
        //准备写入数据库的数据
        $comment = array();
        $samples = M('SampleK');
        $arr['k_assoc'] = $_assoc;
        $arr['k_status'] = $_status;
        $arr['k_comment'] = $_comment;
        $arr['k_time'] = time();
        //只在状态为通过或者未通过的情况下执行
        if($_status && in_array($_status,array(1,2))){
            //查看是否存在属于该产品型号的步骤记录
            $sampleIsset = $samples->where('k_assoc="'.$_assoc.'"')->select()[0];
            //如果存在步骤记录则数据追加，反之则新增
            if($sampleIsset){
                if($_status==1){
                    //如果存在更新日志，则将状态追加到备注信息栏
                    $unserialize = unserialize($sampleIsset['k_comment']);
                    $saveComment = $unserialize;
                    $saveComment['success'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    //准备数据，更新操作时间
                    $saveData['id'] = $sampleIsset['id'];
                    $saveData['k_comment'] = serialize($saveComment);
                    $saveData['k_status'] = $_status;
                    $saveData['k_time'] = time();
                    $id = $samples->save($saveData);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id','c_id','y_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，最新反馈，客户测试已通过';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，最新反馈，客户测试已通过';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单最新反馈，客户测试已通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单最新反馈，客户测试已通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }else{
                    //如果审核未通过也将信息通知到销售
                    $unserialize = unserialize($sampleIsset['k_comment']);
                    $saveComment = $unserialize;
                    $saveComment['error'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $saveData['id'] = $sampleIsset['id'];
                    $saveData['k_comment'] = serialize($saveComment);
                    $saveData['k_status'] = $_status;
                    $saveData['k_time'] = time();
                    $id = $samples->save($saveData);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id','c_id','y_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，最新反馈，客户测试未通过';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，最新反馈，客户测试未通过';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单最新反馈，客户测试未通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单最新反馈，客户测试未通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }else{
                //如果不存在步骤记录是执行
                if($_status==1){
                    $comment['success'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $arrSave['k_assoc'] = $_assoc;
                    $arrSave['k_status'] = $_status;
                    $arrSave['k_comment'] = serialize($comment);
                    $arrSave['k_time'] = time();
                    $id = $samples->add($arrSave);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id','c_id','y_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，最新反馈，测试已通过';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，最新反馈，测试已通过';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单最新反馈，客户测试已通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单最新反馈，客户测试已通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }else{
                    $comment['error'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                    $arrSave['k_assoc'] = $_assoc;
                    $arrSave['k_status'] = $_status;
                    $arrSave['k_comment'] = serialize($comment);
                    $arrSave['k_time'] = time();
                    $id = $samples->add($arrSave);
                    //将更新日志添加至通知表
                    if($id){
                        $whoarr = array();
                        $sample = D('Sample');
                        $map['order'] = $_order;
                        $result = $sample->relation(true)->where($map)->select();
                        foreach($result as $key=>&$value){
                            foreach($value as $k=>&$v){
                                if(in_array($k,array('aid','uid','w_id','c_id','y_id'))){
                                    if(session('user')['id']!=$v){
                                        array_push($whoarr,$v);
                                    }
                                }
                            }
                        }
                        $notice = M('Notice');
                        foreach(array_unique($whoarr) as $key=>&$value){
                            $arr['who'] = $value;
                            $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，最新反馈，测试未通过';
                            $arr['link'] = '/sampleDetails/'.$_order;
                            $arr['pushtime'] = time();
                            $arr['sampleorder'] = $_order;
                            //获取发送用户的邮箱和姓名
                            $addressNickname = M('User')->field('email,nickname')->find($value);
                            //准备邮件发送数据
                            $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，最新反馈，测试未通过';
                            if(empty($_comment)){
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单最新反馈，客户测试未通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                            }else{
                                $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>样品订单最新反馈，客户测试未通过，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                            }
                            $address = $addressNickname['email'];
                            $nickname = $addressNickname['nickname'];
                            //发送邮件
                            $send_result = send_Email($address,$nickname,$subject,$body);
                            if($send_result!=1){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                                exit;
                            }
                            if(!$notice->add($arr)){
                                $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                                exit;
                            }
                        }
                        $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                        exit;
                    }else{
                        $this->ajaxReturn(array('flag'=>0,'msg'=>'错误'));
                        exit;
                    }
                }
            }
        }else{
            //查看数据库是否存在该样品的审核信息
            //如果存在则新增备注信息，否则更新备注信息
            //无论是更新还是新增都将通知销售及产品经理
            //为保留备注兼容三种状态，将各状态备注信息作为数组数序列化后存入数据库
            $sampleIsset = $samples->where('k_assoc="'.$_assoc.'"')->select()[0];
            if($sampleIsset){
                $unserialize = unserialize($sampleIsset['k_comment']);
                $saveComment = $unserialize;
                $saveComment['save'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                //print_r($saveComment);
                $saveData['id'] = $sampleIsset['id'];
                $saveData['k_comment'] = serialize($saveComment);
                $id = $samples->save($saveData);
                //将更新日志添加至通知表
                if($id){
                    $whoarr = array();
                    $sample = D('Sample');
                    $map['order'] = $_order;
                    $result = $sample->relation(true)->where($map)->select();
                    foreach($result as $key=>&$value){
                        foreach($value as $k=>&$v){
                            if(in_array($k,array('aid','uid','w_id','c_id','y_id'))){
                                if(session('user')['id']!=$v){
                                    array_push($whoarr,$v);
                                }
                            }
                        }
                    }
                    $notice = M('Notice');
                    foreach(array_unique($whoarr) as $key=>&$value){
                        $arr['who'] = $value;
                        $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，反馈日志已被'.session('user')['nickname'].'更新';
                        $arr['link'] = '/sampleDetails/'.$_order;
                        $arr['pushtime'] = time();
                        $arr['sampleorder'] = $_order;
                        //获取发送用户的邮箱和姓名
                        $addressNickname = M('User')->field('email,nickname')->find($value);
                        //准备邮件发送数据
                        $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，反馈日志已被'.session('user')['nickname'].'更新';
                        if(empty($_comment)){
                            $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>反馈日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                        }else{
                            $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>反馈日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                        }
                        $address = $addressNickname['email'];
                        $nickname = $addressNickname['nickname'];
                        //发送邮件
                        $send_result = send_Email($address,$nickname,$subject,$body);
                        if($send_result!=1){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                            exit;
                        }
                        if(!$notice->add($arr)){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                            exit;
                        }
                    }
                    $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                    exit;
                }
            }else{
                //查看数据库是否存在该样品的审核信息
                //如果存在则新增备注信息，否则更新备注信息
                //无论是更新还是新增都将通知销售及产品经理
                //为保留备注兼容三种状态，将各状态备注信息作为数组数序列化后存入数据库
                $comment['save'][] = array('time'=>time(),'message'=>$_comment,'manager'=>session('user')['nickname']);
                $arrSave['k_assoc'] = $_assoc;
                $arrSave['k_status'] = $_status;
                $arrSave['k_comment'] = serialize($comment);
                $arrSave['k_time'] = time();
                $id = $samples->add($arrSave);
                //将更新日志添加至通知表
                if($id){
                    $whoarr = array();
                    $sample = D('Sample');
                    $map['order'] = $_order;
                    $result = $sample->relation(true)->where($map)->select();
                    foreach($result as $key=>&$value){
                        foreach($value as $k=>&$v){
                            if(in_array($k,array('aid','uid','w_id','c_id','y_id'))){
                                if(session('user')['id']!=$v){
                                    array_push($whoarr,$v);
                                }
                            }
                        }
                    }
                    $notice = M('Notice');
                    foreach(array_unique($whoarr) as $key=>&$value){
                        $arr['who'] = $value;
                        $arr['message'] = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，反馈日志已被'.session('user')['nickname'].'更新';
                        $arr['link'] = '/sampleDetails/'.$_order;
                        $arr['pushtime'] = time();
                        $arr['sampleorder'] = $_order;
                        //获取发送用户的邮箱和姓名
                        $addressNickname = M('User')->field('email,nickname')->find($value);
                        //准备邮件发送数据
                        $subject = '样品订单[ '.$_total_order.' ]，产品[ '.$_product.' ]，反馈日志已被'.session('user')['nickname'].'更新';
                        if(empty($_comment)){
                            $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>反馈日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'</p>';
                        }else{
                            $body = '<p style="margin-bottom: 20px;">Dear '.$addressNickname['nickname'].',</p><p>反馈日志已被'.session('user')['nickname'].'更新，详情请点击链接&nbsp;<a style="color:#428bca;" href="http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'">http://'.$_SERVER['HTTP_HOST'].'/sampleDetails/'.$_order.'</a><br><br>样品订单：'.$_total_order.'<br>产品型号：'.$_product.'<br>备注信息：'.$_comment.'</p>';
                        }
                        $address = $addressNickname['email'];
                        $nickname = $addressNickname['nickname'];
                        //发送邮件
                        $send_result = send_Email($address,$nickname,$subject,$body);
                        if($send_result!=1){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>$send_result));
                            exit;
                        }
                        if(!$notice->add($arr)){
                            $this->ajaxReturn(array('flag'=>0,'msg'=>'失败'));
                            exit;
                        }
                    }
                    $this->ajaxReturn(array('flag'=>1,'msg'=>'成功'));
                    exit;
                }
            }
        }
    }

    //样品统计
    public function chart(){
        $arr = array();
        $sample = D('Sample');
        $result = $sample->relation(true)->select();
        foreach($result as $key=>&$value){
            if($value['s_status']=='' || $value['s_status']==3 && $value['w_status']=='' && $value['c_status']=='' && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '审核中';
                $value['color'] = 0;
            }else if($value['s_status']==2 && $value['w_status']=='' && $value['c_status']=='' && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '审核失败';
                $value['color'] = 2;
            }else if($value['s_status']==1 && $value['w_status']=='' || $value['w_status']==3 && $value['c_status']=='' && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '物料准备中';
                $value['color'] = 0;
            }else if($value['s_status']==1 && $value['w_status']==2 && $value['c_status']=='' && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '物料准备未完成';
                $value['color'] = 2;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']=='' || $value['c_status']==3 && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '样品制作中';
                $value['color'] = 0;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==2 && $value['y_status']=='' && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '样品制作未完成';
                $value['color'] = 2;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']=='' || $value['y_status']==3 && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '样品测试中';
                $value['color'] = 0;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==2 && $value['f_status']=='' && $value['k_status']==''){
                $value['state'] = '样品测试未通过';
                $value['color'] = 2;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==1 && $value['f_status']=='' || $value['f_status']==3 && $value['k_status']==''){
                $value['state'] = '等待发货';
                $value['color'] = 0;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==1 && $value['f_status']==2 && $value['k_status']==''){
                $value['state'] = '发货失败';
                $value['color'] = 2;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==1 &&  $value['f_status']==1 && $value['k_status']=='' || $value['k_status']==3){
                $value['state'] = '待反馈';
                $value['color'] = 0;
            }else if($value['s_status']==1 && $value['w_status']==1 && $value['c_status']==1 && $value['y_status']==1 && $value['f_status']==1 && $value['k_status']==2 || $value['k_status']==1){
                $value['state'] = '已反馈';
                $value['color'] = 3;
            }else{
                $result['state'] = 'error';
                $result['color'] = 2;
            }
        }
        //print_r($result);
        $this->display();
    }

    
}