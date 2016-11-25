<?php

namespace Usuarios\Model\Dao;

use Usuarios\MisClases\Respuesta;

class PreguntaDao {

    protected $tableGateway;
    protected $params;

    public function __construct($tableGateway = null, $adapter = null) {
        $this->tableGateway = $tableGateway;
    }
    
    public function getTableGateway(){
        return $this->tableGateway;
    }
    
    public function setParams($params){
        $this->params = $params;
    }
    
    public function addParams($params){
        $this->params[] = $params;
    }

    public function setJoin($select) {
        if (isset($this->params["join"])) {
            $attr = $this->params["join"];
            if (is_array($attr)) {
                $select->join(
                        $attr["alias"], $attr["on"], $attr["alias_field"], $attr["type"]
                );
            }
        }

        return $select;
    }

    public function setJoinFields($obj,$unaEntity) {
        if (isset($this->params["join"])) {
            $attr = $this->params["join"];
            foreach ($attr["alias_field"] as $key  => $value) {
                foreach($obj as $k => $p){
                    if($key == $k){
                        $unaEntity->$key = $obj[$k];
                    }
                }
            }
        }

        return $unaEntity;
    }

    public function fetchAll() {

        $enties = array();

        $sql = $this->tableGateway->getSql();

        $select = $this->tableGateway->getSql()->select();

        $select->where(array(strtolower($this->params["table"]) . ".estado" => "1"));

        $select = $this->setJoin($select);

        $select->order('id ASC');

        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        //$salida = $select->getSqlString();

        foreach ($this->tableGateway->selectWith($select) as $entity) {

            $unaEntity = new $this->params["entity"]();

            foreach ($this->params["attrs"] as $attr) {
                $set = "set" . ucwords($attr);
                $unaEntity->$set($entity[$attr]);
            }

            $unaEntity = $this->setJoinFields($entity,$unaEntity);

            $enties[] = $unaEntity;
        }
        
        return array("entities" => $enties, "paginator" => $paginator);
    }
    
    public function fetchOneLike($query){
        $select = $this->tableGateway->getSql()->select();
        
        $select->where->like('nombre', "%".$query."%");

        $entities = $this->tableGateway->selectWith($select);
        
        foreach ($entities as $entity) {
            /*'id'    : 1,
            'name'  : 'Kenneth Auchenberg',
            'avatar': 'http://cdn0.4dots.com/i/customavatars/avatar7112_1.gif',
            'icon'  : 'icon-16 icon-person',
            'type'  : 'contact'*/

            return array(
                "id" => $entity["id"],
                "name" => $entity["nombre"],
                "avatar" => "",
                "icon" => "",
                "type" => "contact",
            );
        }

        return false;
    }

    public function fetchOne($param) {

        $select = $this->tableGateway->getSql()->select();

        $select->where($param);

        $entities = $this->tableGateway->selectWith($select);
        
        foreach ($entities as $entity) {

            $unaEntity = new $this->params["entity"]();
            
            foreach ($this->params["attrs"] as $attr) {
                $set = "set" . ucwords($attr);
                $unaEntity->$set($entity[$attr]);
            }

            return $unaEntity;
        }

        return false;
    }

    public function guardar($unaEntity) {
        
        $data = array();
        
        foreach ($this->params["add_attrs"] as $attr) {
            $get = "get" . ucwords($attr);
            $data[$attr] = $unaEntity->$get();
        }
        
        if($unaEntity->getRequerida() == "on"){
            $data["requerida"] = 1;
        }
        
        if($unaEntity->getEs_pregunta() == "on"){
            $data["es_pregunta"] = 1;
        }
        
        $saveValidator = "\Usuarios\Model\Dao\Validators\Save" . ucwords($this->params["controller"]);
        
        $validate = new $saveValidator($this->tableGateway, $this, $this->params);
        
        $respuesta = $validate->validate($unaEntity);
        
        if($respuesta->getError() === false){

            $result = $this->tableGateway->insert($data);

            $respuesta->setError(false);
            $respuesta->setMensaje(ucwords($this->params["singular"])." saved successfully");

            if (!$result) {
                $respuesta->setError(true);
                $respuesta->setMensaje("Error creating ".$this->params["singular"]);
            }
        }

        return $respuesta;
    }

    public function update($unaEntity) {
        
        $data = array();
        
        foreach ($this->params["edit_attrs"] as $attr) {
            $get = "get" . ucwords($attr);
            $data[$attr] = $unaEntity->$get();
        }
        
        if($unaEntity->getRequerida() == "on"){
            $data["requerida"] = 1;
        }
        
        if($unaEntity->getEs_pregunta() == "on"){
            $data["es_pregunta"] = 1;
        }
        
        $editValidator = "\Usuarios\Model\Dao\Validators\Edit" . ucwords($this->params["controller"]);
        
        $validate = new $editValidator($this->tableGateway, $this, $this->params);
        
        $respuesta = $validate->validate($unaEntity);
        
        if($respuesta->getError() === false){

            $result = $this->tableGateway->update($data, array("id" => $unaEntity->getId()));

            $respuesta->setError(false);
            $respuesta->setMensaje(ucwords($this->params["singular"])." saved successfully");

            if (!$result) {
                $respuesta->setError(true);
                $respuesta->setMensaje("Error saving ".$this->params["singular"]);
            }
        }

        return $respuesta;
    }

    public function delete($id) {
        $respuesta = new Respuesta();

        $result = $this->tableGateway->update(array("estado" => "0"), array("id" => $id));
        
        $respuesta->setError(false);
        $respuesta->setMensaje(ucwords($this->params["singular"])." deleted successfully");
        
        if (!$result) {
            $respuesta->setError(true);
            $respuesta->setMensaje("Error deleting user");
        }

        return $respuesta;
    }

}
