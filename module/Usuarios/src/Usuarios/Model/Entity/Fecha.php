<?php

namespace Usuarios\Model\Entity;

class Fecha {

    private $id;
    private $idInput;
    private $tipoFecha;
    private $desde;
    private $hasta;
    
    function getDesde() {
        return $this->desde;
    }

    function getHasta() {
        return $this->hasta;
    }

    function setDesde($desde) {
        $this->desde = $desde;
    }

    function setHasta($hasta) {
        $this->hasta = $hasta;
    }

        
    function getIdInput() {
        return $this->idInput;
    }

    function setIdInput($idInput) {
        $this->idInput = $idInput;
    }

    function getId() {
        return $this->id;
    }
    
    function setId() {
        return $this->id;
    }
    
    function getTipoFecha() {
        return $this->tipoFecha;
    }

    function setTipoFecha($tipoFecha) {
        $this->tipoFecha = $tipoFecha;
    }

}