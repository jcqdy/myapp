<?php
/**
 *处理用户上传图片的类
 */
class ImageAction extends Action{

    public $imgsrc;

/**
 *处理用户注册上传图片的方法
 */
    public function uploadRegister(){
        $filename=$_FILES['image']['name'];//这个是源文件名
        $tmpname=$_FILES['image']['tmp_name'];// 临时文件地址
        $filetype=$_FILES['image']['type'];
        $filesize=$_FILES['image']['size'];
        
        $dirname .= DIRECTORY_SEPARATOR.date('Y');  //新建年
        if(!file_exists($dirname)){
            mkdir($dirname);
        }                
        $dirname .= DIRECTORY_SEPARATOR.date('m');  //新建月
        if(!file_exists($dirname)){
            mkdir($dirname);
        }                
        $dirname .= DIRECTORY_SEPARATOR.date('d');  //新建日
        if(!file_exists($dirname)){
            mkdir($dirname);
        }

        $type=substr($filetype,(strrpos($filetype,'/')+1));
        $filename=date('Y'.'m'.'d').$filename.".".$type;
        move_uploaded_file($tmpname,$dirname.DIRECTORY_SEPARATOR.$filename);
        $this->imgsrc = './Uploads/image/'.date('Y').'/'.date('m').'/'.date('d').'/'.$filename;
    }

}