<?php

# 导入DB类
require __DIR__ .'/DB.php';

class run extends DB {

    private $scriptPath = '/scripts/';

    public function __construct(){
        # 调用父类构造获取mysql资源句柄
        parent::__construct();
        $this->getServerExecuteScriptsData();
    }

    # 获取执行脚本数据
    public function getServerExecuteScriptsData(){
        $sql = 'select * from atop_tasks';
        $tasks = self::select($sql);
        foreach($tasks as $key=>&$value){
            // 脚本状态为启用才执行
            if( $value['state'] == 'enable' ){
                if( $value['trigger'] == 'month' ){
                    $cycle = $value['days'];
                }elseif( $value['trigger'] == 'week' ){
                    $cycle = $value['weeks'];
                }else{
                    $cycle = [];
                }
                $execTimes = $this->calcExecuteTime($value['trigger'], $cycle);    // 计算执行时间
                $execTimes['id'] = $value['id'];
                if( isset($execTimes['prevExecTime']) ){    // prevExecTime即为上次执行时间同时也决定着当时是否执行，如果执行则复写该字段，否则重写下次执行时间
                    $affectRow = $this->saveExecTimeData($execTimes);
                    if( $affectRow !== false ) $this->executeScript($value['script']);
                }
            }
        }
    }

    # 更新执行时间
    private function saveExecTimeData($data){
        if( isset($data['prevExecTime']) ){
            $sql = 'update atop_tasks set prevExecTime="'.$data['prevExecTime'].'",nextExecTime="'.$data['nextExecTime'].'" where id='.$data['id'];
        }else{
            $sql = 'update atop_tasks set nextExecTime="'.$data['nextExecTime'].'" where id='.$data['id'];
        }
        $row = self::$mysqli->query($sql);
        return $row;
    }

    # 执行脚本
    private function executeScript($scriptName){
        $command = 'php '.getcwd().$this->scriptPath.$scriptName.'.php';
        $command = str_replace('\\', '/', $command);
        echo 'ready execute script: '.$command."\r\n";
        exec($command);
        echo "script execute complete!\r\n";
        sleep(1);
    }

    # 计算执行时间
    private function calcExecuteTime($trigger = '', $data = []){
        $data = $data ? explode(',', $data) : [];
        if( $trigger == 'week' ){
            $currentWeek = date('w', time());   // 当天星期几
            if( $data ){
                foreach( $data as $key=>&$value ){
                    $value = $this->weekConversion($value);     // 将星期转换为数字标识
                }
                sort($data);
                $nextExecTime = null;
                $currentExecTime = null;
                for( $i = 0; $i < count($data); $i++ ){
                    if( $data[$i] > $currentWeek ){
                        $nextExecTime = $data[$i];
                        break;
                    }
                }
                for( $i = 0; $i < count($data); $i++ ){
                    if( $data[$i] == $currentWeek ){
                        $currentExecTime = $data[$i];
                        break;
                    }
                }
                if( $nextExecTime !== null ){
                    $diff = $nextExecTime - $currentWeek;
                    $finalNextExecTime = date('Y-m-d', strtotime('+'.$diff.' day'));
                }else{
                    $diff = 7 - $currentWeek + $data[0];
                    $finalNextExecTime = date('Y-m-d', strtotime('+'.$diff.' day'));
                }
                if( $currentExecTime !== null ){
                    return ['prevExecTime'=>date('Y-m-d', time()), 'nextExecTime'=>$finalNextExecTime];
                }else{
                    return ['nextExecTime'=>$finalNextExecTime];
                }
            }
        }elseif( $trigger == 'month' ){
            $currentMonthDay = date('d', time());    // 当天几号
            $currentMonthFirstDay = date('Y-m-01', strtotime(date("Y-m-d")));
            $currentMonthEndDay = date('d', strtotime("$currentMonthFirstDay +1 month -1 day")); // 当月最后一天
            if( $data ){
                $arrIndex = array_search('最后一天', $data);
                if( $arrIndex !== false ) $data[$arrIndex] = $currentMonthEndDay;
                $data = array_unique($data);
                $monthRange = range(1, $currentMonthEndDay);
                $finalMonth = array_intersect($monthRange, $data);
                $nextExecTime = null;
                $currentExecTime = null;
                for( $i = 0; $i < count($finalMonth); $i++ ){
                    if( $finalMonth[$i] > $currentMonthDay ){
                        $nextExecTime = $finalMonth[$i];
                        break;
                    }
                }
                for( $i = 0; $i < count($finalMonth); $i++ ){
                    if( $finalMonth[$i] == $currentMonthDay ){
                        $currentExecTime = $finalMonth[$i];
                        break;
                    }
                }
                if( $nextExecTime !== null ){
                    $diff = $nextExecTime - $currentMonthDay;
                    $finalNextExecTime = date('Y-m-d', strtotime('+'.$diff.' day'));
                }else{
                    $diff = $currentMonthEndDay - $currentMonthDay + $data[0];
                    $finalNextExecTime = date('Y-m-d', strtotime('+'.$diff.' day'));
                }
                if( $currentExecTime !== null ){
                    return ['prevExecTime'=>date('Y-m-d', time()), 'nextExecTime'=>$finalNextExecTime];
                }else{
                    return ['nextExecTime'=>$finalNextExecTime];
                }
            }
        }else{
            return ['prevExecTime'=>date('Y-m-d', time()), 'nextExecTime'=>date('Y-m-d', strtotime('+1 day'))];
        }
    }

    # 星期转换数字
    private function weekConversion($weekStr){
        switch( $weekStr ){
            case '星期一':
                $weekInt = 1;
                break;
            case '星期二':
                $weekInt = 2;
                break;
            case '星期三':
                $weekInt = 3;
                break;
            case '星期四':
                $weekInt = 4;
                break;
            case '星期五':
                $weekInt = 5;
                break;
            case '星期六':
                $weekInt = 6;
                break;
            default:
                $weekInt = 7;
        }
        return $weekInt;
    }

}

$run = new run();
