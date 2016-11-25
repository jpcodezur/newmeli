<?php

namespace Usuarios\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Usuarios\Controller\Params\PaginaParams;

class FormularioController extends AbstractActionController {

    private $preguntaDao;
    private $paginaDao;
    private $paginaParams;
    private $dao;

    public function __construct() {
        $paginaParams = new PaginaParams();
        $this->paginaParams = $paginaParams->getParams();
    }

    public function setPreguntaDao($dao) {
        $this->preguntaDao = $dao;
    }
    
    public function setDao($dao) {
        $this->dao = new \Usuarios\Model\Dao\FormularioDao($dao);
    }
    
    public function setPaginaDao($dao) {
        $this->paginaDao = $dao;
        $this->paginaDao->setParams($this->paginaParams);
    }

    public function IndexAction() {
        die("500");
    }
    
    public function popupAction(){
        $view = new ViewModel();
        $view->setTerminal(true);
        return $view;
    }

    public function wizardAction(){
        
        $paginas = array();
        
        $active = 1;
        
        if(isset($_SESSION["miSession"]["usuario"])){
            $usuario = $_SESSION["miSession"]["usuario"]; 
        
            $pagina = $this->dao->getLastPage($usuario->getId());
            
            if(!$pagina){
                $pagina = 1;
            }
            
            $paginas = $this->dao->getPaginas(false);
            
            $inputs = $this->dao->getFormulario($pagina);
            
            $active = $this->dao->getUltimaPaginaCompletada($usuario->getId());
            
        }
        
        return array("paginas" => $paginas,"inputs" => $inputs,"active" => $active);
    }
    
    public function resparentAction(){
        
        $word = $this->getRequest()->getPost("word", null);
        
        $tipo = "texto";
        
        $usuario = $_SESSION["miSession"]["usuario"]; 
        
        $response =  $this->dao->getRespuestaJson($word,$usuario->getId(),$tipo);
        
        $view = new JsonModel($response);

        $view->setTerminal(true);

        return $view;
    }
    
    public function nextAction(){
        
        $idPagina = $this->request->getQuery('id_pagina');
        
        $formulario = $this->dao->getFormulario($idPagina);
        
        $view = new JsonModel(array($formulario));

        $view->setTerminal(true);

        return $view;
    }
    
    public function prevAction(){
        
        $idPagina = $this->request->getQuery('id_pagina');
        
        $formulario = $this->dao->getFormulario($idPagina);
        
        $view = new JsonModel(array($formulario));

        $view->setTerminal(true);

        return $view;
    }
    
    
    
}