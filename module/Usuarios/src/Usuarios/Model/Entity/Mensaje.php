<?php

namespace Usuarios\Model\Entity;

class Mensaje {

    private $id;
    private $idEmisor;
    private $idReceptor;
    private $mensaje;
    private $asunto;
    private $fechaLeido;
    private $fechaCreado;
    private $estado;
    private $avatarEvaluador;
    private $evaluador;

    function __construct($id="",  $idEmisor="",  $idReceptor="",  $mensaje="",  $asunto="",  $fechaLeido="",  $fechaCreado="",  $estado="",$evaluador="",$avatarEvaluador="") {
        $this->id = $id;
        $this->idEmisor = $idEmisor;
        $this->idReceptor = $idReceptor;
        $this->mensaje = $mensaje;
        $this->asunto = $asunto;
        $this->fechaLeido = $fechaLeido;
        $this->fechaCreado = $fechaCreado;
        $this->estado = $estado;
        $this->evaluador = $evaluador;
        $this->avatarEvaluador = $avatarEvaluador;
    }
    
    public function getEvaluador(){
        return $this->evaluador;
    }
    
    public function setEvaluador($p){
        $this->evaluador = $p;
    }
    
    public function getAvatarEvaluador(){
        return $this->avatarEvaluador;
    }
    
    public function setAvatarEvaluador($p){
        $this->avatarEvaluador = $p;
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getIdEmisor() {
        return $this->idEmisor;
    }

    public function setIdEmisor($idEmisor) {
        $this->idEmisor = $idEmisor;
    }

    public function getIdReceptor() {
        return $this->idReceptor;
    }

    public function setIdReceptor($idReceptor) {
        $this->idReceptor = $idReceptor;
    }

    public function getMensaje() {
        return $this->mensaje;
    }

    public function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
    }

    public function getAsunto() {
        return $this->asunto;
    }

    public function setAsunto($asunto) {
        $this->asunto = $asunto;
    }

    public function getFechaLeido() {
        return $this->fechaLeido;
    }

    public function setFechaLeido($fechaLeido) {
        $this->fechaLeido = $fechaLeido;
    }

    public function getFechaCreado() {
        return $this->fechaCreado;
    }

    public function setFechaCreado($fechaCreado) {
        $this->fechaCreado = $fechaCreado;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

}

