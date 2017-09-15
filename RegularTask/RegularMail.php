<?php

# 屏蔽所有报错
error_reporting(0);

# 设置默认时间区
date_default_timezone_set('PRC');


//导入PHPMail邮件类
require 'E:\www\ThinkPHP\Library\Vendor\PHPMailer\class.phpmailer.php';
require 'E:\www\ThinkPHP\Library\Vendor\PHPMailer\class.smtp.php';


class RegularMail{

    # 定义数据库配置
    private $db_host = 'localhost:3306';
    private $db_user = 'root';
    private $db_pwd = 'root';
    private $db_name = 'atop';

    static $http_host = '61.139.89.33:8088';

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

        # 获取到当天时间戳
        # $start_date = strtotime(date('Y-m-d',(time()+((date('w')==0?7:date('w'))-3)*24*3600)));
        # 获取所有数据
        $sql = 'SELECT id,pj_num,pj_name,pj_describe,pj_create_time,pj_update_time,pj_create_person,pj_standard_name,pj_platform,pj_family,pj_belong,pj_participate FROM atop_project;';

        $project_data = self::select($sql);

        foreach ($project_data as $key=>&$value){

            $sql = 'SELECT * FROM atop_project_plan
                    WHERE plan_project = '.$value['id'].'
';

            $value['detail'] = self::select($sql);
            # 判断 不存在实际完成时间且存在计划完成时间 如果为真就将信息写进paln 如果不为真就删除此条信息
            foreach ($value['detail'] as $k=>&$v){
                if( empty($v['complete_time']) && $v['plan_stop_time'] != '' ){
                     $value['plan'][] = $v;
                }else{
                    unset($value['detail'][$k]);
                }

            }

        }


        # 判断所有数据中 是否有plan  如果有就保留 如果没有则删除数据
        foreach ($project_data as $ke=>&$value){
            if(!$value['plan']){
                unset($project_data[$ke]);
            }else{
                unset($value['detail']);
            }
        }

        # 需要通知的数据
        foreach ($project_data as $kk=>&$vv){
            # 将需要通知的人转换成数组
            # $vv['pj_participate'] = explode(",", $vv['pj_participate']);
            foreach ($vv['plan'] as $k=>&$v){
                if((strtotime($v['plan_stop_time'])+86400) >= time()){
                    unset($vv['plan'][$k]);
                }
            }
            if(empty($vv['plan'])){
                unset($project_data[$kk]);
            }


            # print_r($vv['pj_participate']);

             $spl = "SELECT email FROM atop_user WHERE id IN (".$vv['pj_participate'].")";

            $vv['email'] = self::select($spl);

            # 邮件通知人邮箱
            $vv['address'] = [];
            foreach ($vv['email'] as $k=>&$v){
                $vv['address'][] = $v['email'];
            }

        }


        # 数组重新排序
        sort($project_data);

         /*print_r($project_data);
         die();*/

        self::output( $project_data );


    }

    private static function output( $data ){

        $style = <<<STYLE
<style>
.title {
    padding: 15px 0;
    text-align: center;
    border-bottom: none;
    font-size: 16px;
    font-weight: 600;
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
p{
    font-size: 15px;
    font-weight: 500;
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
    color: #555;
    background: #e6e6e6;
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
.table tbody tr td .status_start{
    background: #428bca;
    display: inline;
    padding: .2em .6em .3em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
}
.table tbody tr td .status_noStart{
    background: #777; 
    display: inline;
    padding: .2em .6em .3em;
    font-size: 75%;
    font-weight: 700;
    line-height: 1;
    color: #fff;
    text-align: center;
    white-space: nowrap;
    vertical-align: baseline;
    border-radius: .25em;
}
</style>
STYLE;

        foreach ($data as $key=>&$value){

            $html = '<p>Dear All,</p>';
            $html .= '<p> '.$value['pj_num'].' '.$value['pj_name'].' 有任务延期，请关注。</p><p>详情请点击链接：<a href="http://' . self::$http_host . '/Project/details/tab/plan/id/' . $value["id"] . '">http://' . self::$http_host . '/Project/details/tab/plan/id/' . $value["id"] . '</a></p>';
            $html .= "\r\n<table class='table' cellpadding='0' cellspacing='0'>
    <thead>
        <tr>
            <th>项目号</th>
            <th>项目名称</th>
            <th>步骤</th>
            <th>里程碑</th>
            <th>节点</th>
            <th width='90'>开始时间</th>
            <th width='90'>结束时间</th>
            <th>备注</th>
            <th>进度</th>
        </tr>
    </thead>
<tbody>\r\n

";

            foreach ($value['plan'] as $k=>&$v) {


                $html .= "<tr>";
                if (count($value['plan']) > 1) {
                    if (($k + 1) < count($value['plan'])) {
                        if( $k == 0 ){
                            $html .= "<td rowspan='" . count($value['plan']) . "'><a href='http://" . self::$http_host . "/Project/details/tab/overview/id/" . $value['id'] . "'>" . $value['pj_num'] . "</a></td>
                                      <td rowspan='" . count($value['plan']) . "'>" . $value['pj_name'] . "</td>";
                        }
                    }
                } else {
                    $html .= "<td><a href='http://" . self::$http_host . "/Project/details/tab/overview/id/" . $value['id'] . "'>" . $value['pj_num'] . "</a></td>
                              <td>" . $value['pj_name'] . "</td>";
                }

                $html .= "\t\t\t<td>Gate" . $v['gate'] . "</td>\r\n
                          \t\t\t<td>" . $v['mile_stone'] . "</td>\r\n
                          \t\t\t<td>" . $v['items'] . "</td>\r\n
                          \t\t\t<td>" . $v['plan_start_time'] . "</td>\r\n
                          \t\t\t<td>" . $v['plan_stop_time'] . "</td>\r\n
                          \t\t\t<td>" . $v['comments'] . "</td>\r\n
                         ";
                if($v['status'] == 0){
                    $html .= "\t\t\t<td><span class='status_noStart'>未开始</span></td>\r\n";
                }else{
                    $html .= "\t\t\t<td><span class='status_start'>进行中</span></td>\r\n";
                }
            }

            $html .= "\t\t</tr>\r\n";

            $subject = '[项目管理] 项目延期通知';

            $html .= "\t</tbody>\r\n</table>";

            echo $style.$html;

            self::push_eml($style.$html,$subject,$value['address']);




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
    private static function push_eml($body,$subject,$address){

        sleep(1);

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













