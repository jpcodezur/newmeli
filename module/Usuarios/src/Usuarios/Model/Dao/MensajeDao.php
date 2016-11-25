<?php

namespace Usuarios\Model\Dao;

use Usuarios\Model\Connect;
use Usuarios\Model\Entity\Mensaje;

class MensajeDao extends BaseDao implements IMensajeDao {

    private $listaMensajes;
    protected $tableGateway;

    public function __construct($tableGateway="") {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($idUsuario,$enviados = false) {
        $mensajes = array();

        $sql = $this->tableGateway->getSql();

        $select = $this->tableGateway->getSql()->select();

        $select->join(
                array('u' => 'usuarios'), 'u.id = mensajes.id_emisor', array('UsuarioEvaluadorNombre' => 'nombre', 'UsuarioEvaluadorApellido' => 'apellido','UsuarioEvaluadorAvatar'=>'avatar')
        );
        
        if($enviados){
            $select->where(array('mensajes.id_emisor' => $idUsuario));
        }else{
            $select->where(array('mensajes.id_receptor' => $idUsuario));
        }
        
        $select->order('id ASC');

        $this->listaMensajes = $this->tableGateway->selectWith($select);

        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        foreach ($this->listaMensajes as $usr) {
            $unMensaje = new Mensaje();
            $unMensaje->setId($usr["id"]);
            $unMensaje->setEvaluador($usr["UsuarioEvaluadorNombre"]." ".$usr["UsuarioEvaluadorApellido"]);
            $unMensaje->setAsunto($usr["asunto"]);
            $unMensaje->setMensaje($usr["mensaje"]);
            $unMensaje->setEstado($usr["estado"]);
            $unMensaje->setFechaCreado($usr["fecha_creado"]);
            $mensajes[] = $unMensaje;
        }

        return array("mensajes" => $mensajes, "paginator" => $paginator);
    }
    
    public function setMensajeLeido($id,$idUsuario){

        $data = array(
            "estado" => "2",);

        if ($this->tableGateway->update($data, array("id" => $id,"id_receptor"=>$idUsuario))) {
            return array("error" => "0");
        }

        return array("error" => "1");
    }
    
    public function getMensajePorId($id,$idUsuario){
        $res = $this->setMensajeLeido($id,$idUsuario);
        
        $select = $this->tableGateway->getSql()->select();
        
        $select->join(
                array('u' => 'usuarios'), 'u.id = mensajes.id_emisor', array('UsuarioEvaluadorNombre' => 'nombre', 'UsuarioEvaluadorApellido' => 'apellido','UsuarioEvaluadorAvatar'=>'avatar')
        );
        
        $select->where(array('mensajes.id_receptor' => $idUsuario,'mensajes.id' => $id));
        
        $rowset = $this->tableGateway->selectWith($select);
        
        $mensaje = $rowset->current();
        
        $unMensaje = false;
        
        if($mensaje){
            $unMensaje = new Mensaje(
                    $mensaje["id"], 
                    $mensaje["id_emisor"], 
                    $mensaje["id_receptor"], 
                    $mensaje["mensaje"], 
                    $mensaje["asunto"], 
                    $mensaje["fecha_leido"],
                    $mensaje["fecha_creado"], 
                    $mensaje["estado"],
                    $mensaje["UsuarioEvaluadorNombre"]." ".$mensaje["UsuarioEvaluadorApellido"],
                    $mensaje["UsuarioEvaluadorAvatar"]
                    );
        }
        
        return $unMensaje;
    }
    
    public function getMensajesUsuario($id,$estado="1",$limit=null){
        
        $select = $this->tableGateway->getSql()->select();
        
        $select->join(
                array('u' => 'usuarios'), 'u.id = mensajes.id_emisor', array('UsuarioEvaluadorNombre' => 'nombre', 'UsuarioEvaluadorApellido' => 'apellido','UsuarioEvaluadorAvatar'=>'avatar')
        );
        
        $select->where(array('id_receptor' => $id,'mensajes.estado' => $estado));
        
        if($limit){
            $select->limit($limit);
        }
        
        $select->order('id ASC');
        
        $this->listaMensajes = $this->tableGateway->selectWith($select);
        
        $mensajes = array();
        
        foreach($this->listaMensajes as $mensaje){
            
            $unMensaje = new Mensaje(
                    $mensaje["id"],
                    $mensaje["id_emisor"],
                    $mensaje["id_receptor"],
                    $mensaje["mensaje"],
                    $mensaje["asunto"],
                    $mensaje["fecha_leido"],
                    $mensaje["fecha_creado"],
                    $mensaje["estado"],
                    ucwords($mensaje["UsuarioEvaluadorNombre"])." ".ucwords($mensaje["UsuarioEvaluadorApellido"]),
                    $mensaje["UsuarioEvaluadorAvatar"]);
            
            $mensajes[] = $unMensaje;
        }
        
        return $mensajes;
    }

    public function guardar($mensaje) {

        $data = array(
            "id" => $mensaje->getId(),
            "id_emisor" => $mensaje->getIdEmisor(),
            "id_receptor" => $mensaje->getIdReceptor(),
            "mensaje" => $mensaje->getMensaje(),
            "asunto" => $mensaje->getAsunto(),
            "fecha_leido" => $mensaje->getFechaLeido(),
            "fecha_creado" => $mensaje->getFechaCreado(),
            "estado" => $mensaje->getEstado(),
        );

        if ($this->tableGateway->insert($data)) {
            $mensaje->getId($this->tableGateway->lastInsertValue);
            return array("error" => "0", "mensaje" => $mensaje);
        }

        return array("error" => "1", "mensaje" => "Can't save Message.");
    }
    
    public static function update($mensaje) {}
    
    public static function delete($id){
        $sql = "DELETE FROM mensajes WHERE id = ".$id;
        $res = Connect::getInstance()->getAdapter()->query($sql);
        return $res->execute()->getAffectedRows();
    }
    
    public function quitarNotificacion($id){
        
        $data = array("estado" => "2");
        
        if ($this->tableGateway->update($data, array("id_receptor" => $id))) {
            return array("error" => "0");
        }
        
        return array("error" => "1");
    }
    
}
