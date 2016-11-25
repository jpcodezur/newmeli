<?php

namespace Usuarios\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Usuarios\Model\Entity\Mensaje;

class MensajeController extends AbstractActionController {
    
    private $mensajeDao;
    private $usuarioDao;
    
    public function setMensajeDao($dao){
        $this->mensajeDao = $dao;
    }
    
    public function setUsuarioDao($dao){
        $this->usuarioDao = $dao;
    }
    
    public function viewAction() {
        $mensaje = null; 
        $idAgente = null;
        $id = (int) $this->getEvent()->getRouteMatch()->getParam('id', null);
        
        $enviado = $this->request->getQuery('enviado');
        
        if($mensaje){
            return new ViewModel(array("mensaje"=>$mensaje));
        }
        
        if(isset($_SESSION["miSession"]["usuario"])){
            $idAgente = $_SESSION["miSession"]["usuario"]->getId();
        }
        
        if($id && $idAgente){
            $mensaje = $this->mensajeDao->getMensajePorId($id,$idAgente);
        }
        
        if($mensaje){
            return new ViewModel(array("mensaje"=>$mensaje));
        }
        
        return new ViewModel(array("error"=>true));
    }
    
    public function MsgsAction() {
        if(isset($_SESSION["miSession"]["usuario"])){
            $idAgente = $_SESSION["miSession"]["usuario"]->getId();
        
            $result = $this->mensajeDao->fetchAll($idAgente);
            $paginator = $result["paginator"];
            $pn = (int) $this->getEvent()->getRouteMatch()->getParam('id',1);
            $paginator->setCurrentPageNumber($pn);
            return new ViewModel(array("mensajes" => $result["mensajes"], "paginator" => $paginator));
        }
        return new ViewModel(array("mensajes" => $result["mensajes"], "paginator" => $paginator));
    }
    
    public function MsgsenviadosAction() {
        if(isset($_SESSION["miSession"]["usuario"])){
            $idAgente = $_SESSION["miSession"]["usuario"]->getId();
        
            $result = $this->mensajeDao->fetchAll($idAgente,true);
            $paginator = $result["paginator"];
            $pn = (int) $this->getEvent()->getRouteMatch()->getParam('id',1);
            $paginator->setCurrentPageNumber($pn);
            return new ViewModel(array("mensajes" => $result["mensajes"], "paginator" => $paginator));
        }
        return new ViewModel(array("mensajes" => $result["mensajes"], "paginator" => $paginator));
    }
    
    public function newAction(){
        return new ViewModel();
    }
    
    public function sendAction(){
        
        $idReceptores = $this->getRequest()->getPost('emisor-id', null);
        
        $res = array("error" => "");
        
        if(!$idReceptores){
            $idReceptores = trim($idReceptores);
            $idReceptores = $this->getRequest()->getPost('para', null);
            $idReceptores = explode(",",$idReceptores);
        }
        
        for($i=0;$i<count($idReceptores);$i++){
            if(strlen($idReceptores[$i])<1){
                unset($idReceptores[$i]);
            }
        }
        
        if(isset($_SESSION["miSession"]["usuario"])){
            $idEmisor = $_SESSION["miSession"]["usuario"]->getId();
            $idEmisor = $idEmisor;
        }
        
        if(isset($idEmisor,$idReceptores) && $idEmisor){
            
            $body = $this->getRequest()->getPost('textarea', null);
            $asunto = $this->getRequest()->getPost('asunto', null);
            
            $unMensaje = new Mensaje();

            $unMensaje->setIdEmisor($idEmisor);
            $unMensaje->setMensaje($body);
            $unMensaje->setAsunto($asunto);
            $unMensaje->setFechaCreado(date("Y-m-d"));
            $unMensaje->setEstado("1");
            
            foreach($idReceptores as $receptor){
                $receptor = $this->usuarioDao->getUserByEmail($receptor);
                $idReceptor = $receptor->getId();
                $unMensaje->setIdReceptor($idReceptor);
                $res = $this->mensajeDao->guardar($unMensaje);
            }
            
            
            
            if($res["error"] == "0"){
                return new ViewModel(array("enviado" => true));
            }
        }
        
        return new ViewModel(array("enviado" => false));
    }
    
    public function usernameAction(){
        $nombre = $this->request->getQuery('text');
        $usuario = $this->usuarioDao->getUsuarioLikeNombre($nombre);
        $view = new ViewModel(array("usuario"=>$usuario));
        $view->setTerminal(true);    
        return $view;  
    }
    
    public function setvistosAction(){
        
        $id = $this->request->getQuery('id');

        if (!$id) {
            $id = $this->getRequest()->getPost('id', null);
        }
        
        $visto = $this->mensajeDao->quitarNotificacion($id);
        $view = new ViewModel(array("visto"=>$visto["error"]));
        $view->setTerminal(true);    
        return $view;
    }
    
    public function deleteAction() {
        if ($this->request->isGet()) {

            $id = (int) $this->getEvent()->getRouteMatch()->getParam('id', null);

            if ($id) {
                $result = $this->mensajeDao->delete($id);
            }

            if ($result) {
                return $this->redirect()->toRoute('usuarios', array(
                            'controller' => 'mensaje',
                            'action' => 'msgs'
                ));
            }
        }
    }
    
}