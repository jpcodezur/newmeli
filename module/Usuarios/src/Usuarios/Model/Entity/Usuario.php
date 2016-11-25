<?php

namespace Usuarios\Model\Entity;

class Usuario {

    private $id;
    private $nombre;
    private $apellido;
    private $email;
    private $clave;
    private $avatar;
    private $tipo;
    private $alertas;
    private $estado;
    private $claveActivacion;

    public function __construct($id = null, $nombre = null, $apellido = null, $email = null, $clave = null, $estado = 1) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->email = $email;
        $this->clave = $clave;
        $this->estado = $estado;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function getApellido() {
        return $this->apellido;
    }

    public function getEmail() {
        return $this->email;
    }
    
    public function getAvatar(){
        return $this->avatar;
    }
    
    public function getTipo(){
        return $this->tipo;
    }
    
    public function getClave(){
        return $this->clave;
    }
    
    public function getAlertas(){
        return $this->alertas;
    }
    
    public function getEstado() {
        return $this->estado;
    }

    public function setNombre($param) {
        $this->nombre = $param;
    }

    public function setApellido($param) {
        $this->apellido = $param;
    }
    
    public function setTipo($param) {
        $this->tipo = $param;
    }
    
    public function setEstado($param) {
        $this->estado = $param;
    }
    
    public function setAlertas($param) {
        $this->alertas = $param;
    }

    public function setId($param) {
        $this->id = $param;
    }
    
    public function setAvatar($img){
        $this->avatar = $img;
    }
    
    public function setEmail($param){
        $this->email = $param;
    }
    
    public function setClave($param){
        $this->clave = $param;
    }
    
    public function setClaveActivacion($param){
        $this->claveActivacion = $param;
    }
    public function getClaveActivacion(){
        return $this->claveActivacion;
    }
    
}