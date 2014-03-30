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
    public $array=array();

/**
 *搜索引擎主方法
 */
    public function search(){
        $this->consumerid=$this->_param('id');
        $str=$this->_param('search');
        $this->cutWords($str);
    }

/**
 *搜索引擎分词加搜索方法
 */
    public function cutWords($str){
        $so = scws_new();
        $so->set_charset('utf8');
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
                $num_arr3=count($tmp);
                foreach ($tmp as $value) {
                    array_push($this->array,$value['word']);
                }
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
        $keynm=$redis->keys('*'.$n.'*'.$m.'*');
        if ($keynm) {
            foreach ($keynm as $value) {
                $this->zset($redis,$value);
            }
            $this->zget($redis);
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
 *第一次从数据库mysql获取数据的方法
 */
    public function mysqlFind($n,$m,$p,$r,$l,$t){
//        $n='苏宁';
        $time=date('Y-m-d H:i:s');
        $oldtime=date('Y-m-d H:i:s',strtotime("-1 month"));
        $oldertime=date('Y-m-d H:i:s',strtotime("-6 month"));
        
        if($n && !$m && !$p && !$r && !$l && !$t){
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
        }elseif ($n && $m && !$p && !$r && !$l && !$t) {
            $consumer=M('Service');
            $condition['shopname']=array('like','%'.$m.'%');
            $condition['address']=array('like','%'.$m.'%');
            $condition['sertype']=array('like','%'.$m.'%');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $map['city']=array('EQ',$n);
            $result=$consumer->where($map)->order('uptime desc')->limit($this->page_size)->select();
            if(empty($result)){
                echo '搜索不到';
            }elseif (!empty($result)) {
                $lastarr=$this->sqlurlcode($result);
            }
            echo urldecode(json_encode($lastarr));  
        }elseif ($n && $m && $p && !$r && !$l && !$t) {
            $consumer=M('Service');
            $condition['shopname']=array('like',array('%'.$m.'%','%'.$p.'%'),'OR');
            $condition['address']=array('like',array('%'.$m.'%','%'.$p.'%'),'OR');
            $condition['sertype']=array('like',array('%'.$m.'%','%'.$p.'%'),'OR');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $map['city']=array('EQ',$n);
            $result=$consumer->where($map)->order('uptime desc')->limit($this->page_size)->select();
            if(empty($result)){
                echo '搜索不到';
            }elseif (!empty($result)) {
                $lastarr=$this->sqlurlcode($result);
            } 
            echo urldecode(json_encode($lastarr));
        }elseif ($n && $m && $p && $r && !$l && !$t) {
            $consumer=M('Service');
            $condition['shopname']=array('like',array('%'.$m.'%','%'.$p.'%','%'.$r.'%'),'OR');
            $condition['address']=array('like',array('%'.$m.'%','%'.$p.'%','%'.$r.'%'),'OR');
            $condition['sertype']=array('like',array('%'.$m.'%','%'.$p.'%','%'.$r.'%'),'OR');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $map['city']=array('EQ',$n);
            $result=$consumer->where($map)->order('uptime desc')->limit($this->page_size)->select();
            if(empty($result)){
                echo '搜索不到';
            }elseif (!empty($result)) {
                $lastarr=$this->sqlurlcode($result);
            } 
            echo urldecode(json_encode($lastarr));            
        }elseif ($n && $m && $p && $r && $l && !$t) {
            $consumer=M('Service');
            $condition['shopname']=array('like',array('%'.$m.'%','%'.$p.'%','%'.$r.'%'),'OR');
            $condition['address']=array('like',array('%'.$m.'%','%'.$p.'%','%'.$r.'%'),'OR');
            $condition['sertype']=array('like',array('%'.$m.'%','%'.$p.'%','%'.$r.'%'),'OR');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $map['city']=array('EQ',$n);
            $result=$consumer->where($map)->order('uptime desc')->limit($this->page_size)->select();
            if(empty($result)){
                echo '搜索不到';
            }elseif (!empty($result)) {
                $lastarr=$this->sqlurlcode($result);
            } 
            echo urldecode(json_encode($lastarr));            
        }elseif ($n && $m && $p && $r && $l && $t) {
            $consumer=M('Service');
            $condition['shopname']=array('like',array('%'.$m.'%','%'.$p.'%','%'.$r.'%'),'OR');
            $condition['address']=array('like',array('%'.$m.'%','%'.$p.'%','%'.$r.'%'),'OR');
            $condition['sertype']=array('like',array('%'.$m.'%','%'.$p.'%','%'.$r.'%'),'OR');
            $condition['_logic']='or';
            $map['_complex'] = $condition;
            $map['uptime']=array('between',array($oldtime,$time));
            $map['city']=array('EQ',$n);
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


    public function jsonMaker($arr,$redis){
        $array4=array();
        foreach ($arr as $zvalue) {
                $array0=$redis->hGetAll($zvalue);
                $array['shopname']=$array0['shopname'];
                $array['address']=$array0['address'];
                $array['phone_num']=$array0['phone_num'];
                $array['face']=$array0['face'];
                $array['id']=$array0['id'];
                $array['watch']=$array0['watch'];
                $arrcode=$this->urlcode($array);
                array_push($array4,$arrcode);
            }
        $array5['consumer']=$array4;
        echo urldecode(json_encode($array5));
    }


/**
 *将查询结果导入有序集合sort set的方法
 */
    public function zset($redis,$value){
        $zarr=explode('$', $value);
        $score=$zarr['1'];
        $redis->zAdd('service'.$this->consumerid,$score,$value);
    }

/**
 *在有序集合sort set中按照时间戳导出最新的数据的方法
 */
    public function zget($redis){
        $zarr=$redis->zRevRange('service'.$this->consumerid,0,15);
        $this->jsonMaker($zarr,$redis);
    }

/**
 *分页，再次请求redis搜索数据的方法
 */
    public function zgetMore($redis){
        $this->page_num=$this->_param('num');     
        $page_load=($this->page_num-1)*$this->page_size;
        $zarr=$redis->zRevRange('service'.$this->consumerid,$page_load,$this->page_size);
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
        $redis->pconnect('localhost','6379');
        $pag=$redis->exists('service'.$this->consumerid);
        if($pag==true){
            $this->zgetMore($redis);
            $redis->close();
        }elseif ($pag==false) {
            $this->mysqlFinds();
            $redis->close();
        }
    }

/**
 *点击查看商家主页方法
 */
    public function watch(){
        $all_sphoto=array();
        $all_bphoto=array();
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $id=$this->_param('id');
        $consumerid=$this->_param('consumerid');        
        $User=M('Serviceinfo');
        $User2=M('Service');
        $condition['serviceid']=$id;
        $service['id']=$id;
        $result0=$User2->where($service)->field('latitude,longitude')->find();
        $result=$User->where($condition)->field('favorable,information,favtime,infotime')->find();
        $img['serviceid']=$id;
        $image=M('Image');
        $imgarr=$image->where($img)->order('uptime desc')->limit('0,10')->field('imgurl1,imgurl2,explain')->select();
        $hkey=$redis->keys('<'.$id.'>'.'*');
        $array=$redis->hGetAll($hkey['0']); 
        foreach ($result0 as $key => $value) {
            $up[$key]=$value;
        }
        foreach ($result as $key => $value) {
            $up[$key]=$value;
        }
        foreach ($array as $key => $value) {
            $up[$key]=$value;                        
        }
        $up=$this->urlcode($up);
        if ($up['watch']>1000) {
                $num=floor($up['watch']/100)/10;
                $up['watch']=$num.'K';
            }   
        foreach ($imgarr as $photo) {
            foreach ($photo as $key2 => $value2) {
                switch ($key2) {
                    case 'imgurl1':
                        $small_photo['imgurl1']=$value2;
                        break;
                    case 'photoid':
                        $small_photo['photoid']=$value2;
                        break;
                    case 'imgurl2':
                        $big_photo['imgurl2']=$value2;
                        break;
                    case 'explain':
                        $big_photo['explain']=urlencode($value2);
                        break;                    
                    default:
                        
                        break;
                }                
            }
            array_push($all_sphoto,$small_photo);
            array_push($all_bphoto,$big_photo);
        }
        $redis->incr('visitors'.$id);
        $fans=$redis->sIsMember('watch'.$id,$consumerid);
        $up['photo']=$all_sphoto;
        $up['bigphoto']=$all_bphoto;//json_encode($wait,JSON_UNESCAPED_SLASHES);
        $up['fans']='暂无';
        $con['consumer']=$up;       
        echo stripslashes(urldecode(json_encode($con,JSON_UNESCAPED_SLASHES)));
        $redis->close();
    }

}




