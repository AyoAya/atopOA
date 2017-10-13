<?php

# 屏蔽所有报错
error_reporting(0);

# 设置默认时间区
date_default_timezone_set('PRC');


//导入PHPMail邮件类
/*require 'E:\work\atopOA\ThinkPHP\Library\Vendor\PHPMailer\class.phpmailer.php';
require 'E:\work\atopOA\ThinkPHP\Library\Vendor\PHPMailer\class.smtp.php';*/
require 'E:\www\ThinkPHP\Library\Vendor\PHPMailer\class.phpmailer.php';
require 'E:\www\ThinkPHP\Library\Vendor\PHPMailer\class.smtp.php';



class RegularMail{

    # 定义数据库配置
    private $db_host = 'localhost:3306';
    private $db_user = 'root';
    private $db_pwd = 'root';
    private $db_name = 'atop';

    static $http_host = '61.139.89.33:8088';
    # static $http_host = 'atop.local';

    # 定义数据库资源句柄
    static $mysqli;

    # 构造方法
    public function __construct(){
        $this->connect();
        $this->GetOrderSummary();
    }

    # 连接数据库并返回实例
    public function connect(){
        # 连接数据库并返回资源句柄
        self::$mysqli = mysqli_connect( $this->db_host, $this->db_user, $this->db_pwd, $this->db_name );
        # 设置连接字符集
        self::$mysqli->query('set names utf8');
    }

    # 查询所有的数据
    public function GetOrderSummary(){

        # 所有需要通知的人
        $sql = 'SELECT DISTINCT who FROM atop_todolist WHERE state != "done" ';

        $todolist_data = self::select($sql);

        # 获取所有数据
        $sql = 'SELECT * FROM atop_todolist WHERE state != "done" ';

        $tmp_data = self::select($sql);
        # 将数据组合到通知人
        foreach ($todolist_data as $key=>&$value){
            foreach ($tmp_data as $k=>&$val){

                $sql = 'SELECT nickname,email FROM atop_user
                    WHERE id = '.$value['who'].'
';

                $value['nickname'] = self::select($sql)[0]['nickname'];
                $value['email'] = self::select($sql)[0]['email'];

                if($value['who'] == $val['who']){
                    $value['data'][] = $val;
                }
            }
        }

/*        print_r($todolist_data);
        die();*/

        self::output( $todolist_data );


    }

    private static function output( $data ){

        foreach ($data as $key=>&$value){

            $html = '<p>Dear '.$value['nickname'].'，</p> 
                     <p>以下待办事项请及时处理：</p>';

            foreach ($value['data'] as $k=>&$val){
                $html.='
                     <p>'.($k+1).' . <a style="text-decoration:none;color:#428bca;min-width: 300px; display:inline-block;" href="http://'.$val['url'].'">'.$val['matter_name'].'</a><span style="margin-left:40px;color: #999; font-size: 12px;">'.date("Y-m-d H:i",$val['generate_time']).'</span></p>';
            }



            $subject = '[待办事项] 待办事项通知';

            echo $html;

            self::push_eml($html,$subject,$value['email'],'');


        }


    }

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


    /**
     * 发送邮件
     */
    private static function push_eml($body,$subject,$address,$cc){

        sleep(1);

        # $http_host = 'atop.local';
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
        if( is_array($address)){
            foreach($address as $value){
                $mail->addAddress($value);//设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
            }
        }else{
            $mail->addAddress($address);//设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
        }

        if( $cc!='' ){
            if(is_array($cc)){
                foreach($cc as $ccperson){
                    $mail->addCC($ccperson);
                }
            }else{
                $mail->addCC($cc);
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

        $mail->Send();

        # var_dump($abc);


    }




}

# 实例化资源句柄
$push_email = new RegularMail();













