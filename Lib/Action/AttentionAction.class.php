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
        $redis->sAdd('watch'.$serviceid,$consumerid);
        $redis->sAdd('fans'.$consumerid,$serviceid);
        $key=$redis->keys('<'.$serviceid.'>'.'*');
        $redis->hIncrBy($key['0'],'watch',1);
        $redis->close();
    }

/**
 *这是统计输出消费者用户的关注列表的方法
 */
    public function myAttention(){
        $consumerid=$this->_param('id');
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $search=new SearchAction();
        $result=$redis->sMembers('fans'.$consumerid);
        $array=array();
        foreach ($result as $value) {
            $key=$redis->keys('<'.$value.'>'.'*');
            foreach ($key as $value2) {
                $hash=$redis->hGetAll($value2);
                $hash=$search->urlcode($hash);
                array_push($array,$hash);
            }
        }
        $array2['attention']=$array;
        echo urldecode(json_encode($array2));
        $redis->close();
    }

/**
 *这是取消关注的方法
 */
    public function cancelAttention(){
        $serviceid=$this->_param('serviceid');
        $consumerid=$this->_param('id');
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $condition['serviceid']=$serviceid;
        $condition['consumerid']=$consumerid;
        if ($serviceid && $consumerid) {
            $cacel=M('Attention');
            $cacel->where($condition)->delete();
            $redis->sRem('watch'.$serviceid,$consumerid);
            $redis->sRem('fans'.$consumerid,$serviceid);
            $key=$redis->keys('<'.$serviceid.'>'.'*');
            $redis->hIncrBy($key['0'],'watch',-1);
        }
        $redis->close();
    }
    
}