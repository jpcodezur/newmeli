<?php

namespace Usuarios\Model\Entity;

class Articulo {

    private $id;
    public $categorias;
    public $nombre;
    public $estado;

    public function __construct($id = null, $categorias = null) {
        $this->id = $id;
        $this->categorias = $categorias;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getCategorias() {
        return $this->categorias;
    }

    public function setId($param) {
        $this->id = $param;
    }

    public function setCategorias($param) {
        $this->categorias = $param;
    }

    public function setEstado($param){
        $this->estado = $param;
    }

    public function getEstado(){
        return $this->estado;
    }

    public function setNombre($param){
        $this->nombre = $param;
    }

    public function getNombre(){
        return $this->nombre;
    }
	
	public function setMlCategorias($param) {
        $this->mlCategorias = $param;
    }
	
	public function getMlCategorias() {
        return $this->mlCategorias;
    }
	
	

}