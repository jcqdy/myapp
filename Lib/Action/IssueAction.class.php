<?php

/**
 *这个类是关于商家用户发布最新消息的控制器
 */
class IssueAction extends Action{

/**
 *商家用户发布信息的方法
 */
    public function issueRecive(){
        $json=file_get_contents('php://intput');
        $json=json_decode($json);
/*      $json=array(
                'foodmenu'=>'龙井虾仁',
                'favorable'=>'免费',
                'site'     =>'1',
                'information'=>'很好', 
            );                        */
        $issue=M('Serviceinfo');
        $condition['serviceid']=session('id');
        $n=0;  
        $result=$issue->where($condition)->find();     
        if(empty($result)){
            foreach ($json as $key => $value) {
                ++$n;
                switch ($n) {
                    case 1:                                  
                        $condition[$key]=$value;
                        $issue->add($condition);
                        unset($condition[$key]);
                        break;
                    
                    default:
                        $save[$key]=$value;
                        $issue->where($condition)->save($save);
                        break;
                }
            }         
        }elseif (!empty($result)) {
                foreach ($json as $key => $value) {
                    $save[$key]=$value;
                    $issue->where($condition)->save($save);
                }
        }                   
        
}
}

