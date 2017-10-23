<?php
namespace Home\Controller;
/**
 * 后台首页（初始化数据）
 * @author Fulwin
 * 2016-10-8
 */
class IndexController extends AuthController {
    private $dcc = [];
    
    //初始化页面
    public function index(){

        $rel = M('Software')->where('name = "OA System"')->find();

        $result = M('SoftwareLog')->where('soft_asc = '.$rel['id'])->order('save_time DESC ')->find();

        $this->assign('result',$result);
        $this->display();
    }

    //退出登录
    public function logout(){
        //删除cookie
        cookie('UserLoginInfo',null);
        //删除session
        session('[destroy]');
        //跳转了到登陆页
        $this->redirect('Login/index');
    }

    public function export(){
        // 指定根目录
        $root_path = './Uploads/File/ISO9001';
        $result = $this->getfiles($root_path);
        //print_r($result);
        $this->render($result);
    }

    private function render($result, $dir = ''){
        $result = $dir ? $result[$dir] : $result;
        foreach( $result as $key=>&$value ){
            if( is_array($value) ){
                $this->render($value);
            }else{
                $filename = basename($value);   // 提取文件名
                if( preg_match('/[\s]+/i', $filename) ){    //检测文件名中是否包含空格
                    $tmpArr = explode(' ', $filename);  // 将文件名以空格拆分开
                    $data['type'] = 'ISO';
                    $data['filenumber'] = substr($tmpArr[0], 0, -2);  // 获取到文件编号
                    $data['state'] = 'Archiving';
                    $data['version'] = substr($tmpArr[0], strlen($tmpArr[0])-2);
                    $tmp['ext'] = getExtension($value);
                    $tmp['name'] = $filename;
                    $tmp['path'] = $value;
                    $data['attachment'] = json_encode($tmp, JSON_UNESCAPED_UNICODE);
                    $data['description'] = $this->generateDescription(pathinfo($value)['filename']);
                    $data['createtime'] = filemtime(iconv('UTF-8', 'GB2312//IGNORE', $value));
                    $data['createuser'] = 30;
                    echo M()->table(C('DB_PREFIX').'file_number')->add($data);
                }else{
                    // 不包含空格...
                }
            }
        }
    }

    private function getfiles($path){
        $files = [];
        //$prev_dir = basename($path);
        foreach(scandir($path) as $handle){
            if( $handle == '.' || $handle == '..' ) continue;
            $iconv_handle = iconv('GB2312', 'UTF-8//IGNORE', $handle);
            if( is_dir( $path.DIRECTORY_SEPARATOR.$handle ) ){
                $files[$iconv_handle] = $this->getfiles($path.DIRECTORY_SEPARATOR.$handle);
            }else{
                $files[] = iconv('GB2312', 'UTF-8//IGNORE', $path).DIRECTORY_SEPARATOR.$iconv_handle;
            }
        }
        return $files;
    }

    private function generateDescription($str){
        if( !preg_match('/[\s]+/i', $str) ) return $str;
        $description = '';
        $tmpArr = explode(' ', $str);
        foreach( $tmpArr as $key=>&$value ){
            if( $key ) $description .= $value;
        }
        return $description;
    }

}