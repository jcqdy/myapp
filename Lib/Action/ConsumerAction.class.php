<?php
/**
 *这个类是关于消费者用户登录和新用户注册的控制器
 */
class ConsumerAction extends Action{
public $auto=array();	
/**
 *用户登录方法
 */	
	public function login(){
        $email=$this->_param('Email');
		$pass =$this->_param('Password');
//      $email='52393137@qq.com';
//      $pass='88791140';
		$User =M('Consumer');
		$pass =md5($pass);
		if($email && $pass) {
			$condition['email']=$email;
			$condition['pass']=$pass;
            $newencrypt['encrypt']=md5($email.time()).'#'.'consumer';
            $User->where($condition)->save($newencrypt);
			$User_login=$User->where($condition)->field('id,name,face,encrypt,city')->find();
            $this->watchUpdata($User_login['id']);
            if (!empty($User_login)) {
                $search=new SearchAction();
                $check=$search->urlcode($User_login);
                $check['updata']=$this->auto;
                $array['login']=$check;
                echo urldecode(json_encode($array));   
            }else{
                echo 'false';
            }           		
		}else {
            echo 'false1';
        }	          
	}

/**
 *用户自动登录方法
 */
	public function autoLogin(){
		$encrypt=$this->_param('encrypt');
		$User=M('Consumer');
		$condition['encrypt']=$encrypt;
		$result=$User->where($condition)->field('id,name,face,encrypt,city')->find();
		if(!empty($result)){
            $search=new SearchAction();
            $check=$search->urlcode($result);
            $array['login']=$check;
            session('id',$result['id']);
            echo urldecode(json_encode($array));  
		}elseif (empty($result)) {
			echo 'false';
		}
	}

/**
 *用户登出方法
 */
	public function logout(){
        if($this->_param('out')){
            session('id',null);
        }		
	}

/**
 *新用户注册时检测是否删除session方法
 */
	public function checkSession(){
        if(session('?id')==true){
        	echo 'no logout';
        }                
        else if(session('?id')==false){
        	echo 'logout';
        }
    }

/**
 *检测用户是否处于登录状态方法
 */
	public function checkLogin(){
        if(session('?id')==true){
        	echo 'login';
        }
        else if(session('?id')==false){
        	echo 'logout';
        }
    }

/**
 *新用户注册检测邮箱是否注册方法
 */  
    public function checkEmail(){  
    	$email=$this->_param('Email');
        if(!empty($email)){
            $ck_email=D('Consumer');
            $condition['email']=$email;
            if (!$ck_email->create($condition)) {
                exit($ck_email->getError());        
            }else{      
                echo "true";
            }
        }else{
            echo 'false';
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
    	$name  =$this->_param('Name');
    	$email =$this->_param('Email');
        $pass  =$this->_param('Password');
//        $city  =$this->_param('City');
        if($name && $email && $pass){
            $pass  =md5($pass);
            $encrypt=md5($email.time()).'#'.'consumer';
            $condition['name']  =$name;
            $condition['email'] =$email;
            $condition['pass']  =$pass;
            $condition['encrypt']=$encrypt;
            $condition['city']='北京';
            $condition['face']='192.168.1.100/myapp/Public/image/moren.jpg';
            $create =D('Consumer');
            $create->add($condition);
            $register=json_encode($condition);
            echo 'true';   
        }else{
            echo 'false';
        }        		
    }

/**
 *用户更新个人信息方法（城市）
 */    
    public function updataCity(){
        $city=$this->_param('City');
        $id=$this->_param('id');
        $User=M('Consumer');
        $data['city']=$city;
        $condition['id']=$id;
        $User->where($condition)->save($data);
        echo 'true';
    }

/**
 *用户更新个人信息方法（密码）
 */
    public function updataPass(){
    	$this->checkLogin();
    	$pass=$this->_param('pass');
        $newpass=$this->_param('newpass');
   		$pass=md5($pass);
    	$updata=M('Consumer');
    	$condition['pass']=$pass;
    	$condition['id']=session('id');
    	$result=$updata->where($condition)->find();
    	if (!empty($result)) {
     		$newpass=md5($newpass);
    		$newcondition['pass']=$newpass;
    		$updata->where($condition)->save($newcondition);
    		echo 'right';
    	}else if(empty($result)){
    		echo 'false';
    	}
     }   	
    
/**
 *用户更新个人信息方法（用户名）
 */
    public function updataName(){
        $name=$this->_param('name');
        $id=$this->_param('id');
        $save['name']=$name;
        $condition['id']=$id;
        $User=M('Consumer');
        $User->where($condition)->save($save);
        echo $name;
    }

/**
 *用户更新个人信息方法（头像）
 */
    public function updataFace(){
        $image=new ImageAction();
        $image->consumerFace();
        $data['face']=$image->facemixurl;
        $User=M('Consumer');
        $condition['id']=$image->id;
        $User->where($condition)->save($data);
        echo $image->facemixurl;
    }

/**
 *消费者界面关注商家信息更新
 */
    public function watchUpdata($consumerid){
        $redis=new Redis();
        $redis->connect('localhost','6379');
        $result=$redis->sMembers('fans'.$consumerid);
        foreach ($result as $serviceid) {
            $value=$redis->keys('<'.$serviceid.'>'.'*');
            $zarr=explode('$', $value['0']);
            $score=$zarr['1'];
            $redis->zAdd('updata'.$consumerid,$score,$serviceid);
        }
        $re_id=$redis->zRevRange('updata'.$consumerid,0,3);
        $info=M('Serviceinfo');
        $service=M('Service');
        foreach ($re_id as $value2) {
            $condition['serviceid']=$value2;    
            $con['id']=$value2;
            $result2=$info->where($condition)->field('title')->find();
            $result3=$service->where($con)->field('shopname,face')->find();
            $result4['title']=urlencode($result2['title']);
            $result4['shopname']=urlencode($result3['shopname']);
            $result4['face']=$result3['face'];
            array_push($this->auto,$result4);
        }
        $redis->delete('updata'.$consumerid);
        $redis->close();
    }

    
}


