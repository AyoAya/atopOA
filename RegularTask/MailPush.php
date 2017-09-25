<?php

# 屏蔽所有报错
error_reporting(0);

# 设置默认时间区
date_default_timezone_set('PRC');


class PushEmail {

    # 定义数据库配置
    private $db_host = 'localhost:3306';
    private $db_user = 'root';
    private $db_pwd = 'root';
    private $db_name = 'atop';
	
	static $http_host = '61.139.89.33:8088';
	
    static $address = [
	
        'yangpeiyun@atoptechnology.com',    //杨培云
        'huangzhengyin@atoptechnology.com',   //黄正银
        'xiaoaiyou@atoptechnology.com',     //肖艾佑
        'chenshi@atoptechnology.com',   //陈实
        'haorui@atoptechnology.com',        //郝锐
        'jonas@atoptechnology.com'         //张炜哲	
		
	];
    static $cc = [
	
        'liuyan@atoptechnology.com',      //刘燕
        'yubo@atoptechnology.com',          //余波
        'tangzhiqiang@atoptechnology.com',        //唐志强
        'dingzheng@atoptechnology.com',     //丁征
        'mikechen@atoptechnology.com',      //陈应时
        'xiaxiaosen@atoptechnology.com',    //夏小森
        'liping@atoptechnology.com',    //李平
        'kent@atoptechnology.com',      //董总
        'jackfan@atoptechnology.com'    //范总

	];

    /*liping@atoptechnology*/
    # 定义数据库资源句柄
    static $mysqli;

    # 构造方法
    public function __construct(){
		echo '1111';
        $this->connect();
		echo '22222';
        $this->GetOrderSummary();
		echo '33333';
    }

    # 连接数据库并返回实例
    public function connect(){
        # 连接数据库并返回资源句柄
        self::$mysqli = mysqli_connect( $this->db_host, $this->db_user, $this->db_pwd, $this->db_name );
        # 设置连接字符集
        self::$mysqli->query('set names utf8');
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
    padding: 9px 15px;
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
		
        echo $style.$html;
        self::push_eml($style.$html,$subject);

    }


    /**
     * 发送邮件
     */
    private static function push_eml($body,$subject){

        $http_host = '61.139.89.33:8088';

        //设置签名信息
        $sign = <<<SIGN
<style>
    .sign {
        width: 98%;
        padding: 15px;
        margin-top: 50px;
        background: #2a3542;
        color: #fff;
    }
    .sign .logo {
        height: 40px;
    }
    .sign .info {
    
    }
    .sign .info p {
        margin: 0;
        padding: 0;
        line-height: 26px;
    }
    .clearfix {
        clear: both;
    }
</style>
<div class="sign">
    <div class="logo">
        <img src="http://$http_host/Public/home/img/atop_logo_email.png" alt=""/>
    </div>
    <div class="info">
        <p>该邮件由程序自动发送，请勿回复</p>
        <p>华拓光通信OA系统&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;URL：61.139.89.33:8088</p>
    </div>
    <div class="clearfix"></div>
</div>
SIGN;

        //导入PHPMail邮件类
        require 'E:\www\ThinkPHP\Library\Vendor\PHPMailer\class.phpmailer.php';
		require 'E:\www\ThinkPHP\Library\Vendor\PHPMailer\class.smtp.php';




        $mail = new \PHPMailer();

        $mail->isSMTP();// 使用SMTP服务
        $mail->CharSet = "UTF-8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
        $mail->Host = 'smtp.exmail.qq.com';// 发送方的SMTP服务器地址
        $mail->SMTPAuth = true;// 是否使用身份验证
        $mail->Username = 'oa@atoptechnology.com';// 发送方的163邮箱用户名
        $mail->Password = 'Atop123456';// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！
        $mail->SMTPSecure = "ssl";// 使用ssl协议方式
        $mail->Port = '465';// 163邮箱的ssl协议方式端口号是465/994
        $mail->setFrom('oa@atoptechnology.com','华拓光通信OA系统');// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示
        //$mail->addAddress($address,$nickname);// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
        if( is_array(self::$address) ){
            foreach(self::$address as $value){
                $mail->addAddress($value);//设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
            }
        }else{
            $mail->addAddress(self::$address);//设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
        }
        if( self::$cc!='' ){
            if(is_array(self::$cc)){
                foreach(self::$cc as $ccperson){
                    $mail->addCC($ccperson);
                }
            }else{
                $mail->addCC(self::$cc);
            }
        }
        //$mail->addReplyTo("oa@atoptechnology.com","华拓光通信股份有限公司");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
        //$mail->addCC("");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址
        //$mail->addBCC("");// 设置秘密抄送人
        //$mail->addAttachment("test.jpg");// 添加附件

        $mail->IsHTML(true);
        $mail->Subject = $subject;// 邮件标题
        $mail->Body = '<div style="color: #000;padding: 0 20px;">'.$body.$sign.'</div>';// 邮件正文加签名
        //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用
        if($mail->Send()){
            return 1;
        }else{
            return $mail->ErrorInfo;
        }
    }





    /**
     * 查询数据模型
     * @param $sql 传入sql语句
     * @return array
     */
    private static function select( $sql ){

        # 定义空数组作用存放结果集数据
        $tmpArr = [];

        # 执行sql语句并得到结果集
        $result = self::$mysqli->query( $sql );

        # 遍历结果集
        while( $row = $result->fetch_assoc() ) {
            $tmpArr[] = $row;
        }

        # 返回查询数据
        return $tmpArr;

    }


}

# 实例化资源句柄
$push_email = new PushEmail();

# print_r($push_email::$mysqli);
