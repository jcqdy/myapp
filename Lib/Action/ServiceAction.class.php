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
        $User =M('Service');       
        $pass =md5($pass);
        if($email && $pass) {   
            $condition['email']=$email;
            $condition['pass']=$pass;
            $newencrypt['encrypt']=md5($email.time());
            $User->where($condition)->save($newencrypt);
            $service=$User->where($condition)->field('id,phone_num,shopname,address,sertype,face,encrypt,latitude,longitude')->find();            
            if(!empty($service)){  
                $id=$service['id'];
                $info=M('Serviceinfo');
                $condition['id']=$id;
                $result=$info->where($condition)->field('favorable,site,info,favtime,infotime')->find();
                $image=M('Image');
                $img['serviceid']=$id;
                $imgarr=$image->where($img)->order('uptime desc')->limit('0,8')->field('imgurl1,photoid')->select();
                foreach ($service as $key => $value) {
                    $array[$key]=$value;
                }       
                foreach ($result as $key1 => $value1) {
                    $array[$key1]=$value1;
                }
                $redis=new Redis();
                $watch=$redis->sCard($service['id']);
                $take=new SearchAction();
                $array=$take->urlcode($array);
                $array['photo']=$imgarr;
                $array['watch']=$watch;
                $arr['login']=$array;
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
            $id=$service['id'];
            $info=M('Serviceinfo');
            $condition['id']=$id;
            $result=$info->where($condition)->field('favorable,site,info,favtime,infotime')->find();
            $image=M('Image');
            $img['serviceid']=$id;
            $imgarr=$image->where($img)->order('uptime desc')->limit('0,8')->field('imgurl1,photoid')->select();
            foreach ($service as $key => $value) {
                $array[$key]=$value;
            }       
            foreach ($result as $key1 => $value1) {
                $array[$key1]=$value1;
            }
            $redis=new Redis();
            $watch=$redis->sCard($id);
            $take=new SearchAction();
            $array=$take->urlcode($array);
            $array['photo']=$imgarr;
            $array['watch']=$watch;
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
                $ck_email->add();
            }
        }else {
            echo 'false';
        }    
    }

/**
 *新用户注册时检测是否存在相同用户名方法
 */
    public function checkName(){
        $name =$this->_param('name');
        $ck_name=D('Service');
        $condition['name'] =$name;
        
        if (!$ck_name->create($condition)) {
            exit($ck_name->getError());
        }else{
            $ck_name->add();            
        }
    }

/**
 *新用户注册时存入用户信息到数据表方法
 */
    public function creatRegister(){
        $cache=new RedisAction();
        $cache->popRegister();
        $new=$cache->array;
        $arr=array(
        '0'=>'email','1'=>'pass','2'=>'shopname','3'=>'address','4'=>'face','5'=>'sertype','6'=>'latitude',
        '7'=>'longitude','8'=>'phone_num','9'=>'city');
        $n=0;
        foreach ($new as $value) {
            $key=$arr[$n];
            $condition[$key]=$value;
            $n++;
        }
        var_export($condition);$this->display();
        $creat=D('Service');
        if (!$creat->create($condition)) {
            exit($creat->getError());
        }else{
            $creat->add();
            $id=$creat->where($condition)->getField('id');              
        }

        var_dump($id);
        $cache->hashSet($id,$arr); 

 //       $register=json_encode($condition);     
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
        $json=$this->_param('info');
        $json=html_entity_decode($json);
        $info=json_decode($json,true);
        $image=new ImageAction();
        $image->imageUpload();
        $img=M('Image');
        foreach ($image->imgurl as $value) {
            
        }
    }

    public function test(){
        $img=M('Image');
        $condition['imgurl1']='1';
        $condition['serviceid']='1';
        $img->add($condition);
    }




/**
 *用户更新商家头像方法
 */
    public function updataFace(){
        $redis=new Redis();
        $redis->connect('localhost','6379'); 
        $image=new ImageAction();
        $image->faceUpload();
        $data['face']=$image->facemixurl;
        $User=M('Service');
        $condition['id']=session('id');
        $User->where($condition)->save($data);
        $hkey=$redis->keys(session('id').'*');
        $redis->hSet($hkey,'face',$image->facemixurl);
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
        $img['serviceid']=$serviceid;
        $image=M('Image');
        $imgarr=$image->where($img)->order('uptime desc')->field('imgurl2,id,state')->select();
        foreach ($imgarr as $value) {
            foreach ($value as $key => $value2) {
                if($key!='state'){
                    $con[$key]=urlencode($value2);
                }else{
                    $con[$key]=$value2;
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
        $serviceid=$this->_param('serviceid');
        $array=array();
        $search=new SearchAction();
        $img['serviceid']=$serviceid;
        $image=M('Image');
        $imgarr=$image->where($img)->order('uptime desc')->limit('0,8')->field('imgurl2,id,state')->select();
        foreach ($imgarr as $value) {
            foreach ($value as $key => $value2) {
                if($key!='state'){
                    $con[$key]=urlencode($value2);
                }else{
                    $con[$key]=$value2;
                }
            }
            array_push($array,$con);
        }
        $up['photo']=$array;
        echo urldecode(json_encode($up,JSON_UNESCAPED_SLASHES));
    }

}   