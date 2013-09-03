<?php
require './Lib/Action/RedisAction.class.php';
/**
 *这个类是关于商家用户登录和新用户注册的控制器
 */
class ServiceAction extends Action{

/**
 *商家用户登录的方法
 */
    public function loginCheck(){
        $User    =M('Service');
        $shopname=$this->_param('shopname');
        $pass    =$this->_param('pass');
        $pass    =md5($pass);
        if($name && $pass) {
            $condition['shopname']=$name; 
            $condition['pass']=$pass;         
            $User_login=$User->where($condition)->find();
            $json=json_encode($User_login);            
            if (!empty($User_login)) {
                if($User_login['shopname']==$condition['shopname'] && $User_login['pass']==$condition['pass']){
                    session('shopname',$condition['shopname']);
                    session('pass',$condition['pass']);
                    session('id',$User_login['id']);  
                    $encrypt=md5($User_login['id'] . $condition['shopname']);
                    $newcondition['encrypt']=$encrypt;
                    $User->where($condition)->save($newcondition);
                    echo $json; 
                    echo $encrypt;                    
                }   
            }elseif (empty($User_login)) {
                    echo "no user";
            }
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
        if(session('?name')==true){
            echo "wrong";
        }
        else if(session('?name')==false){
            echo "right";
        }
    }

/**
 *检测用户是否处于登录状态方法
 */
    public function check(){
        if(session('?name')==true){
            echo "login";
        }
        else if(session('?name')==false){
            echo "logout";
        }
    }

/**
 *新用户注册检测邮箱是否注册方法
 */  
    public function checkEmail(){
        $email =$this->_param('email');
        $ck_email=D('Service');
        $condition['email'] =$email;
        
        if (!$ck_email->create($condition)) {
            exit($ck_email->getError());
        }else{
            $ck_email->add();           
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
 *用户更新个人信息方法（密码）
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
            try {
                $updata->where($condition)->save($newcondition);
                echo "right";
            } catch (Exception $e) {
                echo "wrong";
            }           
        }else if(empty($result)){
            echo "not found";
            
        }   
    }  

/**
 *用户更新个人信息方法
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
 *输出显示用户信息方法
 */
    public function watchInfo(){
        $info=M('Service');
        $condition['id']=session('id');
        $result=$info->where($condition)->find();
        $json=json_encode($result);
        echo $json;
    }

    

}


    