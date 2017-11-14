<?php
# 导入基类
require __DIR__ . '/../base.php';

class SampleMail extends base {

    # 构造方法
    public function __construct(){
        //echo '获取DB实例<br>';
        parent::__construct();
        //echo '检查数据库任务配置<br>';
        $this->getServerEmailPushData();
        //echo '发送邮件<br>';
        $this->GetOrderSummary();
        //echo 'end<br>';
    }

    # 获取服务器上邮件推送数据
    private function getServerEmailPushData(){
        $currentScriptName = basename(__FILE__, '.php');
        $sql = 'select * from atop_tasks where script = "'.$currentScriptName.'"';
        $result = self::select($sql);
        if( $result ){
            $sql = 'select * from atop_user where id in('.$result[0]['recipients'].')';
            $recipientsRes = self::select($sql);
            $sql = 'select * from atop_user where id in('.$result[0]['ccs'].')';
            $ccsRes = self::select($sql);
            if( $recipientsRes ){
                $recipients = [];
                foreach( $recipientsRes as $key=>$value ){
                    array_push($recipients, $value['email']);
                }
                parent::$address = array_unique($recipients);
            }
            if( $ccsRes ){
                $ccs = [];
                foreach( $ccsRes as $key=>$value ){
                    array_push($ccs, $value['email']);
                }
                parent::$cc = array_unique($ccs);
            }
        }
    }

    # 查询本周数据
    public function GetOrderSummary(){
        # 获取到本周一时间戳
        $start_date = strtotime(date('Y-m-d',(time()-((date('w')==0?7:date('w'))-1)*24*3600)));
        # 汇总当时时间
        $end_date = time();
        # 获取本周的订单数据
        $sql = 'SELECT * FROM atop_sample a,atop_sample_detail b
                WHERE ((a.create_time > '.$start_date.' AND a.create_time < '.$end_date.')
                OR (b.now_step < 7 AND b.state = "N")
                OR (UNIX_TIMESTAMP(b.actual_date) > '.$start_date.' AND UNIX_TIMESTAMP(b.actual_date) < '.$end_date.'))
                AND a.id = b.detail_assoc
                GROUP BY a.id
                ORDER BY b.id ASC';
        $sample_data = self::select($sql);
        foreach( $sample_data as $key=>&$value ){
            $sql = 'SELECT
                        a.id detail_id, a.pn, a.count, a.customer, a.brand, a.model, a.note, a.requirements_date, a.expect_date, a.actual_date, a.state, a.now_step, c.type, d.nickname, b.name
                      FROM
                        atop_sample_detail a, atop_sample_step b, atop_productrelationships c, atop_user d
                      WHERE
                        a.detail_assoc = '.$value['detail_assoc'].' AND a.product_id = c.id AND a.manager = d.id AND a.now_step = b.id';
            $value['detail'] = self::select($sql);
            foreach( $value['detail'] as $k=>&$v ){
                $sql = 'SELECT b.nickname current_person FROM atop_sample_operating a,atop_user b WHERE a.asc_detail='.$v['detail_id'].' AND a.operator=b.id';
                $tmpArr = self::select($sql);
                if(strtotime($v['actual_date']) < $start_date && !empty($v['actual_date'])){
                    unset($value['detail'][$k]);
                }
                $v['current_person'] = end($tmpArr)['current_person'];
                # 如果产品步骤大于6则说明该单已经完成
                if( $v['now_step'] > 6 ){
                    $v['class'] = 'success';
                }else{
                    $v['class'] = 'processing';
                }
            }
            sort($value['detail']);
        }
        self::output( $sample_data );
    }


    private static function output( $data ){
        $style = <<<STYLE
<style>
.title {
    padding: 15px 0;
    text-align: center;
    border-bottom: none;
    font-size: 18px;;
}
span.success {
    padding: 2px 5px;
    background: #5cb85c;
    color: #fff;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
}
span.processing {
    padding: 2px 5px;
    background: #428bca;
    color: #fff;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
}
span.danger {
    padding: 2px 5px;
    background: #d9534f;
    color: #fff;
    -webkit-border-radius: 2px;
    -moz-border-radius: 2px;
    border-radius: 2px;
}
.table {
    font-size: 12px;
    width: 100%;
    border: solid 1px #ccc;
}
.table th,.table td {
    padding: 9px 5px;
}
.table th,.table td a{
    text-decoration: none;
}
.table td a {
    color: #428bca;
}
.table thead tr th {
    border-right: solid 1px #ccc;
    color: #fff;
    background: #428bca;
    height: 20px;
}
.table thead tr th:last-child {
    border-right: none;
}
.table tbody tr td {
    text-align: center;
    border-top: solid 1px #ccc;
    border-right: solid 1px #ccc;
}
.table tbody tr td:last-child {
    border-right: none;
}
</style>
STYLE;
        $html = '<div class="title">'.date('Y',time()).'年第'.date('W',time()).'周样品订单状态汇总表</div>';
        $html .= "\r\n<table class='table' cellpadding='0' cellspacing='0'>
    <thead>
        <tr>
             <th>序号</th>
            <th>订单号</th>
            <th>销售</th>
            <th>下单时间</th>
            <th width='130'>产品型号</th>
            <th width='80'>要求交期</th>
            <th width='80'>预计交期</th>
            <th width='80'>实际交期</th>
            <th width='48'>模块数量</th>
            <th width='200'>客户名称</th>
            <th>设备品牌</th>
            <th>设备型号</th>
            <th width='48'>交期状态</th>
            <th>产品经理</th>
            <th width='90'>当前进度</th>
            <th>当前处理人</th>
        </tr>
    </thead>
    <tbody>\r\n";
        foreach( $data as $key=>&$value ){
            $html .= "\t\t<tr>\r\n";
            $html .= "\t\t\t<td rowspan='".count($value['detail'])."'>".($key+1)."</td>\r\n";
            $html .= "\t\t\t<td rowspan='".count($value['detail'])."'><a href='http://".self::$http_host."/Sample/overview/id/{$value['detail_assoc']}'>".$value['order_num']."</a></td>\r\n";
            $html .= "\t\t\t<td rowspan='".count($value['detail'])."'>".$value['create_person_name']."</td>\r\n";
            $html .= "\t\t\t<td rowspan='".count($value['detail'])."'>".date('Y-m-d ',$value['create_time'])."</td>\r\n";
            foreach( $value['detail'] as $k=>&$v ){
                if( $k == 0 ){
                    $html .= "\t\t\t<td><a href='http://".self::$http_host."/Sample/detail/id/{$v['detail_id']}'>".$v['pn']."</a></td>\r\n";
                    $html .= "\t\t\t<td>".$v['requirements_date']."</td>\r\n";
                    $html .= "\t\t\t<td>".$v['expect_date']."</td>\r\n";
                    $html .= "\t\t\t<td>".$v['actual_date']."</td>\r\n";
                    $html .= "\t\t\t<td>".$v['count']."</td>\r\n";
                    $html .= "\t\t\t<td>".$v['customer']."</td>\r\n";
                    $html .= "\t\t\t<td>".$v['brand']."</td>\r\n";
                    $html .= "\t\t\t<td>".$v['model']."</td>\r\n";
                    if( !empty($v['actual_date'])){
                        if( $v['actual_date'] < $v['requirements_date']){
                            $html .= "\t\t\t<td><span class='success'> 正常 </span></td>\r\n";
                        }else{
                            $html .= "\t\t\t<td><span class='danger'> 延期 </span></td>\r\n";
                        }
                    }else{
                        $html .= "\t\t\t<td><span class=''> --- </span></td>\r\n";
                    }
                    $html .= "\t\t\t<td>".$v['nickname']."</td>\r\n";
                    if( $v['state'] == 'N'|| $v['state'] == 'C' ){
                        if( $v['class'] == 'processing' ){
                            $html .= "\t\t\t<td><span class='".$v['class']."'>".$v['name']."中</span></td>\r\n";
                        }else{
                            if( $v['state'] == 'C' ){
                                $html .= "\t\t\t<td><span class='".$v['class']."'>已".$v['name']."</span></td>\r\n";
                            }else {
                                $html .= "\t\t\t<td><span class='".$v['class']."'>已发货待".$v['name']."</span></td>\r\n";
                            }
                        }
                    }else{
                        $html .= "\t\t\t<td><span class='danger'>".$v['name']."未通过</span></td>\r\n";
                    }
                    $html .= "\t\t\t<td>".$v['current_person']."</td>\r\n";
                    $html .= "\t\t</tr>\r\n";
                }else{
                    $html .= "\t\t<tr>\r\n";
                    $html .= "\t\t\t<td><a href='http://".self::$http_host."/Sample/detail/id/{$v['detail_id']}'>".$v['pn']."</a></td>\r\n";
                    $html .= "\t\t\t<td>".$v['requirements_date']."</td>\r\n";
                    $html .= "\t\t\t<td>".$v['expect_date']."</td>\r\n";
                    $html .= "\t\t\t<td>".$v['actual_date']."</td>\r\n";
                    $html .= "\t\t\t<td>".$v['count']."</td>\r\n";
                    $html .= "\t\t\t<td>".$v['customer']."</td>\r\n";
                    $html .= "\t\t\t<td>".$v['brand']."</td>\r\n";
                    $html .= "\t\t\t<td>".$v['model']."</td>\r\n";
                    if( !empty($v['actual_date'])){
                        if( $v['actual_date'] < $v['requirements_date']){
                            $html .= "\t\t\t<td><span class='success'> 正常 </span></td>\r\n";
                        }else{
                            $html .= "\t\t\t<td><span class='danger'> 延期 </span></td>\r\n";
                        }
                    }else{
                        $html .= "\t\t\t<td><span class=''> --- </span></td>\r\n";
                    }
                    $html .= "\t\t\t<td>".$v['nickname']."</td>\r\n";
                    if( $v['state'] == 'N'|| $v['state'] == 'C' ){
                        if( $v['class'] == 'processing' ){
                            $html .= "\t\t\t<td><span class='".$v['class']."'>".$v['name']."中</span></td>\r\n";
                        }else{
                            if( $v['state'] == 'C' ){
                                $html .= "\t\t\t<td><span class='".$v['class']."'>已".$v['name']."</span></td>\r\n";
                            }else {
                                $html .= "\t\t\t<td><span class='".$v['class']."'>已".$v['name']."待反馈</span></td>\r\n";
                            }
                        }
                    }else{
                        $html .= "\t\t\t<td><span class='danger'>".$v['name']."未通过</span></td>\r\n";
                    }
                    $html .= "\t\t\t<td>".$v['current_person']."</td>\r\n";
                    $html .= "\t\t</tr>\r\n";
                }
            }
        }
        $subject = date('Y',time()).'年第'.date('W',time()).'周样品订单进度汇总';
        $html .= "\t</tbody>\r\n</table>";
        //echo $style.$html;
        //print_r(parent::$address);
        //print_r(parent::$cc);
        echo parent::push_eml($style.$html,$subject, parent::$address, parent::$cc)."\r\n";
    }
}
new SampleMail();
