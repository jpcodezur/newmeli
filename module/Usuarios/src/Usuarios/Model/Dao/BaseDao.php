<?php

namespace Usuarios\Model\Dao;

class BaseDao {

    public function ArrayObjectToSelect($col){
        $array = array();
        
        foreach($col as $item){
            $array[$item->getId()] = $item->getNombre();
        }
        
        return $array;
    }
    
    public function toObject($array){
        $obj = false;
        if($array){
            $class = substr(get_class($this), 0,  strlen(get_class($this))-3);
            $class = str_replace("Dao", "Entity", $class);
            $obj = new $class();
            foreach($array as $key=>$value){
                $pos = strpos($key, "_");
                if($pos!==false){
                    $key = substr($key, 0, $pos) .strtoupper($key[$pos+1]).substr($key, $pos+2, strlen($key));
                }
                $set = "set".ucwords($key);
                $obj->$set($value);
            }
        }
        return $obj;
    }
    
}
