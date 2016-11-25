<?php

namespace Usuarios\Model\Dao;

use Usuarios\Model\Connect;
use \Usuarios\Model\Entity\Alerta;

class AlertaDao extends BaseDao {

    private $listaCampanias;
    protected $tableGateway;

    public function __construct($tableGateway="") {
        $this->tableGateway = $tableGateway;
    }

    public function fetchAll() {
        //getAlertas
        $campanias = array();

        $sql = $this->tableGateway->getSql();

        $select = $this->tableGateway->getSql()->select();
        
        $select->join(
                array('od' => 'origenes_datos'), 'od.id = campanias.id_servidor', array('Servidor' => 'nombre'),'left'
        );
        
        $this->listaCampanias = $this->tableGateway->selectWith($select);

        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        foreach ($this->listaCampanias as $camp) {
            $uaCampania = new Campania();
            $uaCampania->setId($camp["id"]);
            $uaCampania->setNombre($camp["nombre"]);
            $uaCampania->setUbicacion($camp["ubicacion"]);
            $uaCampania->setAprobacion($camp["aprobacion"]);
            $uaCampania->setReprobacion($camp["reprobacion"]);
            $uaCampania->setUpdater($camp["updater"]);
            $uaCampania->setUpdated($camp["updated"]);
            $uaCampania->setFechaReg($camp["fechaReg"]);
            $uaCampania->setServidor($camp["Servidor"]);
            $campanias[] = $uaCampania;
        }

        return array("campanias" => $campanias, "paginator" => $paginator);
    }
    
    public function setAlertaLeida($idAlerta,$idUsuario){
        /*
        $data = array(
            "estado" => "2",);

        if ($this->tableGateway->update($data, array("id" => $idAlerta,"id_agente"=>$idUsuario))) {
            return array("error" => "0");
        }

        return array("error" => "1");
        */
    }
    
    public function getAlertaPorId($idAlerta,$idUsuario){
        /*$res = $this->setAlertaLeida($idAlerta,$idUsuario);
        
        $rowset = $this->tableGateway->select(array('id_agente' => $idUsuario,'id' => $idAlerta));
        $alerta = $rowset->current();
        $unAlerta = false;
        
        if($alerta){
            $unAlerta = new Alerta($alerta["id"], $alerta["id_agente"], $alerta["estado"], $alerta["mensaje"], $alerta["url_audio"], $alerta["audio"], $alerta["asunto"], $alerta["fecha_creada"], $alerta["fecha_visto"]);
        }
        
        return $unAlerta;*/
    }
    
    public function getAlertasUsuario($id,$estado="1",$limit=null){
        /*
        $select = $this->tableGateway->getSql()->select();
        
        $select->where(array('id_agente' => $id,'estado' => $estado));
        
        if($limit)
            $select->limit($limit);
        
        $select->order('id ASC');
        
        $this->listaAlertas = $this->tableGateway->selectWith($select);
        
        $alertas = array();
        
        foreach($this->listaAlertas as $alerta){
            
            $unAlerta = new Alerta(
                    $alerta["id"],
                    $alerta["id_agente"],
                    $alerta["estado"],
                    $alerta["url_audio"],
                    $alerta["audio"],
                    $alerta["asunto"],
                    $alerta["fecha_creada"],
                    $alerta["fecha_visto"]);
            
            $alertas[] = $unAlerta;
        }
        
        return $alertas;
        */
        return array();
    }

    public function guardar($agente,$body,$estado,$urlaudio,$audio,$asunto) {
        /*
        $unAlerta = new Alerta(null,$agente, $estado, $body, $urlaudio="", $audio, $asunto, "", "");
        
        $data = array(
            "id_agente" => $agente,
            "estado" => $estado,
            "url_audio" => $urlaudio,
            "audio" => $audio,
            "asunto" => $asunto,
            "fecha_creada" => date("Y-m-d hh:mm:ss"),
        );

        if ($this->tableGateway->insert($data)) {
            $unAlerta->getId($this->tableGateway->lastInsertValue);
            return array("error" => "0", "alerta" => $unAlerta);
        }*/

        return array("error" => "1", "mensaje" => "Can't save Alert.");
    }
    
    public static function update($campania) {
        
    }
    
    public static function delete($id){
        $res = self::deleteTopicosCampania($id);
        $sql = "DELETE FROM campanias WHERE id = ".$id;
        $res = Connect::getInstance()->getAdapter()->query($sql);
        return $res->execute()->getAffectedRows();
    }
    
    public function quitarNotificacion($id){
        /*
        $data = array("estado" => "2");
        
        if ($this->tableGateway->update($data, array("id_agente" => $id))) {
            return array("error" => "0");
        }*/
        
        return array("error" => "0");
    }

}
