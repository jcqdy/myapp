<?php
require './Lib/Action/RedisAction.class.php';
/**
 *这个类是关于商家用户登录和新用户注册的控制器
 */
class ServiceAction extends Action{

/**
 *商家用户登录的方法
 */
    public function login(){        
        $email=$this->_param('Email');
        $pass =$this->_param('Password');
//        $email='jcq@qa.com';
//        $pass='sttf215';
        $User =M('Service');
        $pass =md5($pass);
        if($email && $pass) {
            $condition['email']=$email;
            $condition['pass']=$pass;
            $newencrypt['encrypt']=md5($email.time()).'#'.'service';
            $User->where($condition)->save($newencrypt);
            $service=$User->where($condition)->field('id,phone_num,shopname,address,sertype,face,encrypt,longitude,latitude')->find();
            $redis=new Redis();
            $redis->connect('localhost','6379');
            $watch=$redis->sCard($service['id']);   //从redis sort中获得关注数量
            if ($watch>1000) {
                $num=floor($watch/100)/10;
                $watch=$num.'K';
            }
            $visitors=$redis->get('visitors'.$service['id']);
//            var_dump($service);            
            if(!empty($service)){
                $id=$service['id'];
                $info=M('Serviceinfo');
                $condition['serviceid']=$id;
                $result=$info->where($condition)->field('favorable,information,favtime,infotime')->find();
                $image=M('Image');
                $img['serviceid']=$id;
                $imgarr=$image->where($img)->order('uptime desc')->limit('0,8')->field('imgurl1,photoid')->select();
                foreach ($service as $key => $value) {
                    $array[$key]=$value;
                }       
                foreach ($result as $key1 => $value1) {
                    $array[$key1]=$value1;
                }
                $take=new SearchAction();
                $array=$take->urlcode($array);
                $array['photo']=$imgarr;
                $array['watch']=$watch;
                $array['visitors']=$visitors;
                $arr['login']=$array;
                session_start();
                session('serviceid',$id);
                echo urldecode(json_encode($arr,JSON_UNESCAPED_SLASHES));
            }elseif (empty($service)) {
                echo 'false';
            } 
        }else {
            echo 'false';
        }             
    }

/**
 *用户自动登录方法  
 */
    public function autoLogin(){
        $encrypt=$this->_param('encrypt');
        $User=M('Service');
        $condition['encrypt']=$encrypt;
        $service=$User->where($condition)->field('id,phone_num,shopname,address,sertype,face,encrypt,latitude,longitude')->find();     
        if(!empty($service)){
            $redis=new Redis();
            $redis->connect('localhost','6379');
            $watch=$redis->sCard($service['id']);   //从redis sort中获得关注数量
            if ($watch>1000) {
                $num=floor($watch/100)/10;
                $watch=$num.'K';
            }
            $visitors=$redis->get('visitors'.$service['id']);
            $id=$service['id'];
            $info=M('Serviceinfo');
            $con['serviceid']=$id;
            $result=$info->where($con)->field('favorable,information,favtime,infotime')->find();
//            var_dump($result);
            $image=M('Image');
            $img['serviceid']=$id;
            $imgarr=$image->where($img)->order('uptime desc')->limit('0,8')->field('imgurl1,photoid')->select();
            foreach ($service as $key => $value) {
                $array[$key]=$value;
            }       
            foreach ($result as $key1 => $value1) {
                $array[$key1]=$value1;
            }
            $take=new SearchAction();
            $array=$take->urlcode($array);
            $array['photo']=$imgarr;
            $array['watch']=$watch;
            $array['visitors']=$visitors;
            $arr['login']=$array;
            echo urldecode(json_encode($arr,JSON_UNESCAPED_SLASHES));
        }elseif (empty($service)) {
            echo "no user";
        }
    }

/**
 *用户登出方法
 */
    public function logoutCheck(){       
        session('shopname',null);
        session('pass',null);  
    }

/**
 *新用户注册时检测是否删除session方法
 */
    public function checkSession(){
        if(session('?id')==true){
            echo "wrong";
        }
        else if(session('?id')==false){
            echo "right";
        }
    }

/**
 *检测用户是否处于登录状态方法
 */
    public function check(){
        if(session('?id')==true){
            echo "login";
        }
        else if(session('?id')==false){
            echo "logout";
        }
    }

/**
 *新用户注册检测邮箱是否注册方法
 */  
    public function checkEmail(){
        $email =$this->_param('Email');
        if(!empty($email)){
            $ck_email=D('Service');
            $condition['email'] =$email;
            if (!$ck_email->create($condition)) {
                exit($ck_email->getError());
            }else{
                echo "true";
            }
        }else {
            echo 'false';
        }    
    }


/**
 *新商家注册时存入用户信息到数据表方法
 */
    public function creatRegister(){
        $email =$this->_param('Email');
        $pass  =$this->_param('Password');  
        if($email && $pass){
            $redis=new Redis();
            $redis->connect('localhost','6379');
            $pass  =md5($pass);
            $encrypt=md5($email.time()).'#'.'service';
            $condition['email'] =$email;
            $condition['pass']  =$pass;
            $condition['encrypt']=$encrypt;
            $condition['face']='192.168.1.100/myapp/Public/image/moren.jpg';
            $condition['phone_num']='暂无';
            $condition['watch']='暂无';
            $condition['shopname']='暂无';
            $condition['address']='暂无';
            $condition['sertype']='暂无';
            $condition['city']='暂无';
            $condition['latitude']='暂无';
            $condition['longitude']='暂无';
            $condition['visitors']='暂无';
            $create =D('Service');
            $create->add($condition);
            $id=$create->where($condition)->field('id')->find();
            $info=D('Serviceinfo');
            $con['serviceid']=$id['id'];
            $con['favorable']='暂无';
            $con['information']='暂无';
            $con['favtime']='暂无';
            $con['infotime']='暂无';
            $info->add($con);
            $redis->set('visitors'.$id,'0');
            echo 'true'; 
        }
    }


/**
 *商家用户编辑商店基本信息的方法
 */
    public function editBasic(){
        $basic=$this->_param('basic');
//        $basic='{"id":"14","shopname":"哈哈"}';
        $basic=html_entity_decode($basic);
        $basic=json_decode($basic,true);
        $id['id']=$basic['id'];
        $condition['shopname']=$basic['shopname'];
        $condition['address']=$basic['address'];
        $condition['sertype']=$basic['sertype'];
        $condition['longitude']=$basic['longitude'];
        $condition['latitude']=$basic['latitude'];
        $condition['phone_num']=$basic['phone_num'];
        $condition['city']=$basic['city'];
        $create=D('Service');
        $create->where($id)->save($condition);
        $condition['face']=$create->where($id)->field('face')->find();
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $hkey='<'.$id['id'].'>'.$condition['shopname'].$condition['address'].$condition['sertype'].$condition['city'].'$'.time().'$'.$condition['latitude'].'$'.$condition['longitude'];
        $okey=$redis->keys('<'.$id['id'].'>'.'*');
        var_dump($okey);
        if($okey){
            $redis->rename($okey['0'],$hkey);
        }
        foreach ($condition as $key => $value) {
            $redis->hSet($hkey,$key,$value);
        }
    } 


/**
 *用户更新商家账号信息方法（密码）
 */
    public function updataPass(){
        $this->check();
        $pass=$this->_param('pass');
        $newpass=$this->_param('newpass');
        $pass=md5($pass);
        $updata=M('Service');
        $condition['pass']=$pass;
        $condition['id']=session('id');
        $result=$updata->where($condition)->find();
        if (!empty($result)) {
            $newpass=md5($newpass);
            $newcondition['pass']=$newpass;
            $updata->where($condition)->save($newcondition);
        }else if(empty($result)){
            echo 'false';
        }
    }

/**
 *用户更新商家信息方法
 */
    public function updataInfo(){
        $info=$this->_param('info');
        $info=json_decode($info,true);
        $image=new ImageAction();
        $image->imageUpload();
        $img=M('Image');
        foreach ($image->imgurl as $value) {
            
        }
    }
    

/**
 *用户更新商家头像方法
 */
    public function updataFace(){
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $image=new ImageAction();
        $image->serviceFace();
        $data['face']=$image->facemixurl;
        $User=M('Service');
        $condition['id']=$image->id;
        $User->where($condition)->save($data);
        $hkey=$redis->keys('<'.$condition['id'].'>'.'*');
        if($hkey){
            $redis->hSet($hkey['0'],'face',$image->facemixurl);
        }else{
            break;
        }      
    }

/**
 *点击查看更多图片方法
 */
    public function watchList(){
        $serviceid=$this->_param('serviceid');
        $list=M('Image');
        $search=new SearchAction();
        $array=array();
        $condition['serviceid']=$serviceid;
        $imgarr=$list->where($condition)->order('uptime desc')->field('imgurl1,photoid')->select();
        $array['photo']=$imgarr;
        echo json_encode($array,JSON_UNESCAPED_SLASHES);
    }

/**
 *图片列表中横向滑动查看大图片方法
 */
    public function listLarge(){
        $serviceid=$this->_param('serviceid');
//        $serviceid='1';
        $img['serviceid']=$serviceid;
        $image=M('Image');
        $imgarr=$image->where($img)->order('uptime desc')->field('imgurl2,photoid,explain')->select();
        $array=array();
        foreach ($imgarr as $value) {
            foreach ($value as $key => $value2) {
                if($key!='explain'){
                    $con[$key]=$value2;
                }else{
                    $con[$key]=urlencode($value2);
                } 
            }           
            array_push($array,$con);
        }
        $up['photo']=$array;   
        echo urldecode(json_encode($up,JSON_UNESCAPED_SLASHES));
    }

/**
 *商家主页中横向滑动查看大图片方法
 */
    public function watchLarge(){
//        $serviceid=$this->_param('serviceid');
        $serviceid='1';
        $array=array();
        $img['serviceid']=$serviceid;
        $image=M('Image');
        $imgarr=$image->where($img)->order('uptime desc')->limit('0,8')->field('imgurl2,photoid,explain')->select();
        foreach ($imgarr as $value) {
            foreach ($value as $key => $value2) {
                if($key!='explain'){
                    $con[$key]=$value2;
                }else{
                    $con[$key]=urlencode($value2);
                }
            }
            array_push($array,$con);
        }
        $up['photo']=$array;
        echo urldecode(json_encode($up,JSON_UNESCAPED_SLASHES));
    }

}   