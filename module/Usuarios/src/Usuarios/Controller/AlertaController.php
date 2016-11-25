<?php

namespace Usuarios\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class AlertaController extends AbstractActionController {
    
    private $alertaDao;
    
    public function setAlertaDao($dao){
        $this->alertaDao = $dao;
    }
    
    public function viewAction() {
        $alerta = null; 
        $idAgente = null;
        $id = (int) $this->getEvent()->getRouteMatch()->getParam('id', null);

        if(isset($_SESSION["miSession"]["usuario"])){
            $idAgente = $_SESSION["miSession"]["usuario"]->getId();
        }
        
        
        if($id && $idAgente){
            $alerta = $this->alertaDao->getAlertaPorId($id,$idAgente);
        }
        
        return new ViewModel(array("alerta"=>$alerta));
    }
    
    public function AlertsAction() {
        return new ViewModel();
    }
    
    public function IndexAction() {
        return new ViewModel();
    }
    
    public function setvistosAction(){
        $id = $this->request->getQuery('id');

        if (!$id) {
            $id = $this->getRequest()->getPost('id', null);
        }
        
        $visto = $this->alertaDao->quitarNotificacion($id);
        $view = new ViewModel(array("visto"=>$visto["error"]));
        $view->setTerminal(true);    
        return $view;
    }
    
}