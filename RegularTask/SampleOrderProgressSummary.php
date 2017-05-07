<?php

# 屏蔽所有报错
#error_reporting(0);

# 设置默认时间区
date_default_timezone_set('PRC');


class PushEmail {

    # 定义数据库配置
    private $db_host = 'localhost:3306';
    private $db_user = 'root';
    private $db_pwd = 'root';
    private $db_name = 'atop';

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

    # 查询本周数据
    public function GetOrderSummary(){

        # 获取到本周一时间戳
        $start_date = strtotime(date('Y-m-d',(time()-((date('w')==0?7:date('w'))-1)*24*3600)));

        # 汇总当时时间
        $end_date = time();

        # 获取本周的订单数据
        $sql = 'SELECT * FROM atop_sample WHERE create_time > '.$start_date.' AND create_time < '.$end_date.' ORDER BY create_time ASC';

        $sample_data = self::select($sql);

        foreach( $sample_data as $key=>&$value ){

            $sql = 'SELECT 
                        a.id detail_id, a.pn, a.count, a.customer, a.brand, a.model, a.note, a.requirements_date, a.expect_date, a.actual_date, a.state, a.now_step, c.type, d.nickname, b.name
                      FROM 
                        atop_sample_detail a, atop_sample_step b, atop_productrelationships c, atop_user d
                      WHERE 
                        detail_assoc = '.$value['id'].' AND a.product_id = c.id AND a.manager = d.id AND a.now_step = b.id';


            $value['detail'] = self::select($sql);

            foreach( $value['detail'] as $k=>&$v ){

                # 如果产品步骤大于6则说明该单已经完成
                if( $v['now_step'] > 6 ){
                    $v['class'] = 'success';
                }else{
                    $v['class'] = 'processing';
                }

            }

        }

        self::output( $sample_data );

    }

    private static function output( $data ){

        $style = <<<STYLE
<style>
.title {
    border: solid 1px #ccc;
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
.table thead tr th {
    border-right: solid 1px #ccc;
    padding: 12px 15px;
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

    $html = '<div class="title">华拓'.date('Y',time()).'年第'.date('W',time()).'周样品订单进度汇总</div>';

    $html .= "\r\n<table class='table' cellpadding='0' cellspacing='0'>
    <thead>
        <tr>
            <th>序号</th>
            <th>订单号</th>
            <th>销售</th>
            <th>产品型号</th>
            <th>产品经理</th>
            <th>要求交期</th>
            <th>实际交期</th>
            <th>模块数量</th>
            <th>客户名称</th>
            <th>设备品牌</th>
            <th>设备型号</th>
            <th>当前进度</th>
        </tr>
    </thead>
    <tbody>\r\n";

        foreach( $data as $key=>&$value ){

            $html .= "\t\t<tr>\r\n";

            $html .= "\t\t\t<td rowspan='".count($value['detail'])."'>".($key+1)."</td>\r\n";

            $html .= "\t\t\t<td rowspan='".count($value['detail'])."'>".$value['order_num']."</td>\r\n";

            $html .= "\t\t\t<td rowspan='".count($value['detail'])."'>".$value['create_person_name']."</td>\r\n";

            foreach( $value['detail'] as $k=>&$v ){

                if( $k == 0 ){

                    $html .= "\t\t\t<td>".$v['pn']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['nickname']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['requirements_date']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['actual_date']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['count']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['customer']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['brand']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['model']."</td>\r\n";

                    if( $v['state'] == 'N' ){

                        if( $v['class'] == 'processing' ){

                            $html .= "\t\t\t<td><span class='".$v['class']."'>".$v['name']."</span></td>\r\n";

                        }else{

                            $html .= "\t\t\t<td><span class='".$v['class']."'>".$v['name']."</span></td>\r\n";

                        }

                    }else{

                        $html .= "\t\t\t<td><span class='danger'>".$v['name']."</span></td>\r\n";

                    }

                    $html .= "\t\t</tr>\r\n";

                }else{

                    $html .= "\t\t<tr>\r\n";

                    $html .= "\t\t\t<td>".$v['pn']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['nickname']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['requirements_date']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['actual_date']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['count']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['customer']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['brand']."</td>\r\n";

                    $html .= "\t\t\t<td>".$v['model']."</td>\r\n";

                    if( $v['state'] == 'N' ){

                        if( $v['class'] == 'processing' ){

                            $html .= "\t\t\t<td><span class='".$v['class']."'>".$v['name']."</span></td>\r\n";

                        }else{

                            $html .= "\t\t\t<td><span class='".$v['class']."'>".$v['name']."</span></td>\r\n";

                        }

                    }else{

                        $html .= "\t\t\t<td><span class='danger'>".$v['name']."</span></td>\r\n";

                    }

                    $html .= "\t\t</tr>\r\n";

                }

            }


        }

        $html .= "\t</tbody>\r\n</table>";

        echo $style.$html;

        //print_r($data);

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