<?php
/*    $worker = new GearmanWorker();
    $worker->addServer(); 
    $worker->addFunction('sendmysql', 'doSendmysql');       
    while($worker->work());
    function doSendmysql($job){
        $array=unserialize($job->workload());
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $hkey='<'.$array['id'].'>'.$array['shopname'].$array['address'].$array['sertype'].$array['city'].'$'.time().'$'.$array['latitude'].'$'.$array['longitude'];
        $result=$redis->keys('<'.$array['id'].'>'.'*');
        if($result){
            $redis->rename($result['0'],$hkey);
            foreach ($array as $key => $value) {
                $redis->hSet($hkey,$key,$value);
            }       
        }elseif (!$result) {
            foreach ($array as $key => $value) {
                $redis->hSet($hkey,$key,$value);
            }
        }
             
    }