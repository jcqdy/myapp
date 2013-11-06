<?php
require './Common/sphinxapi.php';
class SphinxAction extends Action{
    public function test1(){
        $sphinx = new SphinxClient();
        $sphinx->SetServer ( 'localhost', 9312 );
        $sphinx->SetArrayResult ( true );
        $sphinx->SetMaxQueryTime(10);
        $sphinx->SetMatchMode ( "SPH_MATCH_EXTENDED2" );
        $sphinx->SetLimits(0, 20, 1000);
        $index='serviceinfo';
        $result=$sphinx->query('10',$index);
        var_dump($result);
        $this->display();
    }

    public function test2(){
        $so = scws_new();
        $so->set_charset('utf8');
        // 这里没有调用 set_dict 和 set_rule 系统会自动试调用 ini 中指定路径下的词典和规则文件
        $index='梅赛德斯奔驰  专卖店';
//        $index=explode(' ', $index);
        $so->send_text($index);
        while ($tmp = $so->get_result()){      
            var_dump($tmp);
        }  
/*        $tmp = $so->get_result();
        var_dump($tmp);
        $so->close(); */
        $this->display();
    }
}