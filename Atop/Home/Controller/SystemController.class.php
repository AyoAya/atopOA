<?php
namespace Home\Controller;
/**
 * 系统
 * @author Fulwin
 * 2016-10-12
 */
class SystemController extends AuthController {
    
    //初始化页面
    public function index(){

        //print_r($siteConfig);
        $this->display();
    }

    //重写配置
    public function rewriteConfiguration(){
        if(!IS_POST) return;
        //引入配置文件
        $siteConfig = require './Atop/Common/Conf/config.php';
        if(!empty(I('post.key')) && I('post.key')=='LOGIN_VERIFY'){
            if(I('post.value')==1){
                $siteConfig['LOGIN_VERIFY'] = 1;
            }else{
                $siteConfig['LOGIN_VERIFY'] = 0;
            }
        }
        if(!empty(I('post.FileSizeLimit')) && I('post.FileSizeLimit')!=0 && !empty(I('post.UploadLimit')) && I('post.UploadLimit')!=0 && !empty(I('post.FileTypeExts')) && preg_match('/^(\*\.[a-z]+;)+$/',I('post.FileTypeExts'))){
            $siteConfig['UPLOAD_FILESIZELIMIT'] = I('post.FileSizeLimit');
            $siteConfig['UPLOAD_UPLOADLIMIT'] = I('post.UploadLimit');
            $siteConfig['UPLOAD_FILETYPEEXTS'] = I('post.FileTypeExts');
            $str = "<?php \r\n return ";
            //重写配置并取消对配置文件的特殊字符转义
            file_put_contents('./Atop/Common/Conf/config.php',stripslashes($str.var_export($siteConfig,true).';'));
            echo 1;exit;
        }
        if(!empty(I('post.pageLimitSize')) && I('post.pageLimitSize')!=0 && I('post.pageLimitSize')>=10 && I('post.pageLimitSize')<=50){
            $siteConfig['LIMIT_SIZE'] = I('post.value');
        }
        if(!empty(I('post.cookieKey')) && strlen(I('post.cookieKey'))>=6 && I('post.pageLimitSize')<=40){
            $siteConfig['MD5_KEY'] = I('post.value');
        }
        if(!empty(I('post.key')) && I('post.key')=='VERIFY_USECURVE'){
            if(I('post.value')==1){
                $siteConfig['VERIFY_USECURVE'] = true;
            }else{
                $siteConfig['VERIFY_USECURVE'] = false;
            }
        }
        if(!empty(I('post.key')) && I('post.key')=='VERIFY_USENOISE'){
            if(I('post.value')==1){
                $siteConfig['VERIFY_USENOISE'] = true;
            }else{
                $siteConfig['VERIFY_USENOISE'] = false;
            }
        }
        if(!empty(I('post.key')) && I('post.key')=='VERIFY_USEIMGBG'){
            if(I('post.value')==1){
                $siteConfig['VERIFY_USEIMGBG'] = true;
            }else{
                $siteConfig['VERIFY_USEIMGBG'] = false;
            }
        }
        if(!empty(I('post.key')) && I('post.key')=='PAGE_STATUS_INFO'){
            if(I('post.value')==1){
                $siteConfig['PAGE_STATUS_INFO'] = true;
            }else{
                $siteConfig['PAGE_STATUS_INFO'] = false;
            }
        }
        //重写文件头
        $str = "<?php \r\n return ";
        //重写配置并取消对配置文件的特殊字符转义
        file_put_contents('./Atop/Common/Conf/config.php',stripslashes($str.var_export($siteConfig,true).';'));
        echo I('post.value') ? 1 : 0;exit;
    }
    
    //ajax切换皮肤
    public function changetheme(){
        if(!IS_AJAX) return;
        if(!I('post.id')) return;
        $user = M('User');
        if($user->where('id='.I('post.id'))->save(array('theme'=>I('post.theme')))){
            $this->ajaxReturn('true');
        }else{
            $this->ajaxReturn('false');
        }
    }
    
    //测试数据库迁移问题
    public function test(){
        //测试用数据
        $data = array(
            'cc_time'=>date('Y/m/d',time()),
            'salesperson'=>'Will',
            'customer'=>'ATOP Europe',
            'sale_order'=>'MV215427464835',
            'pn'=>'10G SFP+',
            'vendor'=>'Brocade',
            'model'=>'',
            'error_message'=>'交换机不识别',
            'reason'=>'经Jim确认，HP兼容应该使用定制固件，估计是固件斑斑错误。',
            'comments'=>'',
            'status'=>'0',
        );
        //连接到数据库
        $customer = M('Oacustomercomplaint');
        if(!!$result = $customer->add($data)){
            echo '数据新增成功，返回id为：'.$result;
        }else{
            echo '数据新增失败';
        }
    }
    
}