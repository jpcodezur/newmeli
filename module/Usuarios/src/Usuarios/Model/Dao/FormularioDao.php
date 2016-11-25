<?php

namespace Usuarios\Model\Dao;

use Usuarios\Model\Entity\Pagina;
use Usuarios\Model\Entity\Grupo;
use Usuarios\Model\Entity\Input;

class FormularioDao {

    private $adapter;

    public function __construct($adapter) {
        $this->adapter = $adapter;
    }

    public function getLastPage($idUsuario) {
        $sql = "select id_ultima_pagina_completa,terminado from usuario_formulario WHERE id_usuario = $idUsuario";

        $res = $this->adapter->query($sql);

        if ($res) {
            $result = $res->execute();
            foreach ($result as $r) {
                if (!$r["terminado"]) {
                    return $r["id_ultima_pagina_completa"];
                }
            }
        }

        return $this->getLastPageOrden();
    }

    public function getLastPageOrden() {

        $sql = "SELECT id FROM paginas WHERE orden = (SELECT MIN(orden) as minimo FROM `paginas` WHERE estado = 1) AND estado = 1";

        $res = $this->adapter->query($sql);

        if ($res) {
            $result = $res->execute();
            foreach ($result as $r) {
                return $r["id"];
            }
        }

        return false;
    }

    public function savePaginaCompletada($idUsuario, $idPagina, $terminado = false) {
        if ($this->getLastPage($idUsuario)) {
            $sql = "UPDATE TABLE usuario_formulario, set id_ultima_pagina_completa=$idPagina,terminado=$terminado  WHERE id_usuario = $idUsuario";
        } else {
            $sql = "INSERT INTO usuario_formulario (id_usuario,id_ultima_pagina_completa,terminado) VALUES ($idUsuario,$idPagina,$terminado";
        }

        $res = $this->adapter->query($sql);
        return $res->execute();
    }
    
    public function getUltimaPaginaCompletada($idUsuario){
        $sql = "SELECT DISTINCT MAX(id_pagina) as ultima 
                FROM respuestas as r
                INNER JOIN inputs as i on i.id = r.id_input
                WHERE id_usuario = $idUsuario";
        
        $res = $this->adapter->query($sql);
        
        if ($res) {
            $result = $res->execute();
            foreach ($result as $r) {
                $unInput = new Input();
                return $r["ultima"];
            }
        }
        
        return 1;
    }
    
    public function getFormulario($idPagina) {
        $sql = "SELECT * FROM inputs WHERE estado = 1 AND id_pagina = $idPagina ORDER BY orden ASC";

        $inputs = array();

        $res = $this->adapter->query($sql);

        $idAlumno = null;

        if(isset($_SESSION["miSession"]["usuario"])){
            $idAlumno = $_SESSION["miSession"]["usuario"]->getId();
        }
        
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
                $unInput->setRespuesta($this->getRespuestaTexto($r["id"],$idAlumno));

                switch ($unInput->getTipo()) {
                    case "dropdown":
                        $select = new \Usuarios\Model\Entity\Select();
                        $values = $this->getValuesSelect($unInput->getId());
                        $select->setValues($values);
                        $unInput->setControl($select);
                        break;

                    default:
                        break;
                }

                $inputs[] = $unInput;
            }
        }

        return $inputs;
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
                $selectValue->setValue($res["value"]);

                $idAlumno = null;
        
                if(isset($_SESSION["miSession"]["usuario"])){
                    $idAlumno = $_SESSION["miSession"]["usuario"]->getId();
                }
                
                $idRespuesta = $this->getIdRespuesta($res["id_input"],$idAlumno);
                    if($idRespuesta){
                    $isSelected = $this->getRespSelect($res["id_select"],$idAlumno,$idRespuesta);

                    if ($isSelected) {
                        $selectValue->setSelected(true);
                    }
                }

                $results[] = $selectValue;
            }
        }

        return $results;
    }

    public function getIdRespuesta($idInput,$idAlumno) {
        $sql ="SELECT id FROM respuestas WHERE id_input=$idInput AND id_usuario=$idAlumno";
        
        $result = $this->adapter->query($sql)->execute();
        
        if ($result) {
            foreach ($result as $res) {
                return $res["id"];
            }
        }

        return false;
    }
    
    public function getRespSelect($id,$idAlumno,$idRespuesta) {
        
        
        // SELECT * FROM respuesta_select WHERE id_respuesta = 42 AND id_usuario=5
        // AND
        // AND id_respuesta = $idRespuesta
        $sql = "SELECT * FROM respuesta_select WHERE id_respuesta = $idRespuesta AND id_usuario=$idAlumno";
        
        $result = $this->adapter->query($sql)->execute();

        if ($result) {
            foreach ($result as $rres) {
                if ($rres["id_select"] == $id) {
                    return true;
                }
            }
        }

        return false;
    }
    
    public function getRespuestaFecha($idInput,$idAlumno) {
        $sql = "SELECT rf.desde ,rf.hasta from respuesta_fecha as rf "
                . "INNER JOIN respuestas as r on r.id = rf.id_respuesta "
                . "WHERE r.id_input = $idInput "
                . "AND r.estado = 1 "
                . "AND r.id_usuario=$idAlumno";

        $results = array();

        $result = $this->adapter->query($sql)->execute();

        if ($result) {
            foreach ($result as $rres) {
                $hasta = "";
                if($rres["hasta"]){
                    $hasta = " - " . $rres["hasta"];
                }
                return $rres["desde"] . $hasta;
            }
        }

        return "";
    }
    
    public function getRespuestaTexto($idInput,$idAlumno,$numeroRespuestea="1") {
        $sql = "SELECT rt.texto as texto, numero_respuesta from respuesta_texto as rt "
                . "INNER JOIN respuestas as r on r.id = rt.id_respuesta "
                . "WHERE r.id_input = $idInput "
                . "AND r.estado = 1 "
                . "AND r.id_usuario=$idAlumno";

        $results = array();

        $res = $this->adapter->query($sql);

        if ($res) {
            $result = $res->execute();
            foreach ($result as $rres) {
                    $obj = new \stdClass();
                    $obj->texto = $rres["texto"];
                    $obj->numero_respuesta = $rres["numero_respuesta"];
                    $results[]  = $obj;
            }
        }

        return $results;
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
                $inputs = $this->getInputs($r["id"]);
                $unaPagina->setInputs($inputs);

                $paginas[] = $unaPagina;
            }
        }

        return $paginas;
    }

    public function isInputMagic($idPagina,$nombreInput){
        $sql = "SELECT id FROM inputs 
                WHERE id_pagina = $idPagina
                AND label LIKE '%@".$nombreInput."%'";
        
        $res = $this->adapter->query($sql)->execute();

        if ($res) {
            foreach ($res as $r) {
                return true;
            }
        }

        return false;
    }
    
    public function getInputs($idPagina) {
        $sql = "SELECT * FROM inputs WHERE estado = 1 AND id_pagina = $idPagina ORDER BY orden ASC";

        $inputs = array();

        $res = $this->adapter->query($sql);

        $idAlumno = null;

        if(isset($_SESSION["miSession"]["usuario"])){
            $idAlumno = $_SESSION["miSession"]["usuario"]->getId();
        }
        
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
                $unInput->setTamanio($r["tamanio"]);
                $unInput->setLinkAyuda($r["link_ayuda"]);
                
                if($this->isInputMagic($r["id_pagina"],$r["nombre"])){
                    $unInput->setIsMagic(true);
                }
                
                $respuesta = $this->getRespuestaTexto($r["id"],$idAlumno);
                if($unInput->getTipo() == "texto"){
                    $unInput->setRespuesta($respuesta);
                }
                
                if($unInput->getTipo() == "fecha"){
                    $respuesta = $this->getRespuestaFecha($r["id"],$idAlumno);
                    $unInput->setRespuesta($respuesta);
                }
                
                switch ($unInput->getTipo()) {
                    case "dropdown":
                        //$select = new \Usuarios\Model\Entity\Select();
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
                    case "imagen":
                        $imagen = $this->getImagen($unInput->getId());
                        $unInput->setControl($imagen);
                        break;
                    default:
                        break;
                }
                $inputs[] = $unInput;
            }
        }

        return $inputs;
    }
    
    public function getFecha($id) {
        $input = new \Usuarios\Model\Entity\Fecha();

        $sql = "SELECT * FROM input_fecha WHERE id_input=" . $id;

        $result = $this->adapter->query($sql)->execute();

        if ($result) {
            foreach ($result as $res) {
                $input->setId($res["id"]);
                $input->setTipoFecha($res["tipo_fecha"]);
                return $input;
            }
        }

        return $input;
    }
    
    public function getRespuestaImagen($idUsuario,$idInput){
        
        $sql = "SELECT ri.archivo from respuesta_imagen as ri "
                . "INNER JOIN respuestas as r on r.id = ri.id_respuesta "
                . "INNER JOIN inputs as i on i.id = r.id_input "
                . "WHERE ri.id_usuario = $idUsuario AND r.id_input = $idInput";
        
        $result = $this->adapter->query($sql)->execute();
        if ($result) {
            foreach ($result as $res) {
                return $res["archivo"];
            }
        }
        
        return "";
    }
    
    public function getImagen($id){
        $input = new \Usuarios\Model\Entity\Imagen();

        $sql = "SELECT * FROM input_imagen WHERE id_input=" . $id;

        $result = $this->adapter->query($sql)->execute();
        $idUsuario = $_SESSION["miSession"]["usuario"]->getId();
        
        if ($result) {
            foreach ($result as $res) {
                $input->setId($res["id"]);
                $input->setMaxSize($res["max_size"]);
                $input->setExtAllow($res["ext_allow"]);
                $input->setIdInput($res["id_input"]);
                $archivo = "";
                $archivo = $this->getRespuestaImagen($idUsuario,$res["id_input"]);
                $input->setArchivo($archivo);
                return $input;
            }
        }

        return $input;
    }
    
    public function getTexto($id) {
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

    public function getSelect($idInput) {

        $sql = " SELECT * FROM input_select
                WHERE id_input =  $idInput";

        $results = array();

        $res = $this->adapter->query($sql);

        if ($res) {
            $result = $res->execute();
            foreach ($result as $res) {
                $select = new \Usuarios\Model\Entity\Select();
                $select->setId($res["id"]);
                $select->setIdInput($res["id_input"]);
                $select->setRespuestasRequeridas($res["respuestas_requeridas"]);
                $select->setTipo($res["tipo"]);

                return $select;
            }
        }

        return $results;
    }

    public function getGruposPorPagina($idPagina, $id = null) {

        $sql = "select * from grupos WHERE estado = 1 AND id_pagina=" . $idPagina;

        if ($id) {
            $sql .= " AND id=" . $id;
        }

        $grupos = array();

        $res = $this->adapter->query($sql);
        if ($res) {
            $result = $res->execute();
            foreach ($result as $r) {
                $unGrupo = new Grupo();
                $unGrupo->setId($r["id"]);
                $unGrupo->setTitulo($r["titulo"]);
                $unGrupo->preguntas = $this->getPreguntasPorGrupo($r["id"]);
                $grupos[] = $unGrupo;
            }
        }

        return $grupos;
    }

    public function getRespuestaJson($palabra, $idUsuario, $tipo) {
        $separadores = array("?", ".", "!", ":", ";", ",");

        $res = "";

        $respuesta = array();

        if (strpos($palabra, "@") === 0) {
            $palabra = substr($palabra, 1);
            $separador_temp = "";
            foreach ($separadores as $separador) {
                $pos = strpos($palabra, $separador);
                if ($pos !== false) {
                    $separador_temp = $separador;
                    $palabra = substr($palabra, 0, $pos);
                }
            }

            $tipo = $this->getTipoInput($palabra);

            $respuesta[] = '<span class="resp-span" nom="' . $palabra . '" tipo="' . $tipo . '">' . $this->getRespuestaPregunta($palabra, $idUsuario, $tipo, $separador_temp) . '</span>' . $separador_temp;
        }

        return $respuesta;
    }

    public function getTipoInput($name) {
        $sql = "SELECT tipo_input FROM inputs "
                . "WHERE estado=1 AND nombre = '" . $name . "'";

        $res = $this->adapter->query($sql);

        if ($res) {
            $result = $res->execute();
            foreach ($result as $r) {
                return $r["tipo_input"];
            }
        }

        return false;
    }

    public function getRespuestaPregunta($nomPregunta, $idUsuario, $tipo, $separador) {

        if ($tipo == "texto") {
            $sql = "SELECT rt.texto as respuesta FROM respuesta_texto as rt "
                    . "INNER JOIN respuestas as r on r.id = rt.id_respuesta "
                    . "INNER JOIN inputs as i on i.id = r.id_input "
                    . "WHERE r.id_usuario = $idUsuario AND i.nombre = '" . $nomPregunta . "'";
        } elseif ($tipo == "dropdown") {
            $sql = "SELECT sc.value as respuesta FROM respuesta_select as rs "
                    . "INNER JOIN respuestas as r on r.id = rs.id_respuesta "
                    . "INNER JOIN inputs as i on i.id = r.id_input "
                    . "INNER JOIN select_collections as sc on sc.id_input = i.id AND sc.id_select = rs.id_select "
                    . "WHERE r.id_usuario = $idUsuario AND i.nombre = '" . $nomPregunta . "'";
        }elseif ($tipo == "fecha") {
            $sql = "SELECT CONCAT(rf.desde , ' ', rf.hasta)as respuesta  FROM respuesta_fecha as rf "
                    . "INNER JOIN respuestas as r on r.id = rf.id_respuesta "
                    . "INNER JOIN inputs as i on i.id = r.id_input "
                    . "WHERE r.id_usuario = $idUsuario AND i.nombre = '" . $nomPregunta . "'";
        }

        $res = $this->adapter->query($sql);

        if ($res) {
            $result = $res->execute();
            foreach ($result as $r) {
                $nomPregunta = $r["respuesta"];
            }
        }

        return $nomPregunta;
    }

    public function dummy() {
        
    }

}
