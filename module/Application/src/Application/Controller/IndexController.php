<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    private $config;
    
    public function indexAction()
    {
        $this->getConfig();
        $titulo = $this->config['parametros']['mvc']['application']['index']['titulo'];
        
        return $this->redirect()->toRoute('usuarios', array(
                        'controller' => 'index',
                        'action' => 'index'
        ));
    }
    
    public function getConfig(){
        if(!$this->config){
            $sm = $this->getServiceLocator();
            $this->config = $sm->get('ConfigIni');
        }
        return $this->config;
    }
}
