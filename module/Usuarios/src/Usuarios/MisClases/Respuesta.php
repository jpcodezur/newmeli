<?php

namespace Usuarios\MisClases;

class Respuesta{

    public $error = null;
    public $mensaje = "Ocurrio un Error.";
    
    public function __construct(){
        
    }
    
    function getError() {
        return $this->error;
    }

    function getMensaje() {
        return $this->mensaje;
    }

    function setError($error) {
        $this->error = $error;
    }

    function setMensaje($mensaje) {
        $this->mensaje = $mensaje;
    }


}