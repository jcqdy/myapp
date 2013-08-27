<?php
/**
 *这个类是关于消费者用户登录和新用户注册的控制器
 */
class ConsumerAction extends Action{
	
/**
 *用户登录方法
 */	
	public function login(){		
		$email=$this->_param('Email');
		$pass =$this->_param('Password');
		$User =M('Consumer');		
		$pass =md5($pass);
		if($email && $pass) {   
			$condition['email']=$email; 
			$condition['pass']=$pass;       
			
			$User_login=$User->where($condition)->find();
			$json=json_encode($User_login);
			
			if (!empty($User_login)) {
				if($User_login['email']==$condition['email'] && $User_login['pass']==$condition['pass']){
					session('email',$condition['email']);
					session('pass',$condition['pass']);
					session('id',$User_login['id']);
					$encrypt=md5($User_login['id'] . $condition['email']);
					$newcondition['encrypt']=$encrypt;
					$User->where($condition)->save($newcondition);
					echo "true"." ".$encrypt;
//					echo $json;	
//					echo $encrypt;								
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
			echo 'no user';
		}
	}

/**
 *用户登出方法
 */
	public function logout(){		
		session('name',null);
		session('pass',null);  
	}

/**
 *新用户注册时检测是否删除session方法
 */
	public function checkSession(){
        if(session('?name')==true){
        	echo 'no logout';
        }
        else if(session('?name')==false){
        	echo 'logout';
        }
    }

/**
 *检测用户是否处于登录状态方法
 */
	public function checkLogin(){
        if(session('?name')==true){
        	echo 'login';
        }
        else if(session('?name')==false){
        	echo 'logout';
        }
    }

/**
 *新用户注册检测邮箱是否注册方法
 */  
    public function checkEmail(){
 	//		$email='gubi@cbcom';   
    		$email=$this->_param('Email');
        	$ck_email=D('Consumer');
        	$condition['email']=$email;
 	      	if (!$ck_email->create($condition)) {
        		exit($ck_email->getError());       	
        	}else{       		
  				echo "true";
        	} 
    }

/**
 *新用户注册时检测是否存在相同用户名方法
 */
	public function checkName(){

    		$name =$this->_param('name');
        	$ck_name=D('Consumer');
        	$condition['name'] =$name;
        	
        	if (!$ck_name->create($condition)) {
        		exit($ck_name->getError());
        	}else{
        		$ck_name->add();     
   				echo "right";    		
        	}
    }

/**
 *新用户注册时存入用户信息到数据表方法
 */
    public function createRegister(){ 
  /*  		session_destroy();
    		$uid   =$this->_param('Register');    		
    		session_id('$uid');
    		session_start();   */
    		$name  =$this->_param('Name');
    		$email =$this->_param('Email');			      	
        	$pass  =$this->_param('Password');
        	$pass  =md5($pass);
        	$uptime=date('Y-m-d H:i:s');
        	
        	$condition['name']  =$name;
        	$condition['email'] =$email;
        	$condition['pass']  =$pass;
        	$condition['uptime']=$uptime;
        	$create =D('Consumer');
        	if (!$create->create($condition)) {
        		exit($create->getError());
        	}else{
        		$create->add();
        		session('name',$condition['name']);
				session('pass',$condition['pass']);
				$register=json_encode($condition);
   				echo "true";   		
        	}  	
    }
    
/**
 *用户更新个人信息方法（密码）
 */
    public function updataInfo(){
    		$this->checkLogin();
    		$pass=$this->_param('pass');
   			$pass=md5($pass);
    		$updata=M('Consumer');
    		$condition['pass']=$pass;
    		$condition['name']=session('name');
    		
    		$result=$updata->where($condition)->find();
    		if (!empty($result)) {
   				$newpass=$this->_param('newpass');
     			$newpass=md5($newpass);
    			$newcondition['pass']=$newpass;
    			try {
    				$updata->where($condition)->save($newcondition);
    				echo 'right';
    			} catch (Exception $e) {
    				echo 'wrong';
    			}  			
    		}else if(empty($result)){
    			echo 'not found';
    		}	
    }

}


