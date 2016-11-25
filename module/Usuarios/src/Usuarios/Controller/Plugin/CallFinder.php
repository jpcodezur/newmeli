<?php

namespace Usuarios\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class CallFinder extends AbstractPlugin{
    
    public function __invoke($adapter,$array) {
        $this->adapter = $adapter;
        return $this->deDos($array);
    }
    
    public function getFiles($array){
        //File format
        $res = $this->adapter->query("SELECT * FROM ");
        return json_encode($res);
    }
    
}