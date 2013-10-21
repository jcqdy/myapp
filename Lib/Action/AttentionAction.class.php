<?php
/**
 *这个类是消费者用户关注商家的控制器
 */
class AttentionAction extends Action{

/**
 *这是处理消费者用户关注商家的方法
 */   
    public function consumerAttention(){
        $redis=new Redis();
        $redis->connect('localhost','6379'); 
        $serviceid=$this->_param('serviceid');
        $consumerid=$this->_param('id');
        $att=M('Attention');
        $condition['serviceid']=$serviceid;
        $condition['consumerid']=$consumerid;
        $result=$att->where($condition)->find();
        if(empty($result)){
            $att->where($condition)->add();
        }  
        $redis->sAdd($serviceid,$consumerid);
        $watch=$redis->sCard($serviceid);  
        $key=$redis->keys('('.$value.')'.'*');  
        $redis->hSet($key,'watch',$watch);
    }

/**
 *这是统计输出消费者用户的关注列表的方法
 */
    public function myAttention(){
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $search=new SearchAction();
        $my=M('Attention');
        $condition['consumerid']='5';//session('id');
        $result=$my->where($condition)->getfield('serviceid',true);
        var_dump($result);
        $array=array();
        foreach ($result as $value) {
            $key=$redis->keys('('.$value.')'.'*');      
            foreach ($key as $value2) {
                $hash=$redis->hGetAll($value2);
                $hash=$search->urlcode($hash);
                array_push($array,$hash);
            }
        }
        $array2['attention']=$array;
        echo urldecode(json_encode($array2));
    }

/**
 *这是取消关注的方法
 */ 
    public function cancelAttention(){
        $serviceid=$this->_param('serviceid');
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $consumerid=$this->_param('id');
        $condition['serviceid']=$serviceid;
        $condition['consumerid']=$consumerid;
        if ($serviceid) {
            $cacel=M('Attention');
            $cacel->where($condition)->delete();
            $redis->sRem($serviceid,$consumerid);
        }
    }

/**
 *这是记录商家被浏览次数的方法
 */ 
    public function visitors(){
        $serviceid=$this->_param('serviceid');
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $num=$redis->get('visitors'.$serviceid);
        $num=$num++;
        $redis->set('visitors'.$serviceid,$num);
    }

    



}