<?php

namespace Usuarios\Model\Entity;

class Pagina {

    private $id;
    private $titulo;
    private $orden;
    private $estado;
    private $inputs;
    
    function getInputs() {
        return $this->inputs;
    }

    function setInputs($inputs) {
        $this->inputs = $inputs;
    }

        
    public function __construct(){
        
    }
    function getId() {
        return $this->id;
    }

    function getTitulo() {
        return $this->titulo;
    }

    function getOrden() {
        return $this->orden;
    }

    function getEstado() {
        return $this->estado;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setTitulo($titulo) {
        $this->titulo = $titulo;
    }

    function setOrden($orden) {
        $this->orden = $orden;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    
}

