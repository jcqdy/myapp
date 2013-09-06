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
            $service=$User->where($condition)->field('id,phone_num,shopname,address,sertype,face,encrypt')->find();            
            $info=M('Serviceinfo');
            $condition['id']=$id;
            $result=$info->where($condition)->field('favorable,site,info,favtime,infotime')->find();
            $image=M('Image');
            $img['serviceid']=$id;
            $imgarr=$image->where($img)->field('imgurl1')->select();
            foreach ($service as $key => $value) {
                $array[$key]=$value;
            }
            foreach ($result as $key1 => $value1) {
                $array[$key1]=$value1;
            }
            foreach ($imgarr as $key2 => $value2) {
                foreach ($value2 as $value3) {
                    $pho[$key2+1]=$value3;
                }
            }
            $redis=new Redis();
            $watch=$redis->sCard($service['id']);
            $take=new SearchAction();
            $photo['photo']=$pho;
            $array=$take->urlcode($array);
            $array['photo']=$photo['photo'];
            $array['watch']=$watch;
            $arr['login']=$array;
            echo urldecode(json_encode($arr,JSON_UNESCAPED_SLASHES));
        }else {
            echo 'false';
        }             
    }

/**
 *用户自动登录方法  
 */
    public function autoLogin(){
        $encrypt=$this->_param('encrypt');
        $User=M('Consumer');
        $condition['encrypt']=$encrypt;
        $result=$User->where($condition)->find();
        if(!empty($result)){
            session('name',$result['name']);
            session('pass',$result['pass']);
            $json=json_encode($result);
            echo $json;
        }elseif (empty($result)) {
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
        '0'=>'email','1'=>'pass','2'=>'shopname','3'=>'address','4'=>'face','5'=>'sertype','6'=>'phone_num',
        '7'=>'intro','8'=>'location');           
        $n=0;
        foreach ($new as $value) {
            $key=$arr[$n];
            $condition[$key]=$value;
            ++$n;
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

        session('shopname',$condition['shopname']);
        session('pass',$condition['pass']);
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
        $this->check();
        $json=file_get_contents('php://input');
        $json=json_decode($json);
        $updata=M('Service');
        $condition['id']=session('id');
        foreach ($json as $key => $value) {
            $add[$key]=$value;
            $updata->where($condition)->save($add);
        }
    }

/**
 *用户更新商家头像方法
 */
    public function updataFace(){
        $redis=new Redis();
        $redis->connect('localhost','6379'); 
        $image=new ImageAction();
        $image->faceUpload();
        $data['imgurl']=$image->facemixurl;
        $User=M('Service');
        $condition['id']=session('id');
        $User->where($condition)->save($data);
        $hkey=$redis->keys(session('id').'*');
        $redis->hSet($hkey,'face',$image->facemixurl);
    }

    public function takeInfo(){
        $json=file_get_contents('php://input');
        $json=json_decode($json);

    }

    

}


    