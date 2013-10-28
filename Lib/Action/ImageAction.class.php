<?php
/**
 *处理用户上传图片的类
 */
class ImageAction extends Action{

    public $imgurl=array();
    public $imgmixurl=array();
    public $imgmixsrc;
    public $faceurl;
    public $facemixurl;
    public $facemixsrc;
    public $filename;
    public $tmpname;
    public $filetype;
    public $filesize;
    public $state;
    public $id;
    
/**
 *消费者头像上传并储存的方法
 */
    public function consumerFace(){
            $this->filename=$_FILES['file']['name'];
            $this->tmpname=$_FILES['file']['tmp_name'];
            $this->id=$_FILES['file']['type'];
            $dirname= 'Uploads'.DIRECTORY_SEPARATOR.'face'.DIRECTORY_SEPARATOR.'consumer'.DIRECTORY_SEPARATOR.$this->id;
            mkdir($dirname);
            $arr=explode('.',$this->filename);
            $this->filename=time().$arr['0'].'.'.$arr['1'];
            $url=$dirname.DIRECTORY_SEPARATOR.$this->filename;
            $dh=opendir($dirname);
            while ($file=readdir($dh)) {
                if($file!="." && $file!="..") {
                    $fullpath=$dirname.DIRECTORY_SEPARATOR.$file;
                    if(!is_dir($fullpath)) {
                        unlink($fullpath);
                    } 
                }
            }
            move_uploaded_file($this->tmpname,$url);
            $this->faceurl='192.168.1.100/myapp/Uploads/face/consumer/'.$this->id.'/'.$this->filename;
            $this->consumer_faceMake($dirname,$url);
    }

/**
 *商家头像上传并储存的方法
 */
    public function serviceFace(){
            $this->filename=$_FILES['file']['name'];
            $this->tmpname=$_FILES['file']['tmp_name'];
            $this->id=$_FILES['file']['type'];
            $dirname= 'Uploads'.DIRECTORY_SEPARATOR.'face'.DIRECTORY_SEPARATOR.'service'.DIRECTORY_SEPARATOR.$this->id;
            mkdir($dirname);
            $arr=explode('.',$this->filename);
            $this->filename=time().$arr['0'].'.'.$arr['1'];
            $url=$dirname.DIRECTORY_SEPARATOR.$this->filename;
            $dh=opendir($dirname);
            while ($file=readdir($dh)) {
                if($file!="." && $file!="..") {
                    $fullpath=$dirname.DIRECTORY_SEPARATOR.$file;
                    if(!is_dir($fullpath)) {
                        unlink($fullpath);
                    } 
                }
            }
            move_uploaded_file($this->tmpname,$url);
            $this->faceurl='192.168.1.100/myapp/Uploads/face/service/'.$this->id.'/'.$this->filename;
            $this->service_faceMake($dirname,$url);
    }

/**
 *消费者头像压缩并储存的方法
 */
    public function consumer_faceMake($dirname,$url){
        $quality=10;
        $arr=explode('.', $this->filename);
        $mixname=$arr['0'].'$'.'.'.$arr['1'];
        switch ($arr['1']) {
            case 'jpg':
                $im=imagecreatefromjpeg($url);
                break;

            case 'jpeg':
                $im=imagecreatefromjpeg($url);
                break;

            case 'png':
                $im=imagecreatefrompng($url);
                break;
        }     
        $this->facemixsrc=$dirname.DIRECTORY_SEPARATOR.$mixname;
        imagejpeg($im,$this->facemixsrc,$quality);
        $this->facemixurl='192.168.1.100/myapp/Uploads/face/consumer/'.$this->id.'/'.$mixname;
    }

/**
 *商家头像压缩并储存的方法
 */
    public function service_faceMake($dirname,$url){
        $quality=10;
        $arr=explode('.', $this->filename);
        $mixname=$arr['0'].'$'.'.'.$arr['1'];
        switch ($arr['1']) {
            case 'jpg':
                $im=imagecreatefromjpeg($url);
                break;

            case 'jpeg':
                $im=imagecreatefromjpeg($url);
                break;

            case 'png':
                $im=imagecreatefrompng($url);
                break;
        }     
        $this->facemixsrc=$dirname.DIRECTORY_SEPARATOR.$mixname;
        imagejpeg($im,$this->facemixsrc,$quality);
        $this->facemixurl='192.168.1.100/myapp/Uploads/face/service/'.$this->id.'/'.$mixname;
    }


/**
 *商家图片上传并储存的方法
 */
    public function imageUpload(){
        $serviceid;
        $json=$this->_param('Json');
        $json=html_entity_decode($json);
        $this->state=json_decode($json,true);
        for($i=0;$i<count($_FILES['file']['tmp_name']);$i++){
            $this->filename=$_FILES['file']['name'][$i];
            $this->tmpname=$_FILES['file']['tmp_name'][$i];
            $dirname= 'Uploads'.DIRECTORY_SEPARATOR.'image'.DIRECTORY_SEPARATOR.$serviceid;
            mkdir($dirname);
            $arr=explode('.',$this->filename);
            $this->filename=time().$arr['0'].'.'.$arr['1'];
            $url=$dirname.DIRECTORY_SEPARATOR.$this->filename;
            move_uploaded_file($this->tmpname,$url);
            $this->imgurl[$i]='192.168.1.100/myapp/Uploads/image/'.$serviceid.'/'.$this->filename;
            $this->imageMake($dirname,$url,$i,$serviceid);
        }        
    }

/**
 *用户图片压缩并储存的方法
 */
    public function imageMake($dirname,$url,$i,$serviceid){
        $quality=30;
        $arr=explode('.', $this->filename);
        $mixname=$arr['0'].'$'.'.'.$arr['1'];
        switch ($arr['1']) {
            case 'jpg':
                $im=imagecreatefromjpeg($url);
                break;

            case 'jpeg':
                $im=imagecreatefromjpeg($url);
                break;

            case 'png':
                $im=imagecreatefrompng($url);
                break;
        }     
        $this->imgmixsrc=$dirname.DIRECTORY_SEPARATOR.$mixname;
        imagejpeg($im,$this->imgmixsrc,$quality);
        $this->imgmixurl[$i]='192.168.1.100/myapp/Uploads/face/'.$serviceid.'/'.$mixname;
    }
    
}