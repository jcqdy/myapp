<?php
/**
 *关于Redis处理消息队列和缓存的类
 */
class RedisAction extends Action{

    public $list;

    public $array=array();

/**
 *商家用户注册信息导入消息队列的方法
 */   
    public function pushRegister(){
      $redis=new Redis();                  
      $redis->connect('localhost', '6379');
      $image=new ImageAction();
//      $image->uploadRegister();

      $array=array(         
          'email'=>'jcq@qa.com',
          'pass'=>'sttf215',
          'shopname'=>'苏宁电器(南京市鼓楼区湖南路店)',
          'address'=>'江苏省南京市白下区淮海路68号',
          'face'=>'http://192.168.1.100/myapp/Public/image/13$.jpg',
          'sertype'=>'商场',
          'latitude'=>$latitude,  //经度
          'longitude'=>$longitude,   //纬度        
          'phone_num'=>'862584418888', 
          'city'=>'上海',           
        );
//    $this->list=$array['shopname'];
      foreach ($array as $value) {
        $redis->lPush('key1',$value);
      }
    }

/**
 *商家用户注册信息导出消息队列的方法
 */  
    public function popRegister(){
      $redis=new Redis();
      $redis->connect('localhost', '6379'); 
      for($i=0;$i<11;$i++){
        $result=$redis->rPop('key1');
        $this->array[$i]=$result;          
      }           
    }

/**
 *商家用户注册信息存入缓存的方法
 */ 
    public function hashSet($id,$con){
      $redis=new Redis();
      $redis->connect('localhost','6379');
      $hkey=$id.$this->array['2'].$this->array['3'].$this->array['5'].$this->array['9'].'$'.time().'$'.$this->array['6'].'$'.$this->array['7'];
      var_dump($hkey);
      for ($i=2;$i<9;$i++) {       
         $redis->hSet($hkey,$con[$i],$this->array[$i]);
       } 
      $redis->hSet($hkey,'id',$id);
      $watch=$redis->sCard('id');
      $redis->hSet($hkey,'watch',$watch);
    }



   

}
