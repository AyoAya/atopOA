<?php

# 屏蔽所有报错
error_reporting(0);

# 设置默认时间区
date_default_timezone_set('PRC');

# 导入任务列表
require __DIR__ . '/task.config.php';

class DB {
    # 定义数据库配置
    protected $db_host = 'localhost:3306';
    protected $db_user = 'root';
    protected $db_pwd = 'root';
    protected $db_name = 'atop';

    # task列表
    protected static $taskConfig = null;

    # 定义服务器真实地址
    protected static $http_host = '61.139.89.33:8088';

    # 定义数据库资源句柄
    protected static $mysqli;

    # 构造方法
    public function __construct(){
        # 获取全局task列表并赋值到类字段成员
        self::$taskConfig = json_decode($GLOBALS['taskConfig'], true);
        $this->connect();
    }

    # 连接数据库并返回实例
    protected function connect(){
        # 连接数据库并返回资源句柄
        self::$mysqli = mysqli_connect( $this->db_host, $this->db_user, $this->db_pwd, $this->db_name );
        # 设置连接字符集
        self::$mysqli->query('set names utf8');
    }


    # 查询
    protected static function select( $sql ){
        # 定义空数组作用存放结果集数据
        $tmpArr = [];
        # 执行sql语句并得到结果集
        $result = self::$mysqli->query( $sql );
        if( $result ){
            # 遍历结果集
            while( $row = $result->fetch_assoc() ) {
                $tmpArr[] = $row;
            }
        }
        # 返回查询数据
        return $tmpArr;
    }

}
