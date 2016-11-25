<?php

namespace Usuarios\Model\Entity;

class Imagen {

    private $id;
    private $idInput;
    private $archivo;
    private $maxSize;
    private $extAllow;
    
    function getMaxSize() {
        return $this->maxSize;
    }

    function getExtAllow() {
        return $this->extAllow;
    }

    function setMaxSize($maxSize) {
        $this->maxSize = $maxSize;
    }

    function setExtAllow($extAllow) {
        $this->extAllow = $extAllow;
    }

        
    function getId() {
        return $this->id;
    }

    function getIdInput() {
        return $this->idInput;
    }

    function getArchivo() {
        return $this->archivo;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIdInput($idInput) {
        $this->idInput = $idInput;
    }

    function setArchivo($archivo) {
        $this->archivo = $archivo;
    }


}