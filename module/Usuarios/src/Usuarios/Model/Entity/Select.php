<?php

namespace Usuarios\Model\Entity;

class Select{

    private $id;
    private $idInput;
    private $tipo;
    private $values;
    private $respuestasRequeridas;

    function getId() {
        return $this->id;
    }

    function getIdInput() {
        return $this->idInput;
    }

    function getTipo() {
        return $this->tipo;
    }

    function getRespuestasRequeridas() {
        return $this->respuestasRequeridas;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIdInput($idInput) {
        $this->idInput = $idInput;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    
    function setValues($p){
        $this->values = $p;
    }
    
    function getValues(){
        return $this->values;
    }

    function setRespuestasRequeridas($respuestasRequeridas) {
        $this->respuestasRequeridas = $respuestasRequeridas;
    }


}