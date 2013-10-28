<?php
class GearmanAction extends Action{

    public function mysqlClient(){
/*        $client=new GearmanClient();
        $client->addServer();   */
        $User=M('Service');       
        $result=$User->select();
//        var_dump($result);
        foreach ($result as $key) {
    //        var_dump($key);
            $redis=new Redis();
            $redis->connect('localhost','6379');
            $watch=$redis->sCard($key['id']);   //从redis sort中获得关注数量
            if ($watch>1000) {
                $num=floor($watch/100)/10;
                $watch=$num.'K';
            }
            $array=array();              
            $array['shopname']=$key['shopname'];
            $array['address']=$key['address'];
            $array['face']=$key['face'];
            $array['sertype']=$key['sertype'];
            $array['watch']=$watch;
            $array['uptime']=strtotime($key['uptime']);
            $array['id']=$key['id'];
            $array['latitude']=$key['latitude'];
            $array['longitude']=$key['longitude'];
            $array['city']=$key['city'];
//            var_dump($array); 
//            $client->doBackground('sendmysql',serialize($array));
        }
    }

}