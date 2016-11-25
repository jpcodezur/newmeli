<?php

namespace Usuarios\Model\Dao;

use Usuarios\Model\Entity\Categoria;
use ArrayObject;

class CategoriaDao implements ICategoriaDao {

    private $listaCategoria;
    protected $tableGateway;
    private $adapter;

    public function __construct($tableGateway = null, $adapter = null) {
        $this->tableGateway = $tableGateway;
        $this->adapter = $adapter;
    }
    
    public function obtenerTodos() {

        $categorias = array();

        $sql = $this->tableGateway->getSql();

        $select = $this->tableGateway->getSql()->select();

        echo $sql->getSqlstringForSqlObject($select); die ;
        
        $select->where(array("estado"=>"1"));

        $select->order('id ASC');

        $this->listaCategoria = $this->tableGateway->selectWith($select);

        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        foreach ($this->listaCategoria as $cat) {
            $unaCategoria = new Categoria();
            $unaCategoria->setId($cat["id"]);
            $unaCategoria->setEmail($cat["nombre"]);
            $categorias[] = $unaCategoria;
        }

        return array("categorias" => $categorias, "paginator" => $paginator);
    }

}
