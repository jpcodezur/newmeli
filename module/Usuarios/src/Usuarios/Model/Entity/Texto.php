<?php

namespace Usuarios\Model\Entity;

class Texto{

    private $id;
    private $idInput;
    private $respuestasRequeridas;
    private $ejemplo;
    private $validacion;
    
    function getEjemplo() {
        return $this->ejemplo;
    }

    function getValidacion() {
        return $this->validacion;
    }

    function setEjemplo($ejemplo) {
        $this->ejemplo = $ejemplo;
    }

    function setValidacion($validacion) {
        $this->validacion = $validacion;
    }

        
    function getId() {
        return $this->id;
    }

    function getIdInput() {
        return $this->idInput;
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

    function setRespuestasRequeridas($respuestasRequeridas) {
        $this->respuestasRequeridas = $respuestasRequeridas;
    }
    
    function setSelect($s){
        $this->select = $s;
    }
    
    function getSelect($s){
        return $this->select;
    }


    
}