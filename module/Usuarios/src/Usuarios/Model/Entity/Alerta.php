<?php

namespace Usuarios\Model\Entity;

class Alerta {

    private $id;
    private $id_agente;
    private $estado;
    private $mensaje;
    private $url_audio;
    private $audio;
    private $asunto;
    private $fecha_creada;
    private $fecha_visto;
    
    function __construct($id="", $id_agente="", $estado="", $mensaje="", $url_audio="", $audio="", $asunto="", $fecha_creada="", $fecha_visto="") {
        $this->id = $id;
        $this->id_agente = $id_agente;
        $this->estado = $estado;
        $this->mensaje = $mensaje;
        $this->url_audio = $url_audio;
        $this->audio = $audio;
        $this->asunto = $asunto;
        $this->fecha_creada = $fecha_creada;
        $this->fecha_visto = $fecha_visto;
    }
    
    public function getId() {
        return $this->id;
    }

    public function getId_agente() {
        return $this->id_agente;
    }

    public function getEstado() {
        return $this->estado;
    }

    public function getMensaje() {
        return $this->mensaje;
    }

    public function getUrl_audio() {
        return $this->url_audio;
    }

    public function getAudio() {
        return $this->audio;
    }

    public function getAsunto() {
        return $this->asunto;
    }

    public function getFecha_creada() {
        return $this->fecha_creada;
    }

    public function getfecha_visto() {
        return $this->fecha_visto;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setId_agente($id_agente) {
        $this->id_agente = $id_agente;
    }

    public function setEstado($estado) {
        $this->estado = $estado;
    }

    public function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
    }

    public function setUrl_audio($url_audio) {
        $this->url_audio = $url_audio;
    }

    public function setAudio($audio) {
        $this->audio = $audio;
    }

    public function setAsunto($asunto) {
        $this->asunto = $asunto;
    }

    public function setFecha_creada($fecha_creada) {
        $this->fecha_creada = $fecha_creada;
    }

    public function setfecha_visto($fecha_visto) {
        $this->fecha_visto = $fecha_visto;
    }

}

