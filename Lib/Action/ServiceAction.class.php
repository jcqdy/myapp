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
        '0'=>'email','1'=>'pass','2'=>'shopname','3'=>'address','4'=>'phone_num','5'=>'sertype','6'=>'intro',
        '7'=>'face','8'=>'location');           
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

    public function test(){
        $value=$this->_param('ID');
        $image='http://192.168.1.100/myapp/Public/image/1.jpg';
        $image1='http://192.168.1.100/myapp/Public/image/2.jpg';
        $image2='http://192.168.1.100/myapp/Public/image/3.jpg';
        $image3='http://192.168.1.100/myapp/Public/image/4.jpg';
        $image4='http://192.168.1.100/myapp/Public/image/5.jpg';
        $image5='http://192.168.1.100/myapp/Public/image/1.jpg';
        $image6='http://192.168.1.100/myapp/Public/image/2.jpg';
        $image7='http://192.168.1.100/myapp/Public/image/3.jpg';
        $image8='http://192.168.1.100/myapp/Public/image/4.jpg';
        $image9='http://192.168.1.100/myapp/Public/image/5.jpg';

        $id='1';$id1='1';$id2='2';$id3='3';$id4='4';$id5='5';
        $id6='1';$id7='1';$id8='2';$id9='3';$id10='4';$id11='5';

        $name='a';$name1='b';$name2='c';$name3='d';$name4='e';$name5='d'; 
        $name6='a';$name7='b';$name8='c';$name9='d';$name10='e';$name11='d';

        $phone='12345';$phone1='12345';$phone2='12345';$phone3='12345';$phone4='12345';
        $phone5='12345';$phone6='12345';$phone7='12345';$phone8='12345';$phone9='12345';

        $address='gchiocjsoij';$address1='gchiocjsoij';$address2='gchiocjsoij';$address3='gchiocjsoij';$address4='gchiocjsoij';
        $address5='gchiocjsoij';$address6='gchiocjsoij';$address7='gchiocjsoij';$address8='gchiocjsoij';$address9='gchiocjsoij'; 
        $arr['face']=$image;$arr['id']=$id;$arr['name']=$name;$arr['phone']=$phone;$arr['address']=$address;
        $arr1['face']=$image1;$arr1['id']=$id1;$arr1['name']=$name1;$arr1['phone']=$phone1;$arr1['address']=$address1;
        $arr2['face']=$image2;$arr2['id']=$id2;$arr2['name']=$name2;$arr2['phone']=$phone2;$arr2['address']=$address2;
        $arr3['face']=$image3;$arr3['id']=$id3;$arr3['name']=$name3;$arr3['phone']=$phone3;$arr3['address']=$address3;
        $arr4['face']=$image4;$arr4['id']=$id4;$arr4['name']=$name4;$arr4['phone']=$phone4;$arr4['address']=$address4;
        $arr5['face']=$image5;$arr5['id']=$id5;$arr5['name']=$name5;$arr5['phone']=$phone5;$arr5['address']=$address5;
        $arr6['face']=$image6;$arr6['id']=$id6;$arr6['name']=$name6;$arr6['phone']=$phone6;$arr6['address']=$address6;
        $arr7['face']=$image7;$arr7['id']=$id7;$arr7['name']=$name7;$arr7['phone']=$phone7;$arr7['address']=$address7;
        $arr8['face']=$image8;$arr8['id']=$id8;$arr8['name']=$name8;$arr8['phone']=$phone8;$arr8['address']=$address8;
        $arr9['face']=$image9;$arr9['id']=$id9;$arr9['name']=$name9;$arr9['phone']=$phone9;$arr9['address']=$address9;
        $array=array();    
        array_push($array, $arr,$arr1,$arr2,$arr3,$arr4,$arr5,$arr6,$arr7,$arr8,$arr9);        
        $array5['consumer']=$array;
        $json=json_encode($array5);
         echo $json;
/*        if($value){
            echo $json;
        }  */
    } 

    public function test2(){
        $va=$this->_param('Email');
        if($va){
            echo '1';
        }
    } 

}

/*$takelist=new ServiceAction();
$takelist->creatRegister();*/

    