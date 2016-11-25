<?php
namespace Usuarios\MisClases;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;

use Zend\Mvc\MvcEvent;
use Zend\EventManager\EventManagerInterface;

use Usuarios\Model\Dao\AlertaDao;

class ShowAlertas{
    
    public function attach(EventManagerInterface $events) {
        $this->listener[] = $events->attach(MvcEvent::EVENT_DISPATCH,array($this,
        'onDispatch'),100);
    }
    
    public function detach(EventManagerInterface $events) {
        foreach($this->listener as $index => $listener){
            if($events->detach($listener)){
                unset($this->listener[$index]);
            }
        }
    }
    
    public function onDispatch(MvcEvent $e) {
        $application = $e->getApplication();
        $services = $application->getServiceManager();
        $alertTableGateway = $this->getTableGateway($services,"Usuarios\Model\Entity\Alerta");
        
        $alertaDao = new AlertaDao($alertTableGateway);
        $services->setService('AlertasService', $alertaDao);
        $this->sendToLayout($e,$alertaDao);
    }
    
    public function getTableGateway($sm,$obj) {
        
        $dbAdapter = $this->getAdapter($sm);
        
        $resultSetPrototype = new ResultSet();

        $arr = array();
        
        $obj = new $obj();
        
        foreach ($obj as $key => $value) {
            $arr[$key] = $value;
        }
        
        $data = new \ArrayObject($arr);
        $resultSetPrototype->setArrayObjectPrototype($data);
        
        return new TableGateway('alertas', $dbAdapter, null, $resultSetPrototype);
    }
    
    public function getAdapter($sm){
        return $dbAdapter = $sm->get('db');
    }
    
    private function sendToLayout($e,$alertaDao){
        $viewModel = $e->getApplication()->getMvcEvent()->getViewModel();
        $viewModel->alertas = $alertaDao;
    }
    
    
    
}