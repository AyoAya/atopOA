<?php
# 屏蔽所有报错
// error_reporting(0);

# 设置默认时间区
// date_default_timezone_set('PRC');

# 加载可用task列表
require __DIR__ . '/task.config.php';
require __DIR__ . '/DB.php';

# 导入PHPMail邮件类
require 'E:\work\atopOA\ThinkPHP\Library\Vendor\PHPMailer\class.phpmailer.php';
require 'E:\work\atopOA\ThinkPHP\Library\Vendor\PHPMailer\class.smtp.php';

class base extends DB {
    # 定义服务器地址
    protected static $http_host = '61.139.89.33:8088';
    # task列表
    protected static $taskConfig = null;

    protected static $address = [

         'yangpeiyun@atoptechnology.com',    //杨培云
         'huangzhengyin@atoptechnology.com',   //黄正银
         'xiaoaiyou@atoptechnology.com',     //肖艾佑
         'chenshi@atoptechnology.com',   //陈实
         'haorui@atoptechnology.com',        //郝锐
         'jonas@atoptechnology.com'         //张炜哲
//        'jiangming@atoptechnology.com'         // jiangming

	];
    protected static $cc = [

         'liuyan@atoptechnology.com',      //刘燕
         'yubo@atoptechnology.com',          //余波
         'tangzhiqiang@atoptechnology.com',        //唐志强
         'dingzheng@atoptechnology.com',     //丁征
         'mikechen@atoptechnology.com',      //陈应时
         'xiaxiaosen@atoptechnology.com',    //夏小森
         'liping@atoptechnology.com',    //李平
         'kent@atoptechnology.com',      //董总

	];

    # 构造方法
    public function __construct(){
        parent::__construct();
        # 获取全局task列表并赋值到类字段成员
        self::$taskConfig = json_decode($GLOBALS['taskConfig'], true);
        print_r(self::$taskConfig);
    }

    # 邮件发送
    protected static function push_eml($body,$subject){
        $http_host = self::$http_host;
        $currentYear = date('Y', time());
        # 样式
        $style = <<<STYLE
<style>
    a {
        color: #428bca;
    }
    a:hover {
        color: #2a6496;
    }
    p {
        line-height: 150%;
    }
    p.sm-dear {
        margin-bottom: 24px;
    }
    p.ck-lj, p.remark, table {
        margin-top: 24px;
    }
    table {
        font-size: 14px;
        color: #555;
        border: solid 1px #e2e2e2;
    }
    table thead tr th {
        font-size: 14px;
        text-align: left;
        color: #555;
        border-right: solid 1px #e2e2e2;
        border-bottom: solid 1px #e2e2e2;
    }
    table thead tr th:last-child {
        border-right: none;
    }
    table tbody tr td {
        font-size: 14px;
        color: #555;
        text-align: left;
        border-right: solid 1px #e2e2e2;
        border-bottom: solid 1px #e2e2e2;
        line-height: 150%;
    }
    table tbody tr:last-child td {
        border-bottom: none;
    }
    table tbody tr td:last-child {
        border-right: none;
    }
</style>
STYLE;

        // 头部
        $head = <<<HEAD
<div style="color: #555;font-size: 14px;margin: 10px 0;">
    <div style="padding-bottom: 30px;">
HEAD;

        //设置签名信息
        $sign = <<<SIGN
    </div>
    <div>
        <div style="padding: 10px 20px;background: #2a3542;font-size: 12px;color: #fff;">
            <div style="float: left; margin-right: 30px;margin-top: 18px;">
                <a href="http://61.139.89.33:8088" target="_blank" title="华拓光通信股份有限公司"><img style="border:none;" height="30" src="http://$http_host/Public/home/img/atop_logo_email.png" alt=""/></a>
            </div>
            <div style="float: left;">
                <p style="line-height: 100%;">该邮件由系统自动发送，请勿回复。</p>
                <p style="line-height: 100%;">&copy; 2014-$currentYear ATOP版权所有</p>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
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
        $mail->Body = $style.$head.$body.$sign;// 邮件正文加签名
        $mail->SMTPDebug = false;
        //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用
        if($mail->Send()){
            return 1;
        }else{
            return $mail->ErrorInfo;
        }
    }

}
