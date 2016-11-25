<?php

namespace Usuarios\Model\Entity;

class Categoria {

    private $id;
    private $nombre;

    public function __construct($id = null, $nombre = null) {
        $this->id = $id;
        $this->nombre = $nombre;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setId($param) {
        $this->id = $param;
    }

    public function setNombre($param) {
        $this->nombre = $param;
    }

}