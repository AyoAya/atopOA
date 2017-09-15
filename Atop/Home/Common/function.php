<?php

/**
 * 得到今天是星期几
 * @return string
 */
function week(){
    switch(date('w',time())){
        case 0:
            $week = '星期日';
            break;
        case 1:
            $week = '星期一';
            break;
        case 2:
            $week = '星期二';
            break;
        case 3:
            $week = '星期三';
            break;
        case 4:
            $week = '星期四';
            break;
        case 5:
            $week = '星期五';
            break;
        case 6:
            $week = '星期六';
            break;
        default:
            $week = '';
    }
    return $week;
}

/**
 * 获取用户浏览器信息
 * @param $Agent
 * @return string
 */
function determinebrowser ($Agent) {
    $browseragent="";   //浏览器
    $browserversion=""; //浏览器的版本
    if (ereg('MSIE ([0-9].[0-9]{1,2})',$Agent,$version)) {
        $browserversion=$version[1];
        $browseragent="Internet Explorer";
    } else if (ereg( 'Opera/([0-9]{1,2}.[0-9]{1,2})',$Agent,$version)) {
        $browserversion=$version[1];
        $browseragent="Opera";
    } else if (ereg( 'Firefox/([0-9]{1,2}.[0-9]{1,2})',$Agent,$version)) {
        $browserversion=$version[1];
        $browseragent="Firefox";
    }else if (ereg( 'Chrome/([0-9]{1,2}.[0-9]{1,2})',$Agent,$version)) {
        $browserversion=$version[1];
        $browseragent="Chrome";
    }
    else if (ereg( 'Safari/([0-9]{1,2}.[0-9]{1,2})',$Agent,$version)) {
        $browseragent="Safari";
        $browserversion="";
    }
    else {
        $browserversion="";
        $browseragent="Unknown";
    }
    return $browseragent." ".$browserversion;
}

/**
 * 检测验证码
 */
function verifycode(){
    ob_clean();
    $verify = new Think\Verify();
    $verify->useCurve = C('VERIFY_USECURVE');       //是否使用混淆曲线
    $verify->useImgBg = C('VERIFY_USEIMGBG');      //是否使用背景图片
    $verify->fontttf = '4.ttf';     //验证码字体
    $verify->length = 4;            //验证码位数
    $verify->fontSize = 18;         //验证码字体大小
    $verify->useNoise = C('VERIFY_USENOISE');      //是否添加杂点
    $verify->entry(1);
}

/**
 * 检测验证码
 * @param $code
 * @param string $id
 * @return bool
 */
function check_verify ($code,$id=''){
    $verify = new Think\Verify();
    return $verify->check($code,$id);
}

/**
 * 转换时间戳
 * @param $date
 * @return false|string
 */
function conversiontime($date){
    return date('Y-m-d H:i:s',$date);
}

/**
 * 字符串截取
 * @param $sourcestr
 * @param $cutlength
 * @return string
 */
function cutstr($sourcestr,$cutlength){
    $returnstr = '';
    $i = 0;
    $n = 0;
    $str_length = strlen($sourcestr);
    $mb_str_length = mb_strlen($sourcestr,'utf-8');
    while(($n < $cutlength) && ($i <= $str_length)){
        $temp_str = substr($sourcestr,$i,1);
        $ascnum = ord($temp_str);
        if($ascnum >= 224){
            $returnstr = $returnstr.substr($sourcestr,$i,3);
            $i = $i + 3;
            $n++;
        }
        elseif($ascnum >= 192){
            $returnstr = $returnstr.substr($sourcestr,$i,2);
            $i = $i + 2;
            $n++;
        }
        elseif(($ascnum >= 65) && ($ascnum <= 90)){
            $returnstr = $returnstr.substr($sourcestr,$i,1);
            $i = $i + 1;
            $n++;
        }
        else{
            $returnstr = $returnstr.substr($sourcestr,$i,1);
            $i = $i + 1;
            $n = $n + 0.5;
        }
    }
    if ($mb_str_length > $cutlength){
        $returnstr = $returnstr . "...";
    }
    return $returnstr;
}

/**
 * 计算字符串长度
 * @param unknown $str
 * @param string $charset
 */
function strLength($str,$charset='utf-8'){
    if($charset=='utf-8') $str = iconv('utf-8','gb2312',$str);
    $num = strlen($str);
    $cnNum = 0;
    for($i=0;$i<$num;$i++){
        if(ord(substr($str,$i+1,1))>127){
            $cnNum++;
            $i++;
        }
    }
    $enNum = $num-($cnNum*2);
    $number = ($enNum/2)+$cnNum;
    return ceil($number);
}

/**
 * 根据数组中的值进行排序
 * @param $array
 * @param $key
 * @param string $order
 * @return array
 */
function arr_sort($array,$key,$order="ASC"){//asc是升序 desc是降序
    $arr_nums=$arr=array();
    foreach($array as $k=>$v){
        $arr_nums[$k]=$v[$key];
    }
    if($order=='DESC'){
        asort($arr_nums);
    }else{
        arsort($arr_nums);
    }
    foreach($arr_nums as $k=>$v){
        $arr[$k]=$array[$k];
    }
    return $arr;
}

/**
 * 生成订单号
 * @param int $number
 * @return string
 */
function generateOrderNumber($number=2){
    $order = '';
    $time = time();
    $stringArr = range('A','Z');
    for($i=0;$i<$number;$i++){
        $order.= $stringArr[mt_rand(0,count($stringArr)-1)];
    }
    return $order.mt_rand(100,999).time();
}

/**
 * 文件上传类[新]
 * @param $savePath
 * @param $subName
 * @return array|bool
 */
function upload($savePath,$subName=''){
    $upload = new \Think\Upload;
    // 设置上传路径
    $upload->savePath = $savePath;
    // 限制上传文件大小为10mb
    //$upload->maxSize = 10485760;
    // 开启子目录保存 并以指定参数为子目录
    if( $subName != '' ){
        $upload->autoSub = true;
        $upload->subName = $subName;
    }else{
        $upload->autoSub = false;
    }
    // 保持上传文件名不变
    $upload->saveName = '';
    // 存在同名文件是否是覆盖
    $upload->replace = true;
    // 上传并返回结果
    $fileinfo = $upload->upload();

    if( $fileinfo ){
        $path = './Uploads'.$fileinfo['Filedata']['savepath'].$fileinfo['Filedata']['savename'];
        $name = $fileinfo['Filedata']['name'];
        $savename = $fileinfo['Filedata']['savename'];
        $ext = strtolower($fileinfo['Filedata']['ext']);
        $mime = $fileinfo['Filedata']['type'];
        $size = $fileinfo['Filedata']['size'];
        $time = time();
        return ['flag'=>1,'path'=>$path,'name'=>$name,'savename'=>$savename,'ext'=>$ext,'mime'=>$mime,'size'=>$size,'time'=>$time];
    }else{
        return ['flag'=>0,'msg'=>$upload->getError()];
    }
}


/**
 * 文件上传类[旧]
 * @param unknown $savePath
 * $savePath 定义文件上传路径(默认所有文件上传都在Uploads目录下,子目录需自定义)
 */
function FileUpload($savePath,$rename=false,$autosub=true,$fix='face_'){
    $upload = new \Think\Upload;
    //限制上传文件大小为10mb
    //$upload->maxSize = 10485760;
    $upload->savePath = $savePath;
    //$upload->exts = array('','','','','','','');
    if($rename){
        //重新命名
        $upload->saveName = $fix.uniqid().time();
    }else{
        //保留原文件名
        $upload->saveName = '';
    }
    //是否开启自动生成子目录
    if($autosub){
        $upload->autoSub = true;
    }else{
        $upload->autoSub = false;
    }
    //存在同名文件是否是覆盖
    $upload->replace = true;
    $fileinfo = $upload->upload();
    if(!$fileinfo){
        return $upload->getError();
    }else{
        return $fileinfo;
    }
}

/** 删除所有空目录
 * @param String $path 目录路径
 */
function rm_empty_dir($path){
    if(is_dir($path) && ($handle = opendir($path))!==false){
        while(($file=readdir($handle))!==false){// 遍历文件夹
            if($file!='.' && $file!='..'){
                $curfile = $path.'/'.$file;// 当前目录
                if(is_dir($curfile)){// 目录
                    rm_empty_dir($curfile);// 如果是目录则继续遍历
                    if(count(scandir($curfile))==2){//目录为空,=2是因为.和..存在
                        rmdir($curfile);// 删除空目录
                    }
                }
            }
        }
        closedir($handle);
    }
}

/**
 * 裁剪图像
 * @param $path
 * @param $name
 * @param $x
 * @param $y
 * @param $w
 * @param $h
 * @return array
 */
function cutImage($path,$name,$x,$y,$w,$h){
    $image = new \Think\Image();
    $image->open(getcwd().$path);
    $image->crop($w,$h,$x,$y)->save('./Uploads/UserFace/'.$name);
    $data['name'] = $name;
    $data['path'] = '/Uploads/UserFace/'.$name;
    return $data;
}

/**
 * 根据当前时间，得到问候语
 * @return string
 */
function get_welcome_str (){
    $t = date ("H");

    if ($t < 6){
        $time_str = "凌晨好";
    }else if ($t < 9){
        $time_str = "早上好";
    }else if ($t < 11){
        $time_str = "上午好";
    }else if ($t < 13){
        $time_str = "中午好";
    }else if ($t < 18){
        $time_str = "下午好";
    }else{
        $time_str = "晚上好";
    }
    return $time_str;
}

/**
 * 生成头像缩略图
 * @param $path
 * @param $name
 * @return string
 */
function thumbFace($path,$name){
    $image = new \Think\Image();
    $image->open(getcwd().$path);
    // 按照原图的比例生成一个最大为150*150的缩略图并保存为thumb.jpg
    $image->thumb(70,70)->save('./Uploads/UserFace/'.$name);
    return '/Uploads/UserFace/'.$name;
}

/**
 * 获取文件扩展名
 * @param unknown $file
 * @return mixed
 */
function getExtension($file){
    return strtolower(end(explode('.',$file)));
}

/**
 * 获取文件名及后缀
 * @param unknown $filename
 * @return mixed
 */
function getBasename($filename){
    return preg_replace('/^.+[\\\\\\/]/','',$filename);
}

/**
 * 获取当前页面完整URL地址
 * @return string
 */
function getUrl() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
    $path_info = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}

/**
 * 将字符串$str中的换行符用<br />替换
 * @param $str
 * @return mixed
 */
function replaceEnterWithBr ($str){
    $string = $str;
    $string = str_replace ("\n", "<br />", $string);
    $string = str_replace ("\r", "<br />", $string);
    return $string;
}

/**
 * 检测字符串中是否包含中文,如果包含中文则进行转码，不包含则直接返回
 * @param $str
 * @return string
 */
function checkZH_CN($str){
    if(preg_match("/[\x7f-\xff]/",$str)){
        return iconv('UTF-8','GB2312',$str);
    }else{
        return $str;
    }
}

/**
 * 加密函数
 * @param string $txt 需要加密的字符串
 * @param string $key 密钥
 * @return string 返回加密结果
 */
function encrypt($txt, $key = ''){
    if (empty($txt)) return $txt;
    if (empty($key)) $key = md5(C('MD5_KEY'));
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey ="-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
    $nh1 = rand(0,64);
    $nh2 = rand(0,64);
    $nh3 = rand(0,64);
    $ch1 = $chars{$nh1};
    $ch2 = $chars{$nh2};
    $ch3 = $chars{$nh3};
    $nhnum = $nh1 + $nh2 + $nh3;
    $knum = 0;$i = 0;
    while(isset($key{$i})) $knum +=ord($key{$i++});
    $mdKey = substr(md5(md5(md5($key.$ch1).$ch2.$ikey).$ch3),$nhnum%8,$knum%8 + 16);
    $txt = base64_encode(time().'_'.$txt);
    $txt = str_replace(array('+','/','='),array('-','_','.'),$txt);
    $tmp = '';
    $j=0;$k = 0;
    $tlen = strlen($txt);
    $klen = strlen($mdKey);
    for ($i=0; $i<$tlen; $i++) {
        $k = $k == $klen ? 0 : $k;
        $j = ($nhnum+strpos($chars,$txt{$i})+ord($mdKey{$k++}))%64;
        $tmp .= $chars{$j};
    }
    $tmplen = strlen($tmp);
    $tmp = substr_replace($tmp,$ch3,$nh2 % ++$tmplen,0);
    $tmp = substr_replace($tmp,$ch2,$nh1 % ++$tmplen,0);
    $tmp = substr_replace($tmp,$ch1,$knum % ++$tmplen,0);
    return $tmp;
}

/**
 * 解密函数
 * @param string $txt 需要解密的字符串
 * @param string $key 密匙
 * @return string 字符串类型的返回结果
 */
function decrypt($txt, $key = '', $ttl = 0){
    if (empty($txt)) return $txt;
    if (empty($key)) $key = md5(C('MD5_KEY'));
    $chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789-_.";
    $ikey ="-x6g6ZWm2G9g_vr0Bo.pOq3kRIxsZ6rm";
    $knum = 0;$i = 0;
    $tlen = @strlen($txt);
    while(isset($key{$i})) $knum +=ord($key{$i++});
    $ch1 = @$txt{$knum % $tlen};
    $nh1 = strpos($chars,$ch1);
    $txt = @substr_replace($txt,'',$knum % $tlen--,1);
    $ch2 = @$txt{$nh1 % $tlen};
    $nh2 = @strpos($chars,$ch2);
    $txt = @substr_replace($txt,'',$nh1 % $tlen--,1);
    $ch3 = @$txt{$nh2 % $tlen};
    $nh3 = @strpos($chars,$ch3);
    $txt = @substr_replace($txt,'',$nh2 % $tlen--,1);
    $nhnum = $nh1 + $nh2 + $nh3;
    $mdKey = substr(md5(md5(md5($key.$ch1).$ch2.$ikey).$ch3),$nhnum % 8,$knum % 8 + 16);
    $tmp = '';
    $j=0; $k = 0;
    $tlen = @strlen($txt);
    $klen = @strlen($mdKey);
    for ($i=0; $i<$tlen; $i++) {
        $k = $k == $klen ? 0 : $k;
        $j = strpos($chars,$txt{$i})-$nhnum - ord($mdKey{$k++});
        while ($j<0) $j+=64;
        $tmp .= $chars{$j};
    }
    $tmp = str_replace(array('-','_','.'),array('+','/','='),$tmp);
    $tmp = trim(base64_decode($tmp));
    if (preg_match("/\d{10}_/s",substr($tmp,0,11))){
        if ($ttl > 0 && (time() - substr($tmp,0,11) > $ttl)){
            $tmp = null;
        }else{
            $tmp = substr($tmp,11);
        }
    }
    return $tmp;
}

/**
 * 判断是否是今天
 * @param $time
 * @return int
 */
function JtColor($time){
    date_default_timezone_set('Asia/Shanghai');
    $jt=strtotime(date('Y-m-d',time()).' 00:00:00');
    if($time>$jt){
        return 1; //如果是返回1
    }else{
        return 0; //如果不是返回0
    }
}

/**
 * 时间过去多久
 * @param null $time
 * @return false|string
 */
function mdate($time = NULL) {
    $text = '';
    $time = $time === NULL || $time > time() ? time() : intval($time);
    $t = time() - $time; //时间差 （秒）
    $y = date('Y', $time)-date('Y', time());//是否跨年
    switch($t){
        case $t < 60:
            $text = '刚刚'; // 一分钟内
            break;
        case $t < 60 * 60:
            $text = floor($t / 60) . '分钟前'; //一小时内
            break;
        case $t < 60 * 60 * 24:
            $text = floor($t / (60 * 60)) . '小时前'; // 一天内
            break;
        case $t < 60 * 60 * 24 * 3:
            $text = floor($time/(60*60*24)) ==1 ?'昨天 ' . date('H:i', $time) : '前天 ' . date('H:i', $time) ; //昨天和前天
            break;
        case $t < 60 * 60 * 24 * 30:
            $text = date('m月d日 H:i', $time); //一个月内
            break;
        case $t < 60 * 60 * 24 * 365&&$y==0:
            $text = date('m月d日', $time); //一年内
            break;
        default:
            $text = date('Y年m月d日 H:i', $time); //一年以前
            break;
    }
    return $text;
}

/**
 * 获取指定日期的第一天
 * @param $date 时间
 * @return false|string
 */
function getCurMonthFirstDay($date) {
    return date('Y-m-01', strtotime($date));
}

/**
 * 获取指定日期的最后一天
 * @param $date 时间
 * @return false|string
 */
function getCurMonthLastDay($date) {
    return date('Y-m-d', strtotime(date('Y-m-01', strtotime($date)) . ' +1 month -1 day'));
}

/**
 * 对象转数组
 * @param $array 需要转换成数组的对象
 * @return array
 */
function object_to_array($array) {
    if(is_object($array)) {
        $array = (array)$array;
    } if(is_array($array)) {
        foreach($array as $key=>$value) {
            $array[$key] = object_to_array($value);
        }
    }
    return $array;
}

/**
 * 发送邮件
 * @param $address 收件人邮箱
 * @param $nickname 收件人姓名
 * @param $subject 邮件标题
 * @param $body 邮件内容
 */
function send_Email($address, $nickname='', $subject, $body,$cc = []){

    $http_host = $_SERVER['HTTP_HOST'];
    $currentYear = date('Y', time());

    $email_host=C('EMAIL_HOST');
    $email_port=C('EMAIL_PORT');
    $email_username=C('EMAIL_USERNAME');
    $email_password=C('EMAIL_PASSWORD');
    $email_from_name=C('EMAIL_FROM_NAME');

    // 样式
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

    /*<div style="padding: 20px 20px 10px 20px;background: #393d49;">
        <a href="http://61.139.89.33:8088" target="_blank" title="华拓光通信股份有限公司"><img height="30" src="http://$http_host/Public/home/img/atop_logo_email.png" alt=""/></a>
    </div>
    <div style="padding: 15px 0;border-top: dashed 1px #e2e2e2;font-size: 14px;margin-top: 50px;">$email_from_name</div>
    */

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


    vendor('phpmailer.class#phpmailer');
    vendor('phpmailer.class#smtp');
    $mail = new \PHPMailer();

    $mail->isSMTP();// 使用SMTP服务
    $mail->CharSet = "UTF-8";// 编码格式为utf8，不设置编码的话，中文会出现乱码
    $mail->Host = $email_host;// 发送方的SMTP服务器地址
    $mail->SMTPAuth = true;// 是否使用身份验证
    $mail->Username = $email_username;// 发送方的163邮箱用户名
    $mail->Password = $email_password;// 发送方的邮箱密码，注意用163邮箱这里填写的是“客户端授权密码”而不是邮箱的登录密码！
    $mail->SMTPSecure = "ssl";// 使用ssl协议方式
    $mail->Port = $email_port;// 163邮箱的ssl协议方式端口号是465/994
    $mail->setFrom($email_username,$email_from_name);// 设置发件人信息，如邮件格式说明中的发件人，这里会显示为Mailer(xxxx@163.com），Mailer是当做名字显示
    //$mail->addAddress($address,$nickname);// 设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
    if( is_array($address) ){
        if (count($address) == count($address, 1)) {  // 一维数组
            foreach($address as $value){
                $mail->addAddress($value);//设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
            }
        } else {    // 二维数组
            foreach($address as $value){
                $mail->addAddress($value['email'], $value['name']);//设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
            }
        }
    }else{
        $mail->addAddress($address,$nickname);//设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
    }
    if(is_array($cc) && !empty($cc)){
        if (count($cc) == count($cc, 1)) {  // 一维数组
            foreach($cc as $ccperson){
                $mail->addCC($ccperson);
            }
        } else {    // 二维数组
            foreach($cc as $value){
                $mail->addCC($value['email'], $value['name']);//设置收件人信息，如邮件格式说明中的收件人，这里会显示为Liang(yyyy@163.com)
            }
        }
    }else{
        $mail->addCC($cc);
    }
    //$mail->addReplyTo("oa@atoptechnology.com","华拓光通信股份有限公司");// 设置回复人信息，指的是收件人收到邮件后，如果要回复，回复邮件将发送到的邮箱地址
    //$mail->addCC("");// 设置邮件抄送人，可以只写地址，上述的设置也可以只写地址
    //$mail->addBCC("");// 设置秘密抄送人
    //$mail->addAttachment("test.jpg");// 添加附件

    $mail->IsHTML(true);
    $mail->Subject = $subject;// 邮件标题
    $mail->Body = $style.$head.$body.$sign;// 邮件正文加签名
    //$mail->AltBody = "This is the plain text纯文本";// 这个是设置纯文本方式显示的正文内容，如果不支持Html方式，就会用到这个，基本无用
    if($mail->Send()){
        return 1;
    }else{
        return $mail->ErrorInfo;
    }
}

/**
 * 字符串截取
 * @param $str
 * @param $len
 * @return string
 */
function GetPartStr($str,$len){
    $one=0;
    $partstr='';
    for($i=0;$i<$len;$i++)
    { $sstr=substr($str,$one,1);
        if(ord($sstr)>224){
            $partstr.=substr($str,$one,3);
            $one+=3;
        }elseif(ord($sstr)>192){
            $partstr.=substr($str,$one,2);
            $one+=2;
        }elseif(ord($sstr)<192){
            $partstr.=substr($str,$one,1);
            $one+=1;
        }
    }
    if(strlen($str)<$one){
        return $partstr;}else{
        return $partstr.'....';
    }
}

function cut_str($sourcestr,$cutlength)
{
    $returnstr='';
    $i=0;
    $n=0;
    $str_length=strlen($sourcestr);//字符串的字节数
    while (($n<$cutlength) and ($i<=$str_length))
    {
        $temp_str=substr($sourcestr,$i,1);
        $ascnum=Ord($temp_str);//得到字符串中第$i位字符的ascii码
        if ($ascnum>=224)    //如果ASCII位高与224，
        {
            $returnstr=$returnstr.substr($sourcestr,$i,3); //根据UTF-8编码规范，将3个连续的字符计为单个字符
            $i=$i+3;            //实际Byte计为3
            $n++;            //字串长度计1
        }
        elseif ($ascnum>=192) //如果ASCII位高与192，
        {
            $returnstr=$returnstr.substr($sourcestr,$i,2); //根据UTF-8编码规范，将2个连续的字符计为单个字符
            $i=$i+2;            //实际Byte计为2
            $n++;            //字串长度计1
        }
        elseif ($ascnum>=65 && $ascnum<=90) //如果是大写字母，
        {
            $returnstr=$returnstr.substr($sourcestr,$i,1);
            $i=$i+1;            //实际的Byte数仍计1个
            $n++;            //但考虑整体美观，大写字母计成一个高位字符
        }
        else                //其他情况下，包括小写字母和半角标点符号，
        {
            $returnstr=$returnstr.substr($sourcestr,$i,1);
            $i=$i+1;            //实际的Byte数计1个
            $n=$n+0.5;        //小写字母和半角标点等与半个高位字符宽...
        }
    }
    if ($str_length>$i){
        $returnstr = $returnstr . "...";//超过长度时在尾处加上省略号
    }
    return $returnstr;
}

/**
 * 遍历指定目录下的文件与文件夹
 * @param $dir
 * @return array
 */
function get_word($dir){
    $result = array();
    $handle = opendir($dir);
    if($handle){
        while( ($file = readdir($handle)) !== false ){
            $file = iconv("gb2312","utf-8",$file);
            if($file != '.' && $file != '..'){
                $cur_path = $dir.DIRECTORY_SEPARATOR.$file;
                if( !is_dir($cur_path) ){
                /*    $result['dir'][] = $file;
                }else{*/
                    //$result['files'][] = $file;
                    $result['filepath'][] = $dir.DIRECTORY_SEPARATOR.$file;
                }
            }
        }
    }
    return $result;
}

/**
 * 遍历指定目录下的文件与文件夹
 * @param $dir
 * @return array
 */
function read_all_dir ( $dir ){
    $result = array();
    $handle = opendir($dir);
    if ( $handle ){
        while ( ( $file = readdir ( $handle ) ) !== false ){
            if ( $file != '.' && $file != '..'){
                $cur_path = $dir . DIRECTORY_SEPARATOR . $file;
                if ( is_dir ( $cur_path ) ){
                    $result['dir'][iconv("gb2312","utf-8",$cur_path)] = read_all_dir ( $cur_path );
                }else{
                    $result['file'][] = iconv("gb2312","utf-8",$cur_path);
                }
            }
        }
        closedir($handle);
    }
    return $result;
}


/**
 * 获取目录名
 * @param unknown $file
 * @return mixed
 */
function getFolderName($path){
    return end(explode('\\',$path));
}


function traverse_document(&$arr){
    if( is_array($arr) && !empty($arr) ){
        foreach($arr as $key=>&$value){
            if( is_array($value) ){
                if($key!='dir' && $key!='file'){
                    echo '<div class="folder-icon text-primary"><i class="icon-folder-open-alt"></i>&nbsp;'.getFolderName($key).'</div>';
                }
                traverse_document($value);
            }else{
                switch(getExtension($value)){
                    case 'pdf':
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-pdf"></i>&nbsp;'.getBasename($value).'</a></li>';
                        break;
                    case 'PDF':
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-pdf"></i>&nbsp;'.getBasename($value).'</a></li>';
                        break;
                    case 'doc':
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-doc"></i>&nbsp;'.getBasename($value).'</a></li>';
                        break;
                    case 'docx':
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-doc"></i>&nbsp;'.getBasename($value).'</a></li>';
                        break;
                    case 'xlsx':
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-xls"></i>&nbsp;'.getBasename($value).'</a></li>';
                        break;
                    case 'xls':
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-xls"></i>&nbsp;'.getBasename($value).'</a></li>';
                        break;
                    case 'zip':
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-zip"></i>&nbsp;'.getBasename($value).'</a></li>';
                        break;
                    case 'rar':
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-zip"></i>&nbsp;'.getBasename($value).'</a></li>';
                        break;
                    case 'png':
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-img"></i>&nbsp;'.getBasename($value).'</a></li>';
                        break;
                    case 'jpeg':
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-img"></i>&nbsp;'.getBasename($value).'</a></li>';
                        break;
                    case 'jpg':
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-img"></i>&nbsp;'.getBasename($value).'</a></li>';
                        break;
                    case 'gif':
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-img"></i>&nbsp;'.getBasename($value).'</a></li>';
                        break;
                    default:
                        echo '<li class="file-icon" file-path="'.$value.'"><a href="'.$value.'" target="_blank" title="'.getBasename($value).'"><i class="z-icon-logo z-icon-file"></i>&nbsp;'.getBasename($value).'</a></li>';
                }
            }
        }
    }else{
        echo '&nbsp;&nbsp;&nbsp;没有数据';
    }
}
