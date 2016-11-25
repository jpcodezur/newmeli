<?php

namespace Usuarios\Model\Dao;

use Usuarios\Model\Entity\Usuario;
use ArrayObject;
use Usuarios\MisClases\Respuesta;

class UsuarioDao implements IUsuarioDao {

    private $listaUsuario;
    protected $tableGateway;
    private $adapter;

    public function __construct($tableGateway = null, $adapter = null) {
        $this->tableGateway = $tableGateway;
        $this->adapter = $adapter;
    }

    public function obtenerTodos() {

        $usuarios = array();

        $sql = $this->tableGateway->getSql();

        $select = $this->tableGateway->getSql()->select();

        $select->join(
                array('tu' => 'tipos_usuario'), 'tu.id = usuarios.tipo', array('tipoUsuario' => 'usuario')
        );
        
        $select->where(array("estado"=>"1"));

        $select->order('id ASC');

        //$salida = $select->getSqlString();

        $this->listaUsuario = $this->tableGateway->selectWith($select);

        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        foreach ($this->listaUsuario as $usr) {
            $unUsuario = new Usuario();
            $unUsuario->setNombre($usr["nombre"]);
            $unUsuario->setEmail($usr["email"]);
            $unUsuario->setApellido($usr["apellido"]);
            $unUsuario->setTipo($usr["tipoUsuario"]);
            $unUsuario->setId($usr["id"]);
            $unUsuario->setAvatar($usr["avatar"]);
            $usuarios[] = $unUsuario;
        }

        return array("usuarios" => $usuarios, "paginator" => $paginator);
    }
    
    public function obtenerTodosReportes($evaluacionDao,$usuarioActual=null,$fechaDesde=null,$fechaHasta=null,$agentes=null) {

        $usuarios = array();

        $sql = $this->tableGateway->getSql();

        $select = $this->tableGateway->getSql()->select();

        $select->join(array('tu' => 'tipos_usuario'), 'tu.id = usuarios.tipo', array('tipoUsuario' => 'usuario'));
        
        $select->join(array('e' => 'evaluaciones'), 'e.id_agente = usuarios.id', array('rArchivo' => 'archivo'));
        
        $selectMiId = $this->tableGateway->getSql()->select();
        $selectMiId->columns(array('id'));
        $selectMiId->where(array("usuarios.id"=>$usuarioActual->getId()));

        if($usuarioActual){
            $select->where->notIn("usuarios.id",$selectMiId);
        }
        
        if($agentes){
            $select->where->in("usuarios.id",array($agentes));
        }
        
        if($fechaDesde){
            $select->where->between("e.fecha_evaluacion",$fechaDesde,$fechaHasta);
        }
        
        $select->group('usuarios.id');
        
        $select->order('usuarios.id ASC');
        
        //$salida = $select->getSqlString();

        $this->listaUsuario = $this->tableGateway->selectWith($select);

        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        foreach ($this->listaUsuario as $usr) {
            $unUsuario = new Usuario();
            $unUsuario->setId($usr["id"]);
            
            $idUsuario = $unUsuario->getId();
            
            $evaluaciones = $evaluacionDao->getEvaluacionesAgente($idUsuario);
            $unUsuario->setEvaluaciones($evaluaciones);
            $unUsuario->setPorcentaje($evaluacionDao->AvgAgente($unUsuario));
            $unUsuario->setNombre($usr["nombre"]);
            $unUsuario->setEmail($usr["email"]);
            $unUsuario->setApellido($usr["apellido"]);
            $unUsuario->setTipo($usr["tipoUsuario"]);
            $unUsuario->setId($usr["id"]);
            $unUsuario->setAvatar($usr["avatar"]);
            $usuarios[] = $unUsuario;
        }

        return array("usuarios" => $usuarios, "paginator" => $paginator);
    }
    
    public function getUsuarioLikeNombre($nombre){
        $usuarios = array();
        
        $sql = $this->tableGateway->getSql();

        $select = $this->tableGateway->getSql()->select();
        
        $where = new \Zend\Db\Sql\Where();

        $where->literal('nombre LIKE ? OR apellido LIKE ? OR email LIKE ?', array('%' . $nombre . '%','%' . $nombre . '%','%' . $nombre . '%'));

        $select->where($where);
        
        $select->order('id ASC');
        
        $usuarios = $this->tableGateway->selectWith($select);
        
        $usuarios_result = array();
        
        foreach($usuarios as $usuario){
            $usuarios_result[] = $usuario;
        }
        
        return json_encode($usuarios_result);
    }

    public function getUsuario($id) {

        $select = $this->tableGateway->getSql()->select();

        $select->where(array("id" => $id));

        $usuarios =  $this->tableGateway->selectWith($select);
        
        foreach($usuarios as $usuario){

            $unUsuario = new Usuario();
            $unUsuario->setNombre($usuario["nombre"]);
            $unUsuario->setApellido($usuario["apellido"]);
            $unUsuario->setEmail($usuario["email"]);
            $unUsuario->setTipo($usuario["tipo"]);
            $unUsuario->setId($usuario["id"]);
            if (isset($usuario["avatar"]))
                $unUsuario->setAvatar($usuario["avatar"]);

            return $unUsuario;
        }
        
        return false;
    }

    public function obtenerPorId($id) {

        $usuario = false;

        if ($id) {
            $id = (int) $id;

            $usuario = $rowset->current();

            $row = $rowset->current();
            if (!$row) {
                
            } else {
                $unUsuario = new Usuario();
                $unUsuario->setNombre($usuario["nombre"]);
                $unUsuario->setApellido($usuario["apellido"]);
                $unUsuario->setEmail($usuario["email"]);
                $unUsuario->setTipo($usuario["tipo"]);
                $unUsuario->setId($usuario["id"]);
                if (isset($usuario["avatar"]))
                    $unUsuario->setAvatar($usuario["avatar"]);
                $usuario = $unUsuario;
            }
        }
        return $usuario;
    }

    public function getUserByEmail($email) {

        $select = $this->tableGateway->getSql()->select();

        $select->where(array("email" => $email));

        $usuarios =  $this->tableGateway->selectWith($select);
        
        $usu = null;
        
        foreach($usuarios as $usu){
            $usu = $usu;
        }

        return $usu["id"];
    }
    
    public function buscarPorNombre($nombre) {
        $usuariosEncontrados = new ArrayObject();
        foreach ($this->listaUsuario as $usuario) {
            if ($usuario->getNombre() == $nombre) {
                $usuariosEncontrados->append($usuario);
            }
        }
        return $usuariosEncontrados;
    }

    public function buscarPorEmail($email) {
        $usuariosEncontrados = new ArrayObject();
        foreach ($this->listaUsuario as $usuario) {
            if ($usuario->getEmail() == $email) {
                $usuariosEncontrados->append($usuario);
            }
        }
        return $usuariosEncontrados;
    }

    public function obtenerCuenta($email, $clave) {
        $clave = md5($clave);

        $select = $this->tableGateway->getSql()->select();

        $select->where(array("email" => $email, "md5" => $clave));

        $rowset = $this->tableGateway->selectWith($select);

        $usuario = $rowset->current();

        if ($usuario) {

            $unUsuario = new Usuario();
            $unUsuario->setNombre($usuario["nombre"]);
            $unUsuario->setApellido($usuario["apellido"]);
            $unUsuario->setEmail($usuario["email"]);
            $unUsuario->setTipo($usuario["tipo"]);
            $unUsuario->setId($usuario["id"]);
            if (isset($usuario["avatar"])){
                $unUsuario->setAvatar($usuario["avatar"]);
            }
                
            $usuario = $unUsuario;
        }
        return $usuario;
    }

    public function getTipos() {
        $tipos = array("" => "SELECCIONAR");

        $sql = "SELECT * FROM tipos_usuario";

        $statement = $this->adapter->query($sql);
        $salida = $statement->execute();
        foreach ($salida as $s) {
            $tipos[$s["id"]] = $s["usuario"];
        }
        return $tipos;
    }

    public function guardar($usuario) {

        $data = array(
            "id" => $usuario->getId(),
            "email" => $usuario->getEmail(),
            "nombre" => $usuario->getNombre(),
            "apellido" => $usuario->getApellido(),
            "md5" => md5($usuario->getClave()),
            "tipo" => $usuario->getTipo(),
            "fechaReg" => date("Y-m-d"),
            "estado" => "1",
        );

        $avatar = $usuario->getAvatar();
        
        if ($avatar) {
            $data["avatar"] = $avatar;
        }

        $result = $this->tableGateway->insert($data);

        return $result;
    }

    public function update($user) {
        $result = false;

        $data = array(
            "nombre" => $user->getNombre(),
            "apellido" => $user->getApellido(),
            "email" => $user->getEmail(),
            "tipo" => $user->getTipo(),
        );

        if ($user->getAvatar()) {
            $data["avatar"] = $user->getAvatar();
        }
        
        $result = $this->tableGateway->update($data, array("id" => $user->getId()));
        
        if($result){
            return array("error" => "0", "usuario" => $user);
        }

        return array("error" => "1", "usuario" => $user);
    }

    public function delete($id) {
        $respuesta = new Respuesta();
        
        $result = $this->tableGateway->update(array("estado"=>"0"),array("id" => $id));
        
        if(!$result){
            $respuesta->setError(true);
            $respuesta->setMensaje("Error deleting user");
        }
        
        return $respuesta;
    }

}
