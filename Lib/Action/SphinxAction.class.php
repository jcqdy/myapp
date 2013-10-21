<?php
require './Common/sphinxapi.php';
class SphinxAction extends Action{
    public function test1(){
        $sphinx = new SphinxClient();
        $sphinx->SetServer ( 'localhost', 9312 );
        $sphinx->SetArrayResult ( true );
        $sphinx->SetMaxQueryTime(10);
        $sphinx->SetMatchMode ( "SPH_MATCH_EXTENDED2" );

        $index='info';
        $result=$sphinx->query('@serviceid=1',$index);
        var_dump($result);
    }
}