<?php

namespace Usuarios\Model;

use Zend\Db\Adapter\Adapter;

class Connect {

    public static $instance = null;
    private $adapter;
    
    private function __construct() {
        $this->adapter = new Adapter(array(
            'host' => '127.0.0.1',
            'driver' => 'Mysqli',
            'database' => 'tesis_en_linea',
            'username' => 'root',
            'password' => '123',
            'charset' => 'utf8'
        ));
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Connect();
        }

        return self::$instance;
    }
    
    public function getAdapter(){
        return $this->adapter;
    }


    public function arrayToObject($array,$Class){
        $unObj = new $Class();
        foreach($array as $key => $value){
            $unObj->$key = $value;
        }
        return $unObj;
    }
    
    public function exist($table,$id,$field="id"){
        $sql = "SELECT COUNT(*) as TOTAL FROM $table WHERE $field = '" . $id . "'" ;
        $res = $this->adapter->query($sql);
        if($res){
            $result = $res->execute();
            foreach($result as $r){
                return $r["TOTAL"];
            }
        }
        return false;
    }
    
    public function getMaxId($table,$id="id"){
        $sql = "SELECT MAX($id) as MAXID FROM $table";
        $res = $this->adapter->query($sql);
        if($res){
            $result = $res->execute();
            foreach($result as $r){
                return $r["MAXID"];
            }
        }
        return false;
    }
    
    public function getObjectPorId($id="id",$table,$sql=""){
        $sql = "SELECT * FROM $table $sql WHERE id =".$id;
        $res = $this->adapter->query($sql);
        if($res){
            $result = $res->execute();
            foreach($result as $r){
                return $r;
            }
        }
        return false;
    }
    
    public function getObjectPorNombre($nombre="nombre",$table,$sql=""){
        $sql = "SELECT * FROM $table $sql WHERE nombre ='".$nombre."'";
        $res = $this->adapter->query($sql);
        if($res){
            $result = $res->execute();
            foreach($result as $r){
                return $r;
            }
        }
        return false;
    }
    
    

}