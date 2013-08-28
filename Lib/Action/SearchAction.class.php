<?php
import('ORG.Util.Date');
/**
 *搜索商家信息的类
 */ 
class SearchAction extends Action{

    public $page_num;          //当前页数
    public $page_size=15;      //每页数据数量
    public $search=array();
/**
 *搜索引擎主方法
 */
    public function search(){
        $str=$this->_param('search');
//        $str='南京';
//        if(!$str){echo '11';}
        $this->search=explode(' ', $str);

        $num_arr=count($arr);
        if ($num_arr=1) {
            $this->redisFind($this->search['0']);
        }elseif($num_arr=2){
            $this->redisFinds($this->search['0'],$this->search['1']);
        }elseif ($num_arr=3) {
            $this->redisFinds($this->search['0'],$this->search['1'],$this->search['2']);
        }
    }

/**
 *一个关键词时redis缓存中搜索数据的方法
 */ 
    public function redisFind($n,$m,$p){
//        var_dump($n);
        $redis=new Redis();
        $redis->connect('localhost','6379');
//        $key=$redis->keys('*苏宁*');
        if($n && !$m && !$p){
            $key=$redis->keys('*'.$n.'*');
        }elseif ($n && $m && !$p) {
            $key=$redis->keys('*'.$n.'*'.$m.'*');
        }elseif ($n && $m && $p) {
            $key=$redis->keys('*'.$n.'*'.$m.'*'.$p.'*');
        }
//        var_dump($key);
        if(!empty($key)){
             foreach ($key as $value) {
                 $this->zset($value,$redis);
             }  
             $this->zget($redis);
        }elseif (empty($key)) {
             $this->mysqlFind($n,$m,$p);
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
            $result=$consumer->where($map)->order('uptime desc')->select();
            $lastarr=$this->sqlurlcode($result);
        }elseif ($n && $m && !$p) {
            $consumer=M('Service');
            $condition['shopname']=array('like',array('%'.$n.'%','%'.$m.'%'),'OR');
            $condition['address']=array('like',array('%'.$n.'%','%'.$m.'%'),'OR');
            $condition['sertype']=array('like',array('%'.$n.'%','%'.$m.'%'),'OR');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $result=$consumer->where($map)->order('uptime desc')->select();
            $lastarr=$this->sqlurlcode($result);
        }elseif ($n && $m && $p) {
            $consumer=M('Service');
            $condition['shopname']=array('like',array('%'.$n.'%','%'.$m.'%','%'.$p.'%'),'OR');
            $condition['address']=array('like',array('%'.$n.'%','%'.$m.'%','%'.$p.'%'),'OR');
            $condition['sertype']=array('like',array('%'.$n.'%','%'.$m.'%','%'.$p.'%'),'OR');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $result=$consumer->where($map)->order('uptime desc')->select();
            $lastarr=$this->sqlurlcode($result);
        }               
        echo urldecode(json_encode($lastarr));     
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
/*              $array2=$arrcode['id'];
                $array3[$array2]=$arrcode;    */
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
        $zarr=explode(' ', $value);
//        var_dump($zarr);
        $score=$zarr['2'];
        $redis->zAdd('consumer',$score,$value);
//        echo $redis->zSize('consumer');
    }

/**
 *在有序集合sort set中按照时间戳导出最新的数据的方法
 */
    public function zget(){
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $start=time();
//        var_dump($start);
//        $end=date('Y-m-d H:i:s',strtotime("-1 month"));
        $end=$start-2592000;
//        var_dump($end);
        $zarr=$redis->zRevRangeByScore('consumer',$start,$end);
//        var_dump($zarr);
        $num=count($zarr);
        if($num>0 && $num<=15){
            $end=$start-5184000;
            $zarr=$redis->zRevRangeByScore('consumer',$start,$end);
//            var_dump($zarr);
            $this->jsonMaker($zarr,$redis);
        }elseif ($num>15) {
            $znarr=array_slice($zarr,0,15);
            $this->jsonMaker($znarr,$redis);
        }
    }

/**
 *分页，再次请求redis搜索数据的方法
 */
    public function zgetMore($redis){
        $this->page_num=$this->_param('num');     
        $page_load=($this->page_num-1)*$this->page_size;
        $start=time();
        $end=$start-2592000;
        $zarr=$redis->zRevRangeByScore('consumer',$start,$end);
        $znarr=array_slice($zarr,$page_load,$this->page_size);
        $num=count($zarr);
        if ($num<15) {
            $end=$end=$start-5184000;
            $zarr=$redis->zRevRangeByScore('consumer',$start,$end);
            $znarr=array_slice($zarr,$page_load,$this->page_size);
            $this->jsonMaker($znarr,$redis);
        }elseif ($num=15) {
            $this->jsonMaker($znarr,$redis);
        }        
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
        $pag=$redis->exists('consumer');
        if($pag=true){
            $this->zgetMore($redis);
        }elseif ($pag=false) {
            $this->mysqlFinds();
        }   
    }
}