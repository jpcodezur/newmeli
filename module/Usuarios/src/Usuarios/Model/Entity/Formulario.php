<?php

namespace Usuarios\Model\Entity;

class Formulario {

    private $id;
    private $paginas;
    
    function getId() {
        return $this->id;
    }

    function getPaginas() {
        return $this->paginas;
    }

    function setId($id) {
        $this->id = $id;
    }

    function setPaginas($paginas) {
        $this->paginas = $paginas;
    }


}