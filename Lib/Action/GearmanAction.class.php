<?php
class GearmanAction extends Action{

    public function mysqlClient(){
        $client=new GearmanClient();
        $client->addServer();   
        $User=M('Service');       
        $result=$User->select();
        foreach ($result as $key) {
            $array=array();            
            $array['shopname']=$key['shopname'];
            $array['address']=$key['address'];
            $array['face']=$key['face'];
            $array['sertype']=$key['sertype'];
            $array['watch']=$key['watch'];
            $array['uptime']=strtotime($key['uptime']);
            $array['id']=$key['id'];
            var_dump($array); 
            $client->doBackground('sendmysql',serialize($array));
        }
    }

    public function redisWorker(){
        $worker = new GearmanWorker();
        $worker->addServer(); 
        $worker->addFunction('sendmysql', 'doSendmysql');       
        while($worker->work());
        function doSendmysql($job){
            $array=unserialize($job->workload());
            $redis=new Redis();
            $redis->connect('localhost','6379');
            $hkey=$array['id'].$array['shopname'].$array['address'].$array['sertype'].'$'.time();
            $result=$redis->keys($array['id'].'*');
            $redis->delete($result);
            foreach ($array as $key => $value) {
                $redis->hSet($result,$key,$value);
            }            
        }
    }
}