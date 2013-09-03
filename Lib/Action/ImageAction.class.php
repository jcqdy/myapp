<?php
/**
 *处理用户上传图片的类
 */
class ImageAction extends Action{

    public $imgurl;
    public $facemixsrc;
    public $imgmixsrc;
    public $filename;
    public $tmpname;
    public $filetype;
    public $filesize;

/**
 *用户头像上传并储存的方法
 */
    public function faceUpload(){
        $this->filename=$_FILES['image']['name'];//这个是源文件名
        $this->tmpname=$_FILES['image']['tmp_name'];// 临时文件地址
        $this->filetype=$_FILES['image']['type'];
        $this->filesize=$_FILES['image']['size'];       
//        $ip =  get_client_ip();    
        $arr=explode('.', $this->filename);
        $this->filename=time().$arr['0'].'.'.$arr['1'];
        $dirname .= 'Uploads'.DIRECTORY_SEPARATOR.'face'.DIRECTORY_SEPARATOR.'1';  //新建年
        if(!file_exists($dirname)){
            mkdir($dirname);
        }                

        $url=$dirname.DIRECTORY_SEPARATOR.$this->filename;

        move_uploaded_file($this->tmpname,$url);
        $this->faceMake($dirname,$url);

    }

/**
 *用户头像压缩并储存的方法
 */
    public function faceMake($dirname,$url){
        $imgtype=substr($this->filetype,(strrpos($this->filetype,'/')+1));     
        $quality=12;
        $arr=explode('.', $this->filename);
        $mixname=$arr['0'].'$'.'.'.$arr['1'];
        var_dump($mixname);
        switch ($imgtype) {
            case 'jpeg':
                $im=imagecreatefromjpeg($url);
                break;
            
            case 'png':
                $im=imagecreatefrompng($url);
                break;
        }     
        var_dump($im);
        $this->facemixsrc=$dirname.DIRECTORY_SEPARATOR.$mixname;
        imagejpeg($im,$this->facemixsrc,$quality);
    }

/**
 *用户头像上传并储存的方法
 */
    public function imageUpload(){
        $this->filename=$_FILES['image']['name'];//这个是源文件名
        $this->tmpname=$_FILES['image']['tmp_name'];// 临时文件地址
        $this->filetype=$_FILES['image']['type'];
        $this->filesize=$_FILES['image']['size'];       
//        $ip =  get_client_ip();    
        $arr=explode('.', $this->filename);
        $this->filename=time().$arr['0'].'.'.$arr['1'];
        $dirname .= 'Uploads'.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR.'1';  //新建年
        if(!file_exists($dirname)){
            mkdir($dirname);
        }                

        $url=$dirname.DIRECTORY_SEPARATOR.$this->filename;

        move_uploaded_file($this->tmpname,$url);
        $this->imageMake($dirname,$url);
        
    }

/**
 *用户头像压缩并储存的方法
 */
    public function imageMake($dirname,$url){
        $imgtype=substr($this->filetype,(strrpos($this->filetype,'/')+1));     
        $quality=12;
        $arr=explode('.', $this->filename);
        $mixname=$arr['0'].'$'.'.'.$arr['1'];
        var_dump($mixname);
        switch ($imgtype) {
            case 'jpeg':
                $im=imagecreatefromjpeg($url);
                break;
            
            case 'png':
                $im=imagecreatefrompng($url);
                break;
        }     
        var_dump($im);
        $this->imgmixsrc=$dirname.DIRECTORY_SEPARATOR.$mixname;
        imagejpeg($im,$this->imgmixsrc,$quality);
    }
}