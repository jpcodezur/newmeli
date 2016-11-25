<?php

namespace Usuarios\Model\Entity;

class Input {

    private $id;
    private $nombre;
    private $idPagina;
    private $label;
    private $orden;
    private $estado;
    private $required;
    private $tipo;
    private $control;
    private $respuesta;
    private $ayuda;
    private $respuestasRequeridas;
    private $isMagic;
    private $tamanio;
    private $linkAyuda;
    
    function getLinkAyuda() {
        return $this->linkAyuda;
    }

    function setLinkAyuda($linkAyuda) {
        $this->linkAyuda = $linkAyuda;
    }

    function getTamanio() {
        return $this->tamanio;
    }

    function setTamanio($tamanio) {
        $this->tamanio = $tamanio;
    }

    
    function getIsMagic() {
        return $this->isMagic;
    }

    function setIsMagic($isMagic) {
        $this->isMagic = $isMagic;
    }

    
    function getRespuestasRequeridas() {
        return $this->respuestasRequeridas;
    }

    function setRespuestasRequeridas($respuestasRequeridas) {
        $this->respuestasRequeridas = $respuestasRequeridas;
    }

        
    function getAyuda() {
        return $this->ayuda;
    }

    function setAyuda($ayuda) {
        $this->ayuda = $ayuda;
    }

    function getRespuesta($id=1) {
        $id = $id;
        if(is_array($this->respuesta)){
        foreach($this->respuesta as $r){
            if($r->numero_respuesta == $id){
                return $r->texto;
            }
        }
        }else{
            return $this->respuesta;
        }
    }

    function setRespuesta($respuesta) {
        $this->respuesta = $respuesta;
    }

    function getControl() {
        return $this->control;
    }

    function setControl($control) {
        $this->control = $control;
    }

    function getNombre() {
        return $this->nombre;
    }

    function setNombre($nombre) {
        $this->nombre = $nombre;
    }

    function getId() {
        return $this->id;
    }

    function getIdPagina() {
        return $this->idPagina;
    }

    function getLabel() {
        return $this->label;
//        $separadores = array("?", ".", "!", ":", ";", ",");
//
//        $res = "";
//
//        $respuesta = "";
//        $palabras = explode(" ", trim($this->label));
//        foreach ($palabras as $palabra) {
//            $s = $palabra. " ";
//            if (strpos($palabra, "@") === 0) {
//                $s = "<span class='mg'>";
//                foreach ($separadores as $separador) {
//                    $pos = strpos($palabra, $separador);
//                    if ($pos !== false) {
//                        $separador_temp = $separador;
//                        $palabra = substr($palabra, 0, $pos);
//                    }
//                }
//                $s .=$palabra."</span>";
//            }
//            $respuesta.= $s;
//        }
//
//        return $respuesta;
    }

    function getOrden() {
        return $this->orden;
    }

    function getEstado() {
        return $this->estado;
    }

    function getRequired() {
        return $this->required;
    }

    function getTipo() {
        return $this->tipo;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setIdPagina($idPagina) {
        $this->idPagina = $idPagina;
    }

    function setLabel($label) {
        $this->label = $label;
    }

    function setOrden($orden) {
        $this->orden = $orden;
    }

    function setEstado($estado) {
        $this->estado = $estado;
    }

    function setRequired($required) {
        $this->required = $required;
    }

    function setTipo($tipo) {
        $this->tipo = $tipo;
    }
    public function dummi(){}
}
