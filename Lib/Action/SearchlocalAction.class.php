<?php
class SearchlocalAction extends Action{
    public $page_num;          //当前页数
    public $page_size=15;      //每页数量
    public $mylatitude;        //消费者目前纬度
    public $mylongitude;    //消费者目前经度
    public $consumerid;      
    public $search=array();
    public $array=array();
/**
 *搜索引擎主方法
 */
    public function search(){
        $this->consumerid=$this->_param('id');
        $str=$this->_param('search');
//        $str='南京 苏宁淮海路';
        $this->cutWords($str);
//        $this->display();
    }

/**
 *搜索引擎分词加搜索方法
 */
    public function cutWords($str){
        $so = scws_new();
        $so->set_charset('utf8');
//        $str='成都 老麻抄手清江中路';
        $this->search=explode(' ', $str);
        $num_arr=count($this->search);
        switch ($num_arr) {
            case 1:
                $so->send_text($str);
                $tmp=$so->get_result();
                $num_arr2=count($tmp);
                foreach ($tmp as $value) {
                    array_push($this->array,$value['word']);
                }
                switch ($num_arr2) {
                    case 1:
                        $this->redisFind($this->array['0']);
                        break;
                    case 2:
                        $this->redisFind_two($this->array['0'],$this->array['1']);
                        break;
                    case 3:
                        $this->redisFind($this->array['0'],$this->array['1'],$this->array['2']);
                        break;    
                    default:
                        
                        break;
                }
                break;
            case 2:
                $so->send_text($this->search['1']);
                $tmp=$so->get_result();
                var_dump($tmp);
                $num_arr3=count($tmp);
                foreach ($tmp as $value) {
                    array_push($this->array,$value['word']);
                }
//                var_dump($this->array);
                switch ($num_arr3) {
                    case 1:
                        $this->redisFind_two($this->search['0'],$this->array['0']);
                        break;
                    case 2:
                        $this->redisFind_three($this->search['0'],$this->array['0'],$this->array['1']);
                        break;
                    case 3:
                        $this->redisFind_four($this->search['0'],$this->array['0'],$this->array['1'],$this->array['2']);
                        break;   
                    case 3:
                        $this->redisFind_four($this->search['0'],$this->array['0'],$this->array['1'],$this->array['2']);
                        break; 
                    case 4:
                        $this->redisFind_five($this->search['0'],$this->array['0'],$this->array['1'],$this->array['2'],$this->array['3']);
                        break;
                    case 5:
                        $this->redisFind_six($this->search['0'],$this->array['0'],$this->array['1'],$this->array['2'],$this->array['3'],$this->array['4']);
                        break;   
                    default:
                        # code...
                        break;
                }  
                break;
            case 3:
                $so->send_text($this->search['1']);
                $tmp=$so->get_result();
                foreach ($tmp as $value) {
                    array_push($this->array,$value['word']);
                }
                $so->send_text($this->search['2']);
                $tmp2=$so->get_result();
                foreach ($tmp2 as $value) {
                    array_push($this->array,$value['word']);
                }
                var_dump($this->array);
                $num_arr4=count($this->array);
                switch ($num_arr4) {
                    case 1:
                        $this->redisFind_two($this->search['0'],$this->search['1']);
                        break;
                    case 2:
                        $this->redisFind_three($this->search['0'],$this->array['0'],$this->array['1']);
                        break;
                    case 3:
                        $this->redisFind_four($this->search['0'],$this->array['0'],$this->array['1'],$this->array['2']);
                        break;   
                    case 4:
                        $this->redisFind_five($this->search['0'],$this->array['0'],$this->array['1'],$this->array['2'],$this->array['3']);
                        break;
                    case 5:
                        $this->redisFind_six($this->search['0'],$this->array['0'],$this->array['1'],$this->array['2'],$this->array['3'],$this->array['4']);
                        break;    
                    default:
                        # code...
                        break;
                }  
                break;
            case 4:
                $so->send_text($this->search['1']);
                $tmp=$so->get_result();
                foreach ($tmp as $value) {
                    array_push($this->array,$value['word']);
                }
                $so->send_text($this->search['2']);
                $tmp2=$so->get_result();
                foreach ($tmp2 as $value) {
                    array_push($this->array,$value['word']);
                }
                $so->send_text($this->search['3']);
                $tmp2=$so->get_result();
                foreach ($tmp2 as $value) {
                    array_push($this->array,$value['word']);
                }
                var_dump($this->array);
                $num_arr4=count($this->array);
                switch ($num_arr4) {
                    case 1:
                        $this->redisFind_two($this->search['0'],$this->search['1']);
                        break;
                    case 2:
                        $this->redisFind_three($this->search['0'],$this->array['0'],$this->array['1']);
                        break;
                    case 3:
                        $this->redisFind_four($this->search['0'],$this->array['0'],$this->array['1'],$this->array['2']);
                        break;   
                    case 4:
                        $this->redisFind_five($this->search['0'],$this->array['0'],$this->array['1'],$this->array['2'],$this->array['3']);
                        break;
                    case 5:
                        $this->redisFind_six($this->search['0'],$this->array['0'],$this->array['1'],$this->array['2'],$this->array['3'],$this->array['4']);
                        break;    
                    default:
                        # code...
                        break;
                }  
                break;

            default:
                # code...
                break;
        }        
    }

   
    public function redisFind_two($n,$m){
        $redis=new Redis();
        $redis->pconnect('localhost','6379');
        $redis->delete('service'.$this->consumerid);
        $this->mylatitude=$redis->hGet('local:'.$this->consumerid,'latitude');
        $this->mylongitude=$redis->hGet('local:'.$this->consumerid,'longitude');
//        $n='南京';$m='苏宁';
        $keynm=$redis->keys('*'.$n.'*'.$m.'*');
        if ($keynm) {
            foreach ($keynm as $value) {
                $this->zset($redis,$value);
            }
            $this->zget($redis);
        }else {
//            $this->mysqlFind($n,$m,$p);
        }
        $redis->delete('nm+'.$this->consumerid);
        $redis->delete('p+'.$this->consumerid);
        $redis->delete('r+'.$this->consumerid);
        $redis->delete('l+'.$this->consumerid);
        $redis->delete('t+'.$this->consumerid);
        $redis->close();        
    }
/**
 *从redis缓存中搜索数据的方法
 */  
    public function redisFind_three($n,$m,$p){
        $redis=new Redis();
        $redis->pconnect('localhost','6379');
        $redis->delete('service'.$this->consumerid);
        $this->mylatitude=$redis->hGet('local:'.$this->consumerid,'latitude');
        $this->mylongitude=$redis->hGet('local:'.$this->consumerid,'longitude');
        $keynm=$redis->keys('*'.$n.'*'.$m.'*');
        $keyp=$redis->keys('*'.$p.'*');
        if ($keynm && $keyp) {
            foreach ($keynm as $n_value) {
                $redis->sAdd('nm+'.$this->consumerid,$n_value);
            }
            foreach ($keyp as $p_value) {
                $redis->sAdd('p+'.$this->consumerid,$p_value);
            }
            $member=$redis->sInter('nm+'.$this->consumerid,'p+'.$this->consumerid);  
            foreach ($member as $value) {
                $this->zset($redis,$value);
            }
            $this->zget($redis);
        }else {
//            $this->mysqlFind($n,$m,$p);
        }
        $redis->delete('nm+'.$this->consumerid);
        $redis->delete('p+'.$this->consumerid);
        $redis->delete('r+'.$this->consumerid);
        $redis->delete('l+'.$this->consumerid);
        $redis->delete('t+'.$this->consumerid);
        $redis->close();
    }

    public function redisFind_four($n,$m,$p,$r){
        $redis=new Redis();
        $redis->pconnect('localhost','6379');        
        $redis->delete('service'.$this->consumerid);
        $this->mylatitude=$redis->hGet('local:'.$this->consumerid,'latitude');
        $this->mylongitude=$redis->hGet('local:'.$this->consumerid,'longitude');
//        $n='南京';$m='苏宁';$p='湖南路';$r='商场';
        $keynm=$redis->keys('*'.$n.'*'.$m.'*');
        $keyp=$redis->keys('*'.$p.'*');
        $keyr=$redis->keys('*'.$r.'*');
        if ($keynm && $keyp && $keyr) {
            foreach ($keynm as $n_value) {
                $redis->sAdd('nm+'.$this->consumerid,$n_value);
            }
            foreach ($keyp as $p_value) {
                $redis->sAdd('p+'.$this->consumerid,$p_value);
            }
            foreach ($keyr as $r_value) {
                $redis->sAdd('r+'.$this->consumerid,$r_value);
            }
            $member=$redis->sInter('nm+'.$this->consumerid,'p+'.$this->consumerid,'r+'.$this->consumerid);  
            foreach ($member as $value) {
                $this->zset($redis,$value);
            }
            $this->zget($redis);
        }else {
//            $this->mysqlFind($n,$m,$p);
        }
        $redis->delete('nm+'.$this->consumerid);
        $redis->delete('p+'.$this->consumerid);
        $redis->delete('r+'.$this->consumerid);
        $redis->delete('l+'.$this->consumerid);
        $redis->delete('t+'.$this->consumerid);
        $redis->close();       
    }

    public function redisFind_five($n,$m,$p,$r,$l){
        $redis=new Redis();
        $redis->pconnect('localhost','6379');
        $redis->delete('service'.$this->consumerid);
        $this->mylatitude=$redis->hGet('local:'.$this->consumerid,'latitude');
        $this->mylongitude=$redis->hGet('local:'.$this->consumerid,'longitude');
//        $n='南京';$m='苏宁';$p='湖南路';$r='商场';
        $keynm=$redis->keys('*'.$n.'*'.$m.'*');
        $keyp=$redis->keys('*'.$p.'*');
        $keyr=$redis->keys('*'.$r.'*'); 
        $keyl=$redis->keys('*'.$l.'*');
        if ($keynm && $keyp && $keyr && $keyl) {
            foreach ($keynm as $n_value) {
                $redis->sAdd('nm+'.$this->consumerid,$n_value);
            }
            foreach ($keyp as $p_value) {
                $redis->sAdd('p+'.$this->consumerid,$p_value);
            }
            foreach ($keyr as $r_value) {
                $redis->sAdd('r+'.$this->consumerid,$r_value);
            }
            foreach ($keyl as $l_value) {
                $redis->sAdd('l+'.$this->consumerid,$l_value);
            }   
            $member=$redis->sInter('nm+'.$this->consumerid,'p+'.$this->consumerid,'r+'.$this->consumerid,'l+'.$this->consumerid); 
            foreach ($member as $value) {
                $this->zset($redis,$value);
            }
            $this->zget($redis);  
        }else {
//            $this->mysqlFind($n,$m,$p);
        }
        $redis->delete('nm+'.$this->consumerid);
        $redis->delete('p+'.$this->consumerid);
        $redis->delete('r+'.$this->consumerid);
        $redis->delete('l+'.$this->consumerid);
        $redis->delete('t+'.$this->consumerid);
        $redis->close();
        
    }

    public function redisFind_six($n,$m,$p,$r,$l,$t){
        $redis=new Redis();
        $redis->pconnect('localhost','6379');
        $redis->delete('service'.$this->consumerid);
        $this->mylatitude=$redis->hGet('local:'.$this->consumerid,'latitude');
        $this->mylongitude=$redis->hGet('local:'.$this->consumerid,'longitude');
//        $n='南京';$m='苏宁';$p='湖南路';$r='商场';
        $keynm=$redis->keys('*'.$n.'*'.$m.'*');
        $keyp=$redis->keys('*'.$p.'*');
        $keyr=$redis->keys('*'.$r.'*');
        $keyl=$redis->keys('*'.$l.'*');
        $keyt=$redis->keys('*'.$t.'*');
        if ($keynm && $keyp && $keyr && $keyl && $keyt) {
            foreach ($keynm as $n_value) {
                $redis->sAdd('nm+'.$this->consumerid,$n_value);
            }
            foreach ($keyp as $p_value) {
                $redis->sAdd('p+'.$this->consumerid,$p_value);
            }
            foreach ($keyr as $r_value) {
                $redis->sAdd('r+'.$this->consumerid,$r_value);
            }
            foreach ($keyl as $l_value) {
                $redis->sAdd('l+'.$this->consumerid,$l_value);
            } 
            foreach ($keyt as $t_value) {
                $redis->sAdd('t+'.$this->consumerid,$t_value);
            }  
            $member=$redis->sInter('nm+'.$this->consumerid,'p+'.$this->consumerid,'r+'.$this->consumerid,'l+'.$this->consumerid,'t+'.$this->consumerid); 
            foreach ($member as $value) {
                $this->zset($value);
            }
            $this->zget();  
        }elseif ($keynm && $keyp && $keyr && $keyl && !$keyt) {
            foreach ($keynm as $n_value) {
                $redis->sAdd('nm+'.$this->consumerid,$n_value);
            }
            foreach ($keyp as $p_value) {
                $redis->sAdd('p+'.$this->consumerid,$p_value);
            }
            foreach ($keyr as $r_value) {
                $redis->sAdd('r+'.$this->consumerid,$r_value);
            }
            foreach ($keyl as $l_value) {
                $redis->sAdd('l+'.$this->consumerid,$l_value);
            } 
            
            $member=$redis->sInter('nm+'.$this->consumerid,'p+'.$this->consumerid,'r+'.$this->consumerid,'l+'.$this->consumerid); 
            foreach ($member as $value) {
                $this->zset($redis,$value);
            }
            $this->zget($redis);  
        } else {
//            $this->mysqlFind($n,$m,$p);
        }
        $redis->delete('nm+'.$this->consumerid);
        $redis->delete('p+'.$this->consumerid);
        $redis->delete('r+'.$this->consumerid);
        $redis->delete('l+'.$this->consumerid);
        $redis->delete('t+'.$this->consumerid);
        $redis->close();
        
    }
/**
 *将查询结果导入有序集合sort set的方法
 */
    public function zset($redis,$value){        
        $zarr=explode('$', $value);
        $latitude=$zarr['2'];
        $longitude=$zarr['3'];
        $num=($this->mylatitude-$latitude)*($this->mylatitude-$latitude)+($this->mylongitude-$longitude)*($this->mylongitude-$longitude);
        $distance=sqrt($num);
        $score=number_format($distance,8);
        $redis->zAdd('service'.$this->consumerid,$score,$value);
    }

/**
 *在有序集合sort set中按照时间戳导出最新的数据的方法
 */
    public function zget($redis){        
        $zarr=$redis->zRange('service'.$this->consumerid,0,15);
        $this->jsonMaker($zarr,$redis);
    }

/**
 *分页，再次请求redis搜索数据的方法
 */
    public function zgetMore($redis){
        $this->page_num=$this->_param('num');     
        $page_load=($this->page_num-1)*$this->page_size;
        $zarr=$redis->zRange('service'.$this->consumerid,$page_load,$this->page_size);
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
        $redis->pconnect('localhost','6379');
        $pag=$redis->exists('service'.$this->consumerid);
        if($pag==true){
            $this->zgetMore($redis)
            $redis->close();
        }elseif ($pag==false) {
            $this->mysqlFinds();
            $redis->close();
        }
    }
    
}
