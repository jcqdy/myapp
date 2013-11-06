<?php
require ("./Common/sphinxapi.php");
class ShareAction extends Action{

    public function sharePage(){
        $id=$this->_param('id');
        if($id){
            $photo=array();
            $redis=new Redis();
            $redis->connect('localhost','6379');
            $User=M('Serviceinfo');
            $condition['serviceid']=$id;
            $result=$User->where($condition)->field('favorable,information,favtime,infotime')->find();
            $hkey=$redis->keys('<'.$id.'>'.'*');
            $array=$redis->hGetAll($hkey['0']);
           
            $this->assign('shopname',$array['shopname']);
            $this->assign('address',$array['address']);
            $this->assign('phone_num',$array['phone_num']);
            $this->assign('face',$array['face']);
            $this->display();
        }
    }
}