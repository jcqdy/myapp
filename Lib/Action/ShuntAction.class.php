<?php
class ShuntAction extends Action{

    public function allAutologin(){
        $encrypt=$this->_param('encrypt');
        $email=$this->_param('Email');
        $pass =$this->_param('Password');
        if ($character='consumer') {
            $consumer_login=new ConsumerAction();
            $consumer_login->login($email,$pass);
        }elseif ($character='service') {
            $service_login=new ServiceAction();
            $service_login->login($email,$pass);
        }
    }
}