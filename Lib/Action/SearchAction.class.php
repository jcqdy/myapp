<?php
import('ORG.Util.Date');
/**
 *搜索商家信息的类
 */ 
class SearchAction extends Action{

    public $page_num;          //当前页数
    public $page_size=15;      //每页数据数量
    public $search=array();
    public $consumerid;
/**
 *搜索引擎主方法
 */
    public function search(){
        $this->consumerid=$this->_param('id');
        $str=$this->_param('search');
       $str='南京 苏宁';
//        if(!$str){echo '11';}
        $this->search=explode(' ', $str);

        $num_arr=count($arr);
        if ($num_arr=1) {
            $this->redisFind($this->search['0']);
        }elseif($num_arr=2){
            $this->redisFind($this->search['0'],$this->search['1']);
        }elseif ($num_arr=3) {
            $this->redisFind($this->search['0'],$this->search['1'],$this->search['2']);
        }
    }

/**
 *从redis缓存中搜索数据的方法
 */  
    public function redisFind($n,$m,$p){
//        var_dump($n);
        $redis=new Redis();
        $redis->connect('localhost','6379');
//        $key=$redis->keys('*苏宁*');
        if($n && !$m && !$p){
            $key=$redis->keys('*'.$n.'*');
            if(!empty($key)){
             foreach ($key as $value) {
                 $this->zset($value,$redis);
             }  
             $this->zget($redis);
        }elseif (empty($key)) {
             $this->mysqlFind($n,$m,$p);
        }  
        }elseif ($n && $m && !$p) {
            $key=$redis->keys('*'.$n.'*'.$m.'*');
            if(!empty($key)){
             foreach ($key as $value) {
                 $this->zset($value,$redis);
             }  
             $this->zget($redis);
        }elseif (empty($key)) {
             $this->mysqlFind($n,$m,$p);
        }                 
        }elseif ($n && $m && $p) {
            $key=$redis->keys('*'.$n.'*'.$m.'*'.$p.'*');
            if(!empty($key)){
             foreach ($key as $value) {
                 $this->zset($value,$redis);
             }  
             $this->zget($redis);
        }elseif (empty($key)) {
             $this->mysqlFind($n,$m,$p);
        }  
        }elseif (!$n && !$m && !$p) {
            echo '请输入搜索条件';
        }        
    }

/**
 *第一次从数据库mysql获取数据的方法
 */ 
    public function mysqlFind($n,$m,$p){
//        $n='苏宁';
        $time=date('Y-m-d H:i:s');
        $oldtime=date('Y-m-d H:i:s',strtotime("-1 month"));
        $oldertime=date('Y-m-d H:i:s',strtotime("-6 month"));
        
        if($n && !$m && !$p){
            $consumer=M('Service');
            $condition['shopname']=array('like',array('%'.$n.'%'),'OR');
            $condition['address']=array('like',array('%'.$n.'%'),'OR');
            $condition['sertype']=array('like',array('%'.$n.'%'),'OR');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $result=$consumer->where($map)->order('uptime desc')->field('id,shopname,address,face,sertype')->limit($this->page_size)->select();
            var_dump($result);
            if(empty($result)){
                echo '搜索不到';
            }elseif (!empty($result)) {
                $lastarr=$this->sqlurlcode($result);
            }
            echo urldecode(json_encode($lastarr));  
        }elseif ($n && $m && !$p) {
            $consumer=M('Service');
            $condition['shopname']=array('like',array('%'.$n.'%','%'.$m.'%'),'OR');
            $condition['address']=array('like',array('%'.$n.'%','%'.$m.'%'),'OR');
            $condition['sertype']=array('like',array('%'.$n.'%','%'.$m.'%'),'OR');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $result=$consumer->where($map)->order('uptime desc')->limit($this->page_size)->select();
            if(empty($result)){
                echo '搜索不到';
            }elseif (!empty($result)) {
                $lastarr=$this->sqlurlcode($result);
            }
            echo urldecode(json_encode($lastarr));  
        }elseif ($n && $m && $p) {
            $consumer=M('Service');
            $condition['shopname']=array('like',array('%'.$n.'%','%'.$m.'%','%'.$p.'%'),'OR');
            $condition['address']=array('like',array('%'.$n.'%','%'.$m.'%','%'.$p.'%'),'OR');
            $condition['sertype']=array('like',array('%'.$n.'%','%'.$m.'%','%'.$p.'%'),'OR');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $result=$consumer->where($map)->order('uptime desc')->limit($this->page_size)->select();
            if(empty($result)){
                echo '搜索不到';
            }elseif (!empty($result)) {
                $lastarr=$this->sqlurlcode($result);
            } 
            echo urldecode(json_encode($lastarr));            
        }                 
    }

/**
 *从数据库mysql中获取的数组中字符串转码的方法(json)
 */
    public function sqlurlcode($variable){
        $sqlarr=array();
        $lastarr=array();
        foreach ($variable as $value) {
            $arrcode=$this->urlcode($value);
            array_push($sqlarr,$arrcode);               
        }
        $lastarr['consumer']=$sqlarr;
        return $lastarr;
    }

/**
 *数组中字符串转码的方法(json)
 */ 
    public function urlcode($variable){
        foreach ($variable as $key => $value) {
            $result=urlencode($value);
            $arrcode[$key]=$result;
        }
        return $arrcode;
    }

/**
 *将商家信息转为json格式的方法
 */
    public function jsonMaker($arr,$redis){
        $array4=array();
        foreach ($arr as $zvalue) {
                $array=$redis->hGetAll($zvalue);
                $arrcode=$this->urlcode($array);
                array_push($array4,$arrcode);
            } 
        $array5['consumer']=$array4;
        echo urldecode(json_encode($array5));
    }

/**
 *将查询结果导入有序集合sort set的方法
 */
    public function zset($value){
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $redis->delete('service'.session('id'));
        $zarr=explode('$', $value);
//        var_dump($zarr);
        $score=$zarr['1'];
        $redis->zAdd('service'.'5',$score,$value);
//        echo $redis->zSize('consumer');
    }

/**
 *在有序集合sort set中按照时间戳导出最新的数据的方法
 */
    public function zget(){
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $start=time();
        $zarr=$redis->zRevRange('service'.'5',0,15);
        $this->jsonMaker($zarr,$redis);

    }

/**
 *分页，再次请求redis搜索数据的方法
 */
    public function zgetMore($redis){
        $this->page_num=$this->_param('num');     
        $page_load=($this->page_num-1)*$this->page_size;
        $zarr=$redis->zRevRange('service'.session('id'),$page_load,$this->page_size);
        $this->jsonMaker($zarr,$redis);
    }

/**
 *分页时从数据库mysql获取数据的方法
 */
     public function mysqlFinds(){
        $this->page_num=$this->_param('num');
        $page_load=($this->page_num-1)*$this->page_size;
        $n=$this->search['0'];
        $m=$this->search['1'];
        $p=$this->search['2'];
        $time=date('Y-m-d H:i:s');
        $oldtime=date('Y-m-d H:i:s',strtotime("-1 month"));
        $oldertime=date('Y-m-d H:i:s',strtotime("-6 month"));        
        if($n && !$m && !$p){
            $consumer=M('Service');
            $condition['shopname']=array('like',array('%'.$n.'%'),'OR');
            $condition['address']=array('like',array('%'.$n.'%'),'OR');
            $condition['sertype']=array('like',array('%'.$n.'%'),'OR');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $result=$consumer->where($map)->order('uptime desc')->limit($page_load,$this->page_size)->select();
            $lastarr=$this->sqlurlcode($result);
        }elseif ($n && $m && !$p) {
            $consumer=M('Service');
            $condition['shopname']=array('like',array('%'.$n.'%','%'.$m.'%'),'OR');
            $condition['address']=array('like',array('%'.$n.'%','%'.$m.'%'),'OR');
            $condition['sertype']=array('like',array('%'.$n.'%','%'.$m.'%'),'OR');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $result=$consumer->where($map)->order('uptime desc')->limit($page_load,$this->page_size)->select();
            $lastarr=$this->sqlurlcode($result);
        }elseif ($n && $m && $p) {
            $consumer=M('Service');
            $condition['shopname']=array('like',array('%'.$n.'%','%'.$m.'%','%'.$p.'%'),'OR');
            $condition['address']=array('like',array('%'.$n.'%','%'.$m.'%','%'.$p.'%'),'OR');
            $condition['sertype']=array('like',array('%'.$n.'%','%'.$m.'%','%'.$p.'%'),'OR');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $result=$consumer->where($map)->order('uptime desc')->limit($page_load,$this->page_size)->select();
            $lastarr=$this->sqlurlcode($result);
        }               
        echo urldecode(json_encode($lastarr));     
    }

/**
 *分页请求接收处理主方法
 */   
    public function paging(){
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $pag=$redis->exists('service'.session('id'));
        if($pag=true){
            $this->zgetMore($redis);
        }elseif ($pag=false) {
            $this->mysqlFinds();
        }
    }

/**
 *点击查看商家主页方法
 */
    public function watch(){
        $photo=array();
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $id=$this->_param('id');
        $id='1';
        $User=M('Serviceinfo');
        $condition['serviceid']=$id;
        $result=$User->where($condition)->field('favorable,information,favtime,infotime')->find();
        $img['serviceid']=$id;
        $image=M('Image');
        $imgarr=$image->where($img)->order('uptime desc')->limit('0,8')->field('imgurl1,photoid')->select();
//        $hkey=$redis->keys('('.$id.')'.'*');这句正式使用使用时要把注释符'//'删掉，把下面那句也删掉，用这句
        $hkey=$redis->keys($id.'*');
        $array=$redis->hGetAll($hkey['0']);
        foreach ($result as $key => $value) {
            $up[$key]=$value;
        }
        foreach ($array as $key => $value) {
            $up[$key]=$value;                        
        }
        foreach ($alb as $key => $value) {
            $up[$key]=$value;
        }
        $up=$this->urlcode($up);
        if ($up['watch']>1000) {
                $num=floor($up['watch']/100)/10;
                $up['watch']=$num.'K';
            }
        $up['photo']=$imgarr;
        $con['consumer']=$up;
        echo urldecode(json_encode($con,JSON_UNESCAPED_SLASHES));
    }

    
    public function test(){
        $up['info']='招杂工3名，待遇面议';
        $up['favorable']='每日晚上6:00-8:00,打8折';
        $up['face']='http://192.168.1.100/myapp/Public/image/7$.jpg';
        $up['shopname']='苏宁电器(南京市鼓楼区湖南路店)';
        $up['address']='江苏省南京市白下区淮海路68号';
        $up['phone']='862584418888';
        $photo['0']=array(
                'imgurl1'=>'http://192.168.1.100/myapp/Uploads/image/1/13782154331s.jpg','photoid'=>'1');
        $photo['1']=array(
                'imgurl1'=>'http://192.168.1.100/myapp/Uploads/image/1/13782156862s.jpg','photoid'=>'2');
        $photo['2']=array(
                'imgurl1'=>'http://192.168.1.100/myapp/Uploads/image/1/13782154973s.jpg','photoid'=>'3');
        $photo['3']=array(
                'imgurl1'=>'http://192.168.1.100/myapp/Uploads/image/1/13782155104s.jpg','photoid'=>'4');
        $photo['4']=array(
                'imgurl1'=>'http://192.168.1.100/myapp/Uploads/image/1/13782156655s.jpg','photoid'=>'5');
        $photo['5']=array(
                'imgurl1'=>'http://192.168.1.100/myapp/Uploads/image/1/13782156956s.jpg','photoid'=>'6');
        $photo['6']=array(
                'imgurl1'=>'http://192.168.1.100/myapp/Uploads/image/1/13782155257s.jpg','photoid'=>'7');
        $photo['7']=array(
                'imgurl1'=>'http://192.168.1.100/myapp/Uploads/image/1/13782155308s.jpg','photoid'=>'8');        
//        var_dump($photo);
        $up=$this->urlcode($up);
        $up['photo']=$photo;
        $con['consumer']=$up;
        echo urldecode(json_encode($con,JSON_UNESCAPED_SLASHES));
    }

    public function test2(){
        $array=array();
        $photo['0']=array(
                'imgurl2'=>'http://192.168.1.100/myapp/Uploads/image/1/13782154331.jpg','photoid'=>'1','state'=>'12345');
        $photo['1']=array(
                'imgurl2'=>'http://192.168.1.100/myapp/Uploads/image/1/13782156862.jpg','photoid'=>'2','state'=>'12345');
        $photo['2']=array(
                'imgurl2'=>'http://192.168.1.100/myapp/Uploads/image/1/13782154973.jpg','photoid'=>'3','state'=>'12345');
        $photo['3']=array(
                'imgurl2'=>'http://192.168.1.100/myapp/Uploads/image/1/13782155104.jpg','photoid'=>'4','state'=>'12345');
        $photo['4']=array(
                'imgurl2'=>'http://192.168.1.100/myapp/Uploads/image/1/13782156655.jpg','photoid'=>'5','state'=>'12345');
        $photo['5']=array(
                'imgurl2'=>'http://192.168.1.100/myapp/Uploads/image/1/13782156956.jpg','photoid'=>'6','state'=>'12345');
        $photo['6']=array(
                'imgurl2'=>'http://192.168.1.100/myapp/Uploads/image/1/13782155257.jpg','photoid'=>'7','state'=>'12345');
        $photo['7']=array(
                'imgurl2'=>'http://192.168.1.100/myapp/Uploads/image/1/13782155308.jpg','photoid'=>'8','state'=>'12345');
        foreach ($photo as $value) {
            foreach ($value as $key => $value2) {
                if($key!='state'){
                    $con[$key]=$value2;
                }else{
                    $con[$key]=urlencode($value2);
                }
            }
            array_push($array,$con);
        }
        $up['photo']=$array;
        echo json_encode($up,JSON_UNESCAPED_SLASHES);
    }

    
}