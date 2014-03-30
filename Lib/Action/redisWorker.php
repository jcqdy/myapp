<?php
    $worker = new GearmanWorker();
    $worker->addServer(); 
    $worker->addFunction('sendmysql', 'doSendmysql');       
    while($worker->work());
    function doSendmysql($job){
        $array=unserialize($job->workload());
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $hkey='<'.$array['id'].'>'.$array['city'].$array['shopname'].$array['address'].$array['sertype'].'$'.time().'$'.$array['latitude'].'$'.$array['longitude'];
        $result=$redis->keys('<'.$array['id'].'>'.'*');
        $add['id']=$array['id'];
        $add['shopname']=$array['shopname'];
        $add['address']=$array['address'];
        $add['sertype']=$array['sertype'];
        $add['phone_num']=$array['phone_num'];
        if($result){
            $redis->rename($result['0'],$hkey);           
            $redis->hMset($hkey,$add);   
        }elseif (!$result) {
                $redis->hMset($hkey,$add);
        }
             
    }