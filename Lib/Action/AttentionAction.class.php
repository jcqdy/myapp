<?php
/**
 *这个类是消费者用户关注商家的控制器
 */
class AttentionAction extends Action{

/**
 *这是处理消费者用户关注商家的方法
 */   
    public function consumerAttention(){
        $serviceid=$this->_param('serviceid');
        $att=M('Attention');
        $condition['serviceid']=$serviceid;
        $condition['consumerid']=session('id');
        $result=$att->where($condition)->find();
        if(empty($result)){
            $att->where($condition)->add();
        }      
    }

/**
 *这是统计输出消费者用户的关注列表的方法
 */ 
    public function myAttention(){
        $my=M('Attention');
        $condition['consumerid']=session('id');
        $result=$my->where($condition)->getField('serviceid',true);
        $myatt=M('Service');
        $re=$myatt->where(array('id'=>array('in',$result)))->field(array('id','name','address','face'))->select();
        $list=json_encode($re);
    }

/**
 *这是取消关注的方法
 */ 
    public function cancelAttention(){  
        $serviceid=$this->_param('id');
        $consumerid=session('id');
        $condition['serviceid']=$serviceid;
        $condition['consumerid']=session('id');
        if ($serviceid) {
            $cacel=M('Attention');
            $cacel->where($condition)->delete();
        }
    }

    




}