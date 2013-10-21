<?php
class SearchloacalAction extends Action{
    public $page_num;          //当前页数
    public $page_size=15;      //每页数量
    public $mylatitude;        //消费者目前经度
    public $mylongitude;       //消费者目前纬度
    public $search=array();
/**
 *搜索引擎主方法
 */
    public function search(){
        $str=$this->_param('search');
 //      $str='苏宁';
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

    public function redisFind($n,$m,$p){
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
 *将查询结果导入有序集合sort set的方法
 */
    public function zset($value){
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $redis->delete('service'.session('id'));
        $zarr=explode('$', $value);
        $latitude=$zarr['2'];
        $longitude=$zarr['3'];
        $num=($mylatitude-$latitude)*($mylatitude-$latitude)+($mylongitude-$longitude)*($mylongitude-$longitude);
        $distance=sqrt($num);
        $score=number_format($distance,8);
        $redis->zAdd('service'.session('id'),$score,$value);
    }

/**
 *在有序集合sort set中按照时间戳导出最新的数据的方法
 */
    public function zget(){
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $start=time();
        $zarr=$redis->zRange('service'.session('id'),0,15);
        $this->jsonMaker($zarr,$redis);
    }

/**
 *分页，再次请求redis搜索数据的方法
 */
    public function zgetMore($redis){
        $this->page_num=$this->_param('num');     
        $page_load=($this->page_num-1)*$this->page_size;
        $zarr=$redis->zRange('consumer'.session('id'),$page_load,$this->page_size);
        $this->jsonMaker($zarr,$redis);
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
 *第一次从数据库mysql获取数据的方法
 */ 
    public function mysqlFind(){
        $n='苏宁';
        if($n && !$m && !$p){ 
            $service=new Model();
            $result=$service->query("select *,sqrt(power('$mylatitude'-latitude,2)+power('$mylongitude'-longitude,2)) as d from service where (shopname like '%$n%') or (address like'%$n%') or (sertype like '%$n%') order by d asc limit 0,15");   
            if(empty($result)){
                echo '搜索不到';
            }elseif (!empty($result)) {
                $lastarr=$this->sqlurlcode($result);
            }
            echo urldecode(json_encode($lastarr));
        }elseif ($n && $m && !$p) {
            $service=new Model();
            $result=$service->query("select *,sqrt(power('$mylatitude'-latitude,2)+power('$mylongitude'-longitude,2)) as d from service where (shopname like '%$n%' or shopname like '%$m%') or (address like '%$n%' or address like '%$m%') or (sertype like '%$n%' or sertype like '%$m%') order by d asc limit 0,15");   
            if(empty($result)){
                echo '搜索不到';
            }elseif (!empty($result)) {
                $lastarr=$this->sqlurlcode($result);
            }
            echo urldecode(json_encode($lastarr));
        }elseif ($n && $m && $p) {
           $service=new Model();
            $result=$service->query("select *,sqrt(power('$mylatitude'-latitude,2)+power('$mylongitude'-longitude,2)) as d from service where (shopname like '%$n%' or shopname like '%$m%' shopname like '%$p%') or (address like '%$n%' or address like '%$m%' address like '%$p%') or (sertype like '%$n%' or sertype like '%$m%' sertype like '%$p%') order by d asc limit 0,15");
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
 *分页时从数据库mysql获取数据的方法
 */ 
    public function mysqlFinds(){
        $this->page_num=$this->_param('num');
        $page_load=($this->page_num-1)*$this->page_size;
        $n=$this->search['0'];
        $m=$this->search['1'];
        $p=$this->search['2'];
//        $n='苏宁';
        if($n && !$m && !$p){ 
            $service=new Model();
            $result=$service->query("select *,sqrt(power('$mylatitude'-latitude,2)+power('$mylongitude'-longitude,2)) as d from service where (shopname like '%$n%') or (address like'%$n%') or (sertype like '%$n%') order by d asc limit '$page_load','$this->page_size'");   
            if(empty($result)){
                echo '搜索不到';
            }elseif (!empty($result)) {
                $lastarr=$this->sqlurlcode($result);
            }
            echo urldecode(json_encode($lastarr));
        }elseif ($n && $m && !$p) {
            $service=new Model();
            $result=$service->query("select *,sqrt(power('$mylatitude'-latitude,2)+power('$mylongitude'-longitude,2)) as d from service where (shopname like '%$n%' or shopname like '%$m%') or (address like '%$n%' or address like '%$m%') or (sertype like '%$n%' or sertype like '%$m%') order by d asc limit '$page_load','$this->page_size'");   
            if(empty($result)){
                echo '搜索不到';
            }elseif (!empty($result)) {
                $lastarr=$this->sqlurlcode($result);
            }
            echo urldecode(json_encode($lastarr));
        }elseif ($n && $m && $p) {
           $service=new Model();
            $result=$service->query("select *,sqrt(power('$mylatitude'-latitude,2)+power('$mylongitude'-longitude,2)) as d from service where (shopname like '%$n%' or shopname like '%$m%' shopname like '%$p%') or (address like '%$n%' or address like '%$m%' address like '%$p%') or (sertype like '%$n%' or sertype like '%$m%' sertype like '%$p%') order by d asc limit '$page_load','$this->page_size'");
            if(empty($result)){
                echo '搜索不到';
            }elseif (!empty($result)) {
                $lastarr=$this->sqlurlcode($result);
            } 
            echo urldecode(json_encode($lastarr));            
        }                 
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
    
}