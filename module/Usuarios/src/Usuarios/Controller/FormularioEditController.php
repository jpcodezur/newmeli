<?php

namespace Usuarios\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Usuarios\Model\Entity\Formulario;
use Usuarios\Model\Dao\FormularioEditDao;

class FormularioEditController extends AbstractActionController {
    
    protected $dao;
    
    public function setDao($dao){
        $this->dao = new FormularioEditDao($dao);
    }
    
    public function indexAction(){
        $unForm = new Formulario();
        
        $paginas = $this->dao->getPaginas();
        
        $unForm->setPaginas($paginas);
        
        return new ViewModel(array("formulario" => $unForm));
    }
    
    public function popupAction(){
        $view = new ViewModel();
        $view->setTerminal(true);
        return $view;
    }
    
    public function popupeditinputAction(){
        $id = $this->params()->fromQuery('idinput'); 
        
        $input = $this->dao->getInput($id);
        
        $view = new ViewModel(array("input" => $input));
        $view->setTerminal(true);
        return $view;
    }
    
    public function popupPaginaAction(){
        $view = new ViewModel();
        $view->setTerminal(true);
        return $view;
    }
    public function popupEditAction(){
        
        $id = $this->params()->fromQuery('idpagina'); 
        
        $pagina = $this->dao->getPagina($id);
        
        $view = new ViewModel(array("pagina" => $pagina));
        $view->setTerminal(true);
        return $view;
    }
    
    public function saveordenpaginasAction(){
        $paginas = $this->params()->fromPost('paginas'); 
        $ret = $this->dao->saveOrdenPaginas($paginas);
        $view = new JsonModel(array($ret));
        $view->setTerminal(true);
        return $view;
    }
    
    public function saveordenitemsAction(){
        $inputs = $this->params()->fromPost('items'); 
        $ret = $this->dao->saveOrdenItems($inputs);
        $view = new JsonModel(array($ret));
        $view->setTerminal(true);
        return $view;
    }
    
    
    
}