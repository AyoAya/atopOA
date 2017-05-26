<?php
//屏蔽所有报错
error_reporting(0);
//设置时区
date_default_timezone_set('PRC');
//导入PHPMail邮件类
require 'E:\www\ThinkPHP\Library\Vendor\PHPMailer\class.phpmailer.php';
require 'E:\www\ThinkPHP\Library\Vendor\PHPMailer\class.smtp.php';

//定期推送汇总邮件
class MailPush {



    //保存数据库资源句柄
    public $mysqli = null;
    //结果集资源
    public $result = null;
    //收件人
    public $address = array(
/*        'yangpeiyun@atoptechnology.com',    //杨培云
        'xiaoaiyou@atoptechnology.com',     //肖艾佑
        'chenshi@atoptechnology.com',   //陈实
        'haorui@atoptechnology.com',        //郝锐
        'jonas@atoptechnology.com'         //张炜哲*/
    );
    public $cc = array(
/*        'sunbin@atoptechnology.com',        //孙膑
        'dingzheng@atoptechnology.com',     //丁征
        'mikechen@atoptechnology.com',      //陈应时
        'xiaxiaosen@atoptechnology.com',    //夏小森
        'liping@atoptechnology.com',    //李平
        'kent@atoptechnology.com',      //董总
        'jackfan@atoptechnology.com'    //范总*/
    );


    //自动连接数据库
    public function __construct(){
        $this->mysql_init();
        $this->get_summary_info();
        $this->send_email($this->address,date('Y').'年第'.date('W').'周样品单状态汇总表',$this->result,$this->cc);
    }

    //初始化连接数据库
    public function mysql_init(){
        $mysqli = mysqli_connect('localhost','root','root','atop');
        mysqli_set_charset($mysqli,'UTF8');
        if($mysqli){
            $this->mysqli = $mysqli;
        }
    }

    //获取数据汇总信息
    public function get_summary_info(){

        $key = 0;
        $result = array();
        //获取到本周周一
        $startDate = mktime(0, 0 , 0,date("m"),date("d")-date("w")+1,date("Y"));
        //获取到本周周五
        $endDate = mktime(23,59,59,date("m"),date("d")-date("w")+5,date("Y"));
        //获取订单分组
        $totalorderGroup = "SELECT totalorder,createtime,customer,saleperson FROM atop_sample group by totalorder";


        $AllTotalorder = mysqli_query($this->mysqli,$totalorderGroup);
        while($totalRow = mysqli_fetch_assoc($AllTotalorder)){
            $arr = array();
            //获取订单子数据
            $childGroup = 'SELECT * FROM atop_sample WHERE totalorder="'.$totalRow['totalorder'].'"';
            $AllChild = mysqli_query($this->mysqli,$childGroup);
            while($childRow = mysqli_fetch_assoc($AllChild)){
                //连接子表(审核)
                $linkSampleS = "SELECT s.s_status,s.wid,s.s_time,u.nickname FROM atop_sample a,atop_sample_s s,atop_user u WHERE s.s_assoc={$childRow['id']} AND s.wid=u.id";
                $linkSampleSResult = mysqli_query($this->mysqli,$linkSampleS);
                if($linkSampleSResult){
                    while($SResult = mysqli_fetch_assoc($linkSampleSResult)){
                        $childRow['s_status'] = $SResult['s_status'];
                        $childRow['s_nickname'] = $SResult['nickname'];
                        $childRow['s_time'] = $SResult['s_time'];
                    }
                }
                //连接子表(物料准备)
                $linkSampleW = "SELECT w.w_status,w.cid,w.w_time,u.nickname FROM atop_sample a,atop_sample_w w,atop_user u WHERE w.w_assoc={$childRow['id']} AND w.cid=u.id";
                $linkSampleWResult = mysqli_query($this->mysqli,$linkSampleW);
                if($linkSampleWResult){
                    while($WResult = mysqli_fetch_assoc($linkSampleWResult)){
                        $childRow['w_status'] = $WResult['w_status'];
                        $childRow['w_nickname'] = $WResult['nickname'];
                        $childRow['w_time'] = $WResult['w_time'];
                    }
                }
                //连接子表(样品制作)
                $linkSampleC = "SELECT c.c_status,c.yid,c.c_time,u.nickname FROM atop_sample a,atop_sample_c c,atop_user u WHERE c.c_assoc={$childRow['id']} AND c.yid=u.id";
                $linkSampleCResult = mysqli_query($this->mysqli,$linkSampleC);
                if($linkSampleCResult){
                    while($CResult = mysqli_fetch_assoc($linkSampleCResult)){
                        $childRow['c_status'] = $CResult['c_status'];
                        $childRow['c_nickname'] = $CResult['nickname'];
                        $childRow['c_time'] = $CResult['c_time'];
                    }
                }
                //连接子表(样品测试)
                $linkSampleY = "SELECT y.y_status,y.fid,y.y_time,u.nickname FROM atop_sample a,atop_sample_y y,atop_user u WHERE y.y_assoc={$childRow['id']} AND y.fid=u.id";
                $linkSampleYResult = mysqli_query($this->mysqli,$linkSampleY);
                if($linkSampleYResult){
                    while($YResult = mysqli_fetch_assoc($linkSampleYResult)){
                        $childRow['y_status'] = $YResult['y_status'];
                        $childRow['y_nickname'] = $YResult['nickname'];
                        $childRow['y_time'] = $YResult['y_time'];
                    }
                }
                //连接子表(发货)
                $linkSampleF = "SELECT f.f_status,f.kid,f.f_time,u.nickname FROM atop_sample a,atop_sample_f f,atop_user u WHERE f.f_assoc={$childRow['id']} AND f.kid=u.id";
                $linkSampleFResult = mysqli_query($this->mysqli,$linkSampleF);
                if($linkSampleFResult){
                    while($FResult = mysqli_fetch_assoc($linkSampleFResult)){
                        $childRow['f_status'] = $FResult['f_status'];
                        $childRow['f_nickname'] = $FResult['nickname'];
                        $childRow['f_time'] = $FResult['f_time'];
                    }
                }
                //连接子表(反馈)
                $linkSampleK = "SELECT k_status,k_time FROM atop_sample_k WHERE k_assoc={$childRow['id']}";
                $linkSampleKResult = mysqli_query($this->mysqli,$linkSampleK);
                if($linkSampleKResult){
                    while($KResult = mysqli_fetch_assoc($linkSampleKResult)){
                        $childRow['k_status'] = $KResult['k_status'];
                        $childRow['k_time'] = $KResult['k_time'];
                    }
                }
                array_push($arr,$childRow);
            }
            $result[$key]['totalorder'] = $totalRow['totalorder'];
            $result[$key]['customer'] = $totalRow['customer'];
            $result[$key]['saleperson'] = $totalRow['saleperson'];
            $result[$key]['childData'] = $arr;
            $key++;
        }
        foreach($result as $key=>&$value){
            foreach($value['childData'] as $k=>&$v){
                if(!isset($v['s_status']) || $v['s_status']==3 && !isset($v['w_status']) && !isset($v['c_status']) && !isset($v['y_status']) && !isset($v['f_status']) && !isset($v['k_status'])){
                    $v['state'] = '待审核';
                    $v['color'] = 'warning';
                    $v['operationPerson'] = $v['manager'];
                }elseif($v['s_status']==2 && !isset($v['w_status']) && !isset($v['c_status']) && !isset($v['y_status']) && !isset($v['f_status']) && !isset($v['k_status'])){
                    $v['state'] = '审核未通过';
                    $v['color'] = 'danger';
                    $v['operationPerson'] = $v['manager'];
                }elseif($v['s_status']==1 && !isset($v['w_status']) || $v['w_status']==3 && !isset($v['c_status']) && !isset($v['y_status']) && !isset($v['f_status']) && !isset($v['k_status'])){
                    $v['state'] = '物料准备中';
                    $v['color'] = 'warning';
                    $v['operationPerson'] = $v['s_nickname'];
                }elseif($v['s_status']==1 && $v['w_status']==2 && !isset($v['c_status']) && !isset($v['y_status']) && !isset($v['f_status']) && !isset($v['k_status'])){
                    $v['state'] = '物料准备未完成';
                    $v['color'] = 'danger';
                    $v['operationPerson'] = $v['s_nickname'];
                }elseif($v['s_status']==1 && $v['w_status']==1 && !isset($v['c_status']) || $v['c_status']==3 && !isset($v['y_status']) && !isset($v['f_status']) && !isset($v['k_status'])){
                    $v['state'] = '样品制作中';
                    $v['color'] = 'warning';
                    $v['operationPerson'] = $v['manager'];
                }elseif($v['s_status']==1 && $v['w_status']==1 && $v['c_status']==2 && !isset($v['y_status']) && !isset($v['f_status']) && !isset($v['k_status'])){
                    $v['state'] = '样品制作未完成';
                    $v['color'] = 'danger';
                    $v['operationPerson'] = $v['manager'];
                }elseif($v['s_status']==1 && $v['w_status']==1 && $v['c_status']==1 && !isset($v['y_status']) || $v['y_status']==3 && !isset($v['f_status']) && !isset($v['k_status'])){
                    $v['state'] = '样品测试中';
                    $v['color'] = 'warning';
                    $v['operationPerson'] = $v['c_nickname'];
                }elseif($v['s_status']==1 && $v['w_status']==1 && $v['c_status']==1 && $v['y_status']==2 && !isset($v['f_status']) && !isset($v['k_status'])){
                    $v['state'] = '样品测试未通过';
                    $v['color'] = 'danger';
                    $v['operationPerson'] = $v['c_nickname'];
                }elseif($v['s_status']==1 && $v['w_status']==1 && $v['c_status']==1 && $v['y_status']==1 && !isset($v['f_status']) || $v['f_status']==3 && !isset($v['k_status'])){
                    $v['state'] = '待发货';
                    $v['color'] = 'warning';
                    $v['operationPerson'] = $v['manager'];
                }elseif($v['s_status']==1 && $v['w_status']==1 && $v['c_status']==1 && $v['y_status']==1 && $v['f_status']==2 && !isset($v['k_status'])){
                    $v['state'] = '样品未发货';
                    $v['color'] = 'danger';
                    $v['operationPerson'] = $v['manager'];
                }elseif($v['s_status']==1 && $v['w_status']==1 && $v['c_status']==1 && $v['y_status']==1 && $v['f_status']==1 && !isset($v['k_status']) || $v['k_status']==3){
                    $v['state'] = '已发货,待反馈';
                    $v['color'] = 'success';
                    $v['operationPerson'] = $v['saleperson'];
                }elseif($v['s_status']==1 && $v['w_status']==1 && $v['c_status']==1 && $v['y_status']==1 && $v['f_status']==1 && $v['k_status']==1 || $v['k_status']==2){
                    $v['state'] = '已反馈';
                    $v['color'] = 'success';
                    $v['operationPerson'] = $v['saleperson'];
                }else{
                    $v['state'] = 'UNKNOWN';
                    $v['color'] = 'danger';
                    $v['operationPerson'] = 'UNKNOWN';
                }
            }
        }
        //排除已完成并且时间小于本周的订单
        foreach($result as $key=>&$value){
            foreach($value['childData'] as $k=>&$v){
                if(isset($v['a_date']) && !empty($v['a_date']) && $v['a_date']!=''){
                    if( ($v['a_date']-strtotime($v['d_date'])) > 0 ){
                        $v['delivery_date_state'] = '延期';
                    }else{
                        $v['delivery_date_state'] = '正常';
                    }
                    $v['a_date'] = date('Y-m-d',$v['a_date']);
                }else{
                    if( (mktime(0,0,0,date('m'),date('d'),date('Y'))-strtotime($v['d_date'])) > 0 ){
                        $v['delivery_date_state'] = '延期';
                    }else{
                        $v['delivery_date_state'] = '正常';
                    }
                }
                if( isset($v['f_status']) && $v['f_status']==1 || isset($v['k_status']) && $v['k_status']==1 ){

                    if( isset($v['f_status']) && $v['f_status']==1 && $v['f_time'] < $startDate || isset($v['k_status']) && $v['k_status']==1 && $v['k_time'] < $startDate ){
                        unset($result[$key]['childData'][$k]);
                        //如果pn为空则将该订单删除
                        if(empty($result[$key]['childData'])){
                            unset($result[$key]);
                        }
                    }
                }else{
                    # 如果存在本周之前未完成的样品单将其删除
                    if( isset($v['s_status']) && $v['s_status']==2 && $v['s_time'] < $startDate
                        || isset($v['w_status']) && $v['w_status']==2 && $v['w_time'] < $startDate
                        || isset($v['c_status']) && $v['c_status']==2 && $v['c_time'] < $startDate
                        || isset($v['y_status']) && $v['y_status']==2 && $v['y_time'] < $startDate
                        || isset($v['f_status']) && $v['f_status']==2 && $v['f_time'] < $startDate
                        || isset($v['k_status']) && $v['k_status']==2 && $v['k_time'] < $startDate){
                        unset($result[$key]['childData'][$k]);
                        //如果pn为空则将该订单删除
                        if(empty($result[$key]['childData'])){
                            unset($result[$key]);
                        }
                    }
                }
            }
        }
        //var_export($result);
        //print_r($result);
        $body = '<center><h1 style="font-style: normal;font-weight: normal;margin: 20px 0 10px 0;letter-spacing:2px;font-size: 25px;"><span>'.date('Y').'年第'.date('W').'周</span>样品单状态汇总表</h1><table border="0" cellspacing="0" cellpadding="7" width="100%" style="border:solid 1px #ccc;border-bottom:none;font-size:12px;overflow:hidden;">
            <thead>
                <tr style="text-align:center;background-color:#428bca;color: #fff;font-size:13px;">
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">序号</td>
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">订单号</td>
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">客户名称</td>
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">销售</td>
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">产品型号</td>
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">数量</td>
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">下单时间</td>
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">需求交期</td>
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">预计交期</td>
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">实际交期</td>
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">交期状态</td>
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">产品经理</td>
                    <td style="border-right:solid 1px #ccc;font-weight:bold;text-align:center;">状态</td>
                    <td style="font-weight:bold;text-align:center;">当前进度处理人</td>
                </tr>
            </thead>
		<tbody>';
        $number = 1;
        foreach($result as $key=>&$value){
            $body .= '<tr style="text-align:center;font-size:12px;">
            <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;" rowspan="'.count($value['childData']).'">'.($number).'</td>
            <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;" rowspan="'.count($value['childData']).'">
                <a href="http://61.139.89.33:8088/sampleOverview/'.$value['totalorder'].'" target="_blank" style="color:#337ab7;text-decoration:none;">'.$value['totalorder'].'</a>
            </td>
            <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;" rowspan="'.count($value['childData']).'">'.$value['customer'].'</td>
            <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;" rowspan="'.count($value['childData']).'">'.$value['saleperson'].'</td>';
            $number++;
            foreach($value['childData'] as $k=>&$v){
                switch($v['color']){
                    case 'warning':
                        if($v['delivery_date_state']=='延期'){
                            $body .= '<td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><a href="http://61.139.89.33:8088/sampleDetails/'.$v['order'].'" target="_blank" style="color:#337ab7;text-decoration:none;">'.$v['product'].'</a></td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['number'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.date('Y-m-d',$v['createtime']).'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['d_date'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['e_date'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['a_date'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><span style="padding:2px 5px;background:#d9534f;border-radius:3px;color:#fff;">'.$v['delivery_date_state'].'</span></td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['manager'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><span style="padding:3px 8px;background-color:#f0ad4e;border-radius:3px;color:#fff;">'.$v['state'].'</span></td>
                                    <td style="border-bottom:solid 1px #ccc;text-align:center;">'.$v['operationPerson'].'</td>
                                </tr>';
                        }else{
                            $body .= '<td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><a href="http://61.139.89.33:8088/sampleDetails/'.$v['order'].'" target="_blank" style="color:#337ab7;text-decoration:none;">'.$v['product'].'</a></td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['number'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.date('Y-m-d',$v['createtime']).'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['d_date'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['e_date'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['a_date'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><span style="padding:2px 5px;background:#5cb85c;border-radius:3px;color:#fff;">'.$v['delivery_date_state'].'</span></td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['manager'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><span style="padding:3px 8px;background-color:#f0ad4e;border-radius:3px;color:#fff;">'.$v['state'].'</span></td>
                                    <td style="border-bottom:solid 1px #ccc;text-align:center;">'.$v['operationPerson'].'</td>
                                </tr>';
                        }
                        break;
                    case 'danger':
                        if($v['delivery_date_state']=='延期'){
                            $body .= '<td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><a href="http://61.139.89.33:8088/sampleDetails/'.$v['order'].'" target="_blank" style="color:#337ab7;text-decoration:none;">'.$v['product'].'</a></td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['number'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.date('Y-m-d',$v['createtime']).'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['d_date'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['e_date'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['a_date'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><span style="padding:2px 5px;background:#d9534f;border-radius:3px;color:#fff;">'.$v['delivery_date_state'].'</span></td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['manager'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><span style="padding:3px 8px;background-color:#d9534f;border-radius:3px;color:#fff;">'.$v['state'].'</span></td>
                                <td style="border-bottom:solid 1px #ccc;text-align:center;">'.$v['operationPerson'].'</td>
                            </tr>';
                        }else{
                            $body .= '<td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><a href="http://61.139.89.33:8088/sampleDetails/'.$v['order'].'" target="_blank" style="color:#337ab7;text-decoration:none;">'.$v['product'].'</a></td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['number'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.date('Y-m-d',$v['createtime']).'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['d_date'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['e_date'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['a_date'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><span style="padding:2px 5px;background:#5cb85c;border-radius:3px;color:#fff;">'.$v['delivery_date_state'].'</span></td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['manager'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><span style="padding:3px 8px;background-color:#d9534f;border-radius:3px;color:#fff;">'.$v['state'].'</span></td>
                                <td style="border-bottom:solid 1px #ccc;text-align:center;">'.$v['operationPerson'].'</td>
                            </tr>';
                        }
                        break;
                    case 'success':
                        if($v['delivery_date_state']=='延期'){
                            $body .= '<td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><a href="http://61.139.89.33:8088/sampleDetails/'.$v['order'].'" target="_blank" style="color:#337ab7;text-decoration:none;">'.$v['product'].'</a></td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['number'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.date('Y-m-d',$v['createtime']).'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['d_date'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['e_date'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['a_date'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><span style="padding:2px 5px;background:#d9534f;border-radius:3px;color:#fff;">'.$v['delivery_date_state'].'</span></td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['manager'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><span style="padding:3px 8px;background-color:#5cb85c;border-radius:3px;color:#fff;">'.$v['state'].'</span></td>
                                <td style="border-bottom:solid 1px #ccc;text-align:center;">'.$v['operationPerson'].'</td>
                            </tr>';
                        }else{
                            $body .= '<td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><a href="http://61.139.89.33:8088/sampleDetails/'.$v['order'].'" target="_blank" style="color:#337ab7;text-decoration:none;">'.$v['product'].'</a></td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['number'].'</td>
                                    <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.date('Y-m-d',$v['createtime']).'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['d_date'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['e_date'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['a_date'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><span style="padding:2px 5px;background:#5cb85c;border-radius:3px;color:#fff;">'.$v['delivery_date_state'].'</span></td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;">'.$v['manager'].'</td>
                                <td style="border-bottom:solid 1px #ccc;border-right:solid 1px #ccc;text-align:center;"><span style="padding:3px 8px;background-color:#5cb85c;border-radius:3px;color:#fff;">'.$v['state'].'</span></td>
                                <td style="border-bottom:solid 1px #ccc;text-align:center;">'.$v['operationPerson'].'</td>
                            </tr>';
                        }
                        break;
                }
            }
        }
        $body .= '</tbody></table></center><span style="position:relative;top:15px;font-size:14px;">本报表自动生成时间：'.date('Y-m-d H:i:s',time()).'</span>';
        $sign = '<div style="margin-top:40px;width: 100%;padding-top: 15px;border-top: solid 1px #ccc;"><div><img src="http://www.atoptechnology.com.cn/images/logo1.jpg" alt="..." style="float:left;"><span style="float:left;margin-left:15px;"><p style="margin:4px 0;">[ 该邮件由程序自动发送，请勿回复 ]</p><p style="margin:4px 0;">华拓光通信OA系统</p></span></div></div>';
        //echo $body.$sign;
        $this->result = $body.$sign;
    }

    //发送邮件
    public function send_email($address,$subject,$body,$cc=''){
        //邮箱配置
        $email_host = 'smtp.exmail.qq.com';
        $email_username = 'oa@atoptechnology.com';
        $email_password = 'Atop123456';
        $email_from_name = '华拓光通信OA系统';
        $email_port = '465';
        //设置签名信息
        //$sign = '<div style="margin-top:50px;width: 100%;padding-top: 15px;border-top: solid 1px #ccc;"><div><img src="http://www.atoptechnology.com.cn/images/logo1.jpg" alt="..." style="float:left;"><span style="float:left;margin-left:15px;"><p style="margin:4px 0;">[ 该邮件由程序自动发送，请勿回复 ]</p><p style="margin:4px 0;">华拓光通信OA系统</p></span></div></div>';
        //实例化PHPMail类
        $mail = new PHPMailer();
        $mail->isSMTP();// 使用SMTP服务
        $mail->CharSet = "UTF-8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->Host = $email_host;// 发送方的SMTP服务器地址
        $mail->SMTPAuth = true;// 是否使用身份验证
        $mail->Username = $email_username;// 发送方的163邮箱用户名
        $mail->Password = $email_password;// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！
        $mail->SMTPSecure = "ssl";// 使用ssl协议方式
        $mail->Port = $email_port;// 163邮箱的ssl协议方式端口号是465/994
        //设置收件人信息及内容
        $mail->setFrom($email_username,$email_from_name);// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示
        if(is_array($address)){
            foreach($address as $addressv){
                $mail->AddAddress($addressv);
            }
        }else{
            $mail->AddAddress($address);
        }
        if($cc!=''){
            if(is_array($cc)){
                foreach($cc as $ccperson){
                    $mail->addCC($ccperson);
                }
            }else{
                $mail->addCC($cc);
            }
        }
        //$mail->addAddress($address);// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
        $mail->IsHTML(true);
        $mail->Subject = $subject;// 邮件标题
        $mail->Body = $body;// 邮件正文加签名
        $mail->Send();
    }

}
//实例化汇总信息推送
$mail = new MailPush();

$mail->get_summary_info();

//print_r($mail->result);

?>