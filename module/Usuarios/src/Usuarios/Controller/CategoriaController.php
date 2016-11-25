<?php

namespace Usuarios\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Usuarios\Model\Entity\Categoria;
use Usuarios\Model\Dao\CategoriaDao;

class CategoriaController extends AbstractActionController {

    private $config;
    private $tableGateway;
    private $dao;
    private $adapter;

    public function setDao($dao){
        $this->dao = new CategoriaDao($dao);
    }

    public function indexAction(){
        $result = $this->dao->fetchAll();
        $articulos = array("total" => "100");
        return new ViewModel(array("articulos" => $articulos));
    }
}
