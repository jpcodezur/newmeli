<?php

namespace Usuarios\Controller\Inputs;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Usuarios\Model\Dao\InputDao;
use Usuarios\Model\Entity\Input;

class InputController extends AbstractActionController {

    private $dao;

    public function __construct() {
        
    }

    public function setTableGateway($tableGateway) {
        $this->tableGateway = $tableGateway;
    }

    public function setDao($dao){
        $this->dao = new InputDao($dao);
    }

    public function IndexAction() {
        die("500");
    }

    public function ListAction() {
        $response = new \Usuarios\MisClases\Respuesta();
        $resp_temp = $this->params()->fromRoute('response');

        if ($resp_temp == true) {
            $response = $resp_temp;
        }

        $result = $this->dao->fetchAll();
        $paginator = $result["paginator"];
        $pn = (int) $this->getEvent()->getRouteMatch()->getParam('id', 1);
        $paginator->setCurrentPageNumber($pn);
        return new ViewModel(array("response" => $response, "params" => $this->params, "entities" => $result["entities"], "paginator" => $paginator));
    }

    public function addAction() {

        $response = new \Usuarios\MisClases\Respuesta();
        
        if ($this->request->isPost()) {

            $unaEntity = new Input();
            
            $unaEntity->setNombre($this->getRequest()->getPost("nombre", null));
            $unaEntity->setLabel($this->getRequest()->getPost("label", null));
            $unaEntity->setRequired($this->getRequest()->getPost("obligatorio", null));
            $unaEntity->setTipo($this->getRequest()->getPost("tipo", null));
            $unaEntity->setTamanio($this->getRequest()->getPost("tamanio", null));
            $unaEntity->setEstado(1);
            $unaEntity->setOrden($this->getRequest()->getPost("orden", null));
            $unaEntity->setIdPagina($this->getRequest()->getPost("id_pagina", null));
            $unaEntity->setAyuda($this->getRequest()->getPost("ayuda", null));
            $unaEntity->setLinkAyuda($this->getRequest()->getPost("link_ayuda", null));
            
            $input_data = $this->getRequest()->getPost("input_data", null);
            
            $unInput = null;
            
            switch ($unaEntity->getTipo()) {
                    case "texto":
                        $unInput = new \Usuarios\Model\Entity\Texto();
                        $unInput->setRespuestasRequeridas($input_data["respuestas_requeridas"]);
                        $unInput->setEjemplo($input_data["ejemplo"]);
                        $unInput->setValidacion($input_data["validacion"]);
                        break;
                    case "dropdown":
                        $unInput = new \Usuarios\Model\Entity\Select();
                        $select = $input_data["select"];
                        $unInput->setRespuestasRequeridas($select["respuestas_requeridas"]);
                        $unInput->setTipo($select["tipo"]);
                        $unInput->setValues($select["values"]);
                        break;
                    case "fecha":
                        $unInput = new \Usuarios\Model\Entity\Fecha();
                        $unInput->setTipoFecha($input_data["tipo_fecha"]);
                        break;
                    case "imagen":
                        $unInput = new \Usuarios\Model\Entity\Imagen();
                        break;
                    default:
                        break;
            }
            
            $unaEntity->setControl($unInput);
            
            $response = $this->dao->save($unaEntity);
        }
        
        $view = new JsonModel(array( "response" => $response));

        $view->setTerminal(true);

        return $view;
    }
    
    public function deleteselectAction(){
        
        $idselect = $this->getRequest()->getPost("idselect", null);
        $idinput = $this->getRequest()->getPost("idinput", null);
        
        $response = $this->dao->deleteSelect($idselect,$idinput);
        
        $view = new JsonModel(array( "response" => $response));

        $view->setTerminal(true);

        return $view;
    }
    
    public function deleteitemtempselectAction(){
        
        $idUsuario = $_SESSION["miSession"]["usuario"]->getId();
        
        $this->dao->deleteItemTempSelectValue($idUsuario);
        
        $view = new JsonModel(array( "response" => $response));

        $view->setTerminal(true);

        return $view;
    }
    
    public function saveordenvalueAction(){
        $inputs = $this->params()->fromPost('items'); 
        $ret = $this->dao->saveOrdenValues($inputs);
        $view = new JsonModel(array($ret));
        $view->setTerminal(true);
        return $view;
    }
    
    /*public function addtempselectAction(){
        
        $idUsuario = $_SESSION["miSession"]["usuario"]->getId();
        
        $value = $this->getRequest()->getPost("valueselect", null);
        $tipo = $this->getRequest()->getPost("tipo", null);
        $nrespuestas = $this->getRequest()->getPost("nrespuestas", null);
        $orden = $this->getRequest()->getPost("orden", null);
        
        $response = $this->dao->insertTempSelectValue($idUsuario,$value,$tipo,$nrespuestas,$orden);
        
        $view = new JsonModel(array( "response" => $response));

        $view->setTerminal(true);

        return $view;
    }*/

    public function editAction() {

        $response = new \Usuarios\MisClases\Respuesta();
        
        if ($this->request->isPost()) {

            $unInput = new Input();
            
            $unInput->setId($this->getRequest()->getPost("idinput", null));
            $unInput->setNombre($this->getRequest()->getPost("nombre", null));
            $unInput->setLabel($this->getRequest()->getPost("label", null));
            $unInput->setTamanio($this->getRequest()->getPost("tamanio", null));
            $unInput->setTipo($this->getRequest()->getPost("tipo", null));
            $unInput->setAyuda($this->getRequest()->getPost("ayuda", null));
            $unInput->setRequired($this->getRequest()->getPost("obligatorio", null));
            $unInput->setLinkAyuda($this->getRequest()->getPost("link_ayuda", null));
            
            $input_data = $this->getRequest()->getPost("input_data", null);
            
            switch ($unInput->getTipo()) {
                    case "texto":
                        $unControl = new \Usuarios\Model\Entity\Texto();
                        $unControl->setRespuestasRequeridas($input_data["respuestas_requeridas"]);
                        $unControl->setEjemplo($input_data["ejemplo"]);
                        $unControl->setValidacion($input_data["validacion"]);
                        break;
                    case "dropdown":
                        $unControl = new \Usuarios\Model\Entity\Select();
                        $select = $input_data["select"];
                        $unControl->setRespuestasRequeridas($select["respuestas_requeridas"]);
                        $unControl->setTipo($select["tipo"]);
                        $unControl->setValues($select["values"]);
                        break;
                    case "fecha":
                        $unControl = new \Usuarios\Model\Entity\Fecha();
                        $unControl->setTipoFecha($input_data["tipo_fecha"]);
                        break;
                    case "imagen":
                        $unControl = new \Usuarios\Model\Entity\Imagen();
                        break;
                    default:
                    default:
                        break;
            }
            
            $unInput->setControl($unControl);
            
            $response = $this->dao->update($unInput);
        }
        
        $view = new JsonModel(array($response));

        $view->setTerminal(true);

        return $view;
    }

    public function deleteAction() {

        $response = new \Usuarios\MisClases\Respuesta();

        if ($this->request->isGet()) {

            $id = (int) $this->getEvent()->getRouteMatch()->getParam('id', null);

            if (!$id) {
                $id = $this->request->getQuery('id');
            }

            if ($id) {
                $response = $this->dao->delete($id);
            }

            $view = new JsonModel(array($response));

            $view->setTerminal(true);

            return $view;
        }
    }

    public function getInputsJsonAction() {
        
        $query = $this->request->getQuery('query');
        
        $result = $this->dao->fetchOneLike($query);

        $view = new JsonModel(($result));

        $view->setTerminal(true);

        return $view;
    }
    public function dummi(){}
}
