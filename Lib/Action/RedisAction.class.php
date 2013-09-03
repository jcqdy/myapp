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
      $image->uploadRegister();
      
      $array=array(         
          'email'=>'jcq@qaqer.com',
          'pass'=>'sttf215',
          'shopname'=>'苏宁电器(南京市鼓楼区湖南路店)',
          'address'=>'江苏省南京市白下区淮海路68号',
          'face'=>'http://192.168.1.100/myapp/Public/image/6.jpg',
          'sertype'=>'商场',
          'phone_num'=>'862584418888',          
          'intro'=>'苏宁是中国商业企业的领先者，经营商品涵盖传统家电、消费电子、百货、日用品、图书、虚拟产品等综合品类，线下实体门店1700多家，线上苏宁易购位居国内B2C前三，线上线下的融合发展引领零售发展新趋势。',
//          'face'=>$image->$imgsrc,          
          'location'=>'上海',
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
       for($i=0;$i<9;$i++){
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
       $hkey=$id.$this->array['2'].$this->array['3'].$this->array['5'].'$'.time();
       var_dump($hkey);
       for ($i=2;$i<6;$i++) {       
          $redis->hSet($hkey,$con[$i],$this->array[$i]);
        } 
       $redis->hSet($hkey,'id',$id);
  
    }



   

}
