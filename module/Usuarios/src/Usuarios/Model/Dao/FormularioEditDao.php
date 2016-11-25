<?php

namespace Usuarios\Model\Dao;

use Usuarios\Model\Entity\Pagina;
use Usuarios\Model\Entity\Input;

class FormularioEditDao {

    private $adapter;

    public function __construct($adapter) {
        $this->adapter = $adapter;
    }

    public function saveOrdenPaginas($paginas) {
        foreach ($paginas as $pagina) {
            $sql = "UPDATE paginas set orden = " . $pagina["orden"] . " WHERE id= " . $pagina["id"];
            $res = $this->adapter->query($sql);
            $result = $res->execute();
            if (!$result) {
                return false;
            }
        }

        return true;
    }

    public function saveOrdenItems($inputs) {
        foreach ($inputs as $input) {
            $sql = "UPDATE inputs set orden = " . $input["orden"] . " WHERE id= " . $input["id"];
            $res = $this->adapter->query($sql);
            $result = $res->execute();
            if (!$result) {
                return false;
            }
        }

        return true;
    }

    public function getPaginas() {
        $sql = "SELECT * FROM paginas WHERE estado = 1 ORDER BY orden ASC";

        $paginas = array();

        $res = $this->adapter->query($sql);

        if ($res) {
            $result = $res->execute();
            foreach ($result as $r) {
                $unaPagina = new Pagina();
                $unaPagina->setId($r["id"]);
                $unaPagina->setTitulo($r["titulo"]);
                $unaPagina->setEstado($r["estado"]);
                $unaPagina->setOrden($r["orden"]);
                $inputs = $this->getInputs($unaPagina->getId());
                $unaPagina->setInputs($inputs);
                $paginas[] = $unaPagina;
            }
        }

        return $paginas;
    }

    public function getPagina($id) {
        $sql = "SELECT * FROM paginas WHERE estado = 1 AND id=$id ORDER BY orden ASC";

        $unaPagina = new Pagina;

        $res = $this->adapter->query($sql);

        if ($res) {
            $result = $res->execute();
            foreach ($result as $r) {
                $unaPagina->setId($r["id"]);
                $unaPagina->setTitulo($r["titulo"]);
                $unaPagina->setEstado($r["estado"]);
                $unaPagina->setOrden($r["orden"]);
                $inputs = $this->getInputs($unaPagina->getId());
                $unaPagina->setInputs($inputs);
                return $unaPagina;
            }
        }

        return $unaPagina;
    }

    public function getInputs($idPagina) {
        $sql = "SELECT * FROM inputs WHERE estado = 1 AND id_pagina = $idPagina ORDER BY orden ASC";

        $inputs = array();

        $res = $this->adapter->query($sql);

        if ($res) {
            $result = $res->execute();
            foreach ($result as $r) {
                $unInput = new Input();
                $unInput->setId($r["id"]);
                $unInput->setIdPagina($r["id_pagina"]);
                $unInput->setLabel($r["label"]);
                $unInput->setEstado($r["estado"]);
                $unInput->setOrden($r["orden"]);
                $unInput->setNombre($r["nombre"]);
                $unInput->setRequired($r["required"]);
                $unInput->setTipo($r["tipo_input"]);
                $unInput->setAyuda($r["ayuda"]);

                $inputs[] = $unInput;
            }
        }

        return $inputs;
    }

    public function getInput($idinput) {
        $sql = "SELECT * FROM inputs WHERE estado = 1 AND id = $idinput";

        $unInput = new Input();

        $res = $this->adapter->query($sql);

        if ($res) {
            $result = $res->execute();
            foreach ($result as $r) {
                $unInput->setId($r["id"]);
                $unInput->setIdPagina($r["id_pagina"]);
                $unInput->setLabel($r["label"]);
                $unInput->setEstado($r["estado"]);
                $unInput->setOrden($r["orden"]);
                $unInput->setNombre($r["nombre"]);
                $unInput->setRequired($r["required"]);
                $unInput->setTipo($r["tipo_input"]);
                $unInput->setLinkAyuda($r["link_ayuda"]);
                $unInput->setAyuda($r["ayuda"]);
                //$unInput->setEjemplo($r["ayuda"]);
                $unInput->setTamanio($r["tamanio"]);
                switch ($r["tipo_input"]) {
                    case "dropdown":

                        $select = $this->getSelect($unInput->getId());
                        $values = $this->getValuesSelect($unInput->getId());
                        $select->setValues($values);
                        $unInput->setControl($select);
                        break;
                    case "texto":
                        $texto = $this->getTexto($unInput->getId());
                        $unInput->setControl($texto);
                        break;
                    case "fecha":
                        $fecha = $this->getFecha($unInput->getId());
                        $unInput->setControl($fecha);
                        break;

                    default:
                        break;
                }
                return $unInput;
            }
        }

        return $inputs;
    }
    
    public function getFecha($id){
        $fecha = new \Usuarios\Model\Entity\Fecha();

        $sql = "SELECT * FROM input_fecha WHERE id_input=" . $id;

        $result = $this->adapter->query($sql)->execute();

        if ($result) {
            foreach ($result as $res) {
                $fecha->setId($res["id"]);
                $fecha->setTipoFecha($res["tipo_fecha"]);
                return $fecha;
            }
        }

        return $fecha;
    }
    
    public function getTexto($id){
        $input = new \Usuarios\Model\Entity\Texto();

        $sql = "SELECT * FROM input_texto WHERE id_input=" . $id;

        $result = $this->adapter->query($sql)->execute();

        if ($result) {
            foreach ($result as $res) {
                $input->setId($res["id"]);
                $input->setRespuestasRequeridas($res["respuestas_requeridas"]);
                $input->setEjemplo($res["ejemplo"]);
                $input->setValidacion($res["validacion"]);
                return $input;
            }
        }

        return $input;
    }
    
    public function getSelect($id) {
        $select = new \Usuarios\Model\Entity\Select();

        $sql = "SELECT * FROM input_select WHERE id_input=" . $id;

        $result = $this->adapter->query($sql)->execute();

        if ($result) {
            foreach ($result as $res) {
                $select->setId($res["id"]);
                $select->setIdInput($res["id_input"]);
                $select->setRespuestasRequeridas($res["respuestas_requeridas"]);
                $select->setTipo($res["tipo"]);
                return $select;
            }
        }

        return $select;
    }

    public function getValuesSelect($idInput) {
        $sql = " SELECT * FROM select_collections as sc  
                INNER JOIN input_select as iss      
                on sc.id_input = iss.id_input 
                WHERE iss.id_input =  $idInput";

        $results = array();

        $res = $this->adapter->query($sql);

        if ($res) {
            $result = $res->execute();
            foreach ($result as $res) {
                $selectValue = new \Usuarios\Model\Entity\SelectValues();
                $selectValue->setId($res["id_select"]);
                $selectValue->id_input = $res["id_input"];
                $selectValue->setValue($res["value"]);
                $results[] = $selectValue;
            }
        }

        return $results;
    }

}
