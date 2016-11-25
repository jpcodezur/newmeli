<?php

namespace Usuarios\Model\Dao;
ini_set('max_execution_time', 3000); //300 seconds = 5 minutes

use Usuarios\Model\Entity\Articulo;
use Usuarios\MisClases\Meli;

class ArticuloDao extends BaseDao implements IArticuloDao {

    private $listaCategoria;
    protected $tableGateway;
    private $adapter;
    Const tableNameAlerts = "alertas";
    Const tableNameAlertsHistory = "alertas_history";
    Const columnItemEstatus = "columnItemEstatus";
    Const tableNameArticlulos = "articulos";

    public function __construct($tableGateway = null, $adapter = null,$config = null) {
        $this->tableGateway = $tableGateway;
        $this->adapter = $adapter;
        $this->config = $config;
    }



    public function getCategorias() {
        $articulos = array();

        $sql = $this->tableGateway->getSql();

        $select = $this->tableGateway->getSql()->select();

        $select->columns(array('categories'));
        $select->quantifier(\Zend\Db\Sql\Select::QUANTIFIER_DISTINCT);

        $select->order('categories ASC');

        $this->listaCategoria = $this->tableGateway->selectWith($select);

        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        foreach ($this->listaCategoria as $cat) {
            /////////////////////////////
            $mapeo = $this->getCategoriaMapeo(null,$cat["categories"]);

            $catMl = explode (",",$mapeo["categorias_ml"]);

            $mapeo = $this->getCategoriaMapeo(null,$cat["categories"]);

            $catMl = explode (",",$mapeo["categorias_ml"]);
            $template = $mapeo["template"];

            $unArticulo = new Articulo();
            $categoriasMl = array();
            foreach($catMl as $c){
                $categoriaMl = new  \stdClass();
                $categoriaMl->id = $c;
                $res = $this->getCateogriaMlDb($c);
                $categoriaMl->text = $res["nombre_categoria_ml"];
                $categoriasMl[] = $categoriaMl;
            }


            $unArticulo->setCategorias($cat["categories"]);
            $unArticulo->template = $template;
            $unArticulo->setMlCategorias($categoriasMl);

            $articulos[] = $unArticulo;
        }

        return array("categorias" => $articulos, "paginator" => $paginator);
    }

    public function getEstados(){
        $estados = array();
        $this->adapter = $this->tableGateway->getAdapter();

        $sql = "SELECT DISTINCT * FROM estados_publicacion";

        $res = $this->adapter->query($sql);

        if($res){
            $result = $res->execute();
            foreach($result as $r){
                $estados[] = $r;
            }
        }

        return $estados;
    }

    public function getTotalArticulos($filtros=null){
        $ret = array();
        $this->adapter = $this->tableGateway->getAdapter();

        $where = "WHERE visibility = '4' ";

        if($filtros["categorias"] && $filtros["categorias"] != "todas"){
            $where .= "AND categories='".$filtros["categorias"]."' ";
        }

        $groupBy = "";
        $estado = "";

        if($filtros["estado"]){
            $where .= "AND a.estado='".$filtros["estado"]."' ";
        }
            $groupBy = " group by a.estado, a.categories ";
            $estado = ", a.estado , a.categories ";


        $sql = "SELECT COUNT(*) as total $estado FROM articulos as a 
                INNER JOIN mapeo_categorias as mc 
                  on mc.categoria_geopos = a.categories 
                $where $groupBy";

        $res = $this->adapter->query($sql);

        if($res){
            $result = $res->execute();
            $tmp = array();
            foreach($result as $r){
                if(isset($r["categories"])){
                    $tmp[$r["categories"]]["estados"][$r["estado"]] = $r["total"];

                    if(!isset($tmp[$r["categories"]]["estados"]["active"])){
                        $tmp[$r["categories"]]["estados"]["active"] = 0;
                    }

                    if(!isset($tmp[$r["categories"]]["estados"]["paused"])) {
                        $tmp[$r["categories"]]["estados"]["paused"] = 0;
                    }

                    if(!isset($tmp[$r["categories"]]["estados"]["closed"])) {
                        $tmp[$r["categories"]]["estados"]["closed"] = 0;
                    }

                    if(!isset($tmp[$r["categories"]]["estados"]["not_published"])) {
                        $tmp[$r["categories"]]["estados"]["not_published"] = 0;
                    }
                }else{

                }
            }

            $ret[] = $tmp;
        }

        return array("ret" => $ret);
    }

    public function checkCatIsMapped($cat){
        $this->adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM mapeo_categorias WHERE UPPER(categoria_geopos)='".strtoupper(($cat))."'";

        $res = $this->adapter->query($sql);

        if($res){
            $result = $res->execute();
            foreach($result as $r){
                if($r["categoria_ml"]){
                    return $r;
                }
            }
        }

        return false;
    }

    public function republicarArticulo($pArticulo,$mapeo,$id_evento=0){
        $pArticulo = $pArticulo["articulo"];
        $articulo = $this->mapeoArticulo($pArticulo,$mapeo);
        $articulo["id"] = $pArticulo["id"];
        $articulo["name"] = $pArticulo["name"];

        $articulo["short_description"] = $pArticulo["short_description"];


        $params = array('access_token' => $_SESSION['access_token']);
        $meli = new Meli($this->config["appId"], $this->config["key"], $_SESSION['access_token'], $_SESSION['refresh_token']);

        //$template = $this->setTemplate($articulo);
        //$price = (int) $this->fixSpecialPrice($pArticulo);

        $articulo = array(
            "title" => $pArticulo["name"],
            "price"=> (int)$pArticulo["price"],
            "quantity" => (int) $pArticulo["qty"],
            "listing_type_id" => $this->config["listing_type"],

        );

        if(isset($pArticulo["short_description"]) && $pArticulo["short_description"]){
            $articulo["description"] = $pArticulo["short_description"];
        }

        $resp = $meli->post("items/".$pArticulo["ml_articulo_id"]."/relist",$articulo,$params);

        if(($resp["httpCode"] == "200" || $resp["httpCode"] == "201")){
            $this->actualizarEstadoArticulo($pArticulo,$resp);
            $this->insertLog($pArticulo["id"], "republicar", $resp,false,$id_evento);
            return (array(
                "id" => $pArticulo["id"],
                "error" => false,
                "body" => $resp["body"],
                "httpCode" => $resp["httpCode"]
            ));
        }else{
            return (array(
                "id" => $pArticulo["id"],
                "error" => true,
                "body" => $resp["body"],
                "httpCode" => $resp["httpCode"]
            ));
        }
    }

    public function getArticulosFomrMlAction(){

    }

    public function cronUpdatePublicaciones($id_evento){
        $articulos = $this->getArticulosCron();
        //estado_alerta
        $results = array();
        foreach($articulos["articulos"] as $articulo){
            if($articulo[self::columnItemEstatus] == "A"){
                return;
                return $this->publicarArticuloSku($articulo,$id_evento);
            }else{
                $result = $this->modificarEstadoSku($articulo,$articulo[self::columnItemEstatus],$id_evento);
            }

            $results[] = $result;
        }
        //$this->actualizarHistoryAlert();
        $this->dropNotificaciones();

        return $results;
    }

    public function logTrace($error){}

    public function actualizarHistoryAlert($articulo,$resp){
        $this->adapter = $this->tableGateway->getAdapter();

        $sql = 'INSERT INTO '.tableNameAlertsHistory.' (id,_attribute_set,_type,controled,categories,_root_category,_product_websites,description,name,price,special_price,short_description,status,tax_class_id,visibility,weight,image,small_image,thumbnail,qty,estado,fecha_publicacion,permalink,date_created,last_updated,ml_articulo_id,event_alert)
        (SELECT ar.id,ar._attribute_set,ar._type,ar.controled,ar.categories,ar._root_category,ar._product_websites,ar.description,ar.name,ar.price,ar.special_price,ar.short_description,ar.status,ar.tax_class_id,ar.visibility,ar.weight,ar.image,ar.small_image,ar.thumbnail,ar.qty,ar.estado,ar.fecha_publicacion,ar.permalink,ar.date_created,ar.last_updated,ar.ml_articulo_id,al.estado
         FROM '.tableNameArticlulos.' as ar
         INNER JOIN '.tableNameAlerts.' as al on ar.id = al.id)';

        $res = $this->adapter->query($sql);

        if($res){
            return $result = $res->execute();
        }

        return false;
    }

    public function dropNotificaciones(){
        $this->adapter = $this->tableGateway->getAdapter();
        $sql = "TRUNCATE TABLE ".self::tableNameAlerts;
        $res = $this->adapter->query($sql);

        if($res){
            return $result = $res->execute();
        }

        return false;
    }

    public function publicarArticuloSku($articulo,$id_evento){
        $mapeo = $this->getCategoriaMapeo(null,$articulo["articulo"]["categories"]);
        $this->publicarArticuloSendJson($articulo,$mapeo);
    }

    public function modificarEstadoSku($articulo,$estado,$id_evento){
        $tempArticulo = $articulo;
        $params = array('access_token' => $_SESSION['access_token']);
        $meli = new Meli($this->config["appId"], $this->config["key"], $_SESSION['access_token'], $_SESSION['refresh_token']);
        $status = $articulo[self::columnItemEstatus];
        $articuloId = $articulo["id"];
        $articuloMlId = $articulo["ml_articulo_id"];

        if(isset($articulo["short_description"]) && $articulo["short_description"]){
            $articulo["description"] = $articulo["short_description"];
        }

        $template = $this->setTemplate($articulo);

        $price = (int) $this->fixSpecialPrice($articulo);

        $articulo = array(
            "title" => $articulo["name"],
            "price"=>(int) $price,
        );

        if($status == "B"){
            $articulo["status"] = "closed";
        }

        $error = false;

        //Solo para Modificar Descripcion
        if($status == "M"){
            $resp = $meli->put("items/".$articuloMlId."/",$articulo,$params);
            $articulo = array("text" => $template,);
            $resp_description = $meli->put("items/".$articuloMlId."/description/",$articulo,$params);
        }else{
            $resp = $meli->put("items/".$articuloMlId."/",$articulo,$params);
        }

        $this->insertLog($articuloId, "closed", $resp,true,$id_evento);

        if($resp["httpCode"] == "200" || $resp["httpCode"] == "201"){
            $this->actualizarEstadoArticulo($tempArticulo,$resp);
            return (array(
                "id" => $articuloId,
                "error" => false,
                "body" => $resp["body"],
                "httpCode" => $resp["httpCode"]
            ));
        }else{
            return (array(
                "id" => $articuloId,
                "error" => true,
                "body" => $resp["body"],
                "httpCode" => $resp["httpCode"]
            ));
        }

        return $resp;
    }

    public function publicarArticulo($filtros=null,$id_evento=0){
        $articulo = $this->getArticulo($filtros,$id_evento);
        if($articulo["articulo"]){

            if($this->isEnabled($articulo)){

                $chk = $this->checkCatIsMapped($articulo["articulo"]["categories"]);
                if($chk){
                    $mapeo = $this->getCategoriaMapeo(null,$articulo["articulo"]["categories"]);

                    if($filtros["accion"] == "PUBLICAR"){
                        return $this->publicarArticuloSendJson($articulo,$mapeo,$id_evento);
                    }elseif($filtros["accion"] == "REPUBLICAR"){
                        return $this->republicarArticulo($articulo,$mapeo,$id_evento);
                    }else{
                        return $this->modificarEstadoPublicacion($articulo,$mapeo,$filtros["accion"],$id_evento);
                    }
                }

                return (array(
                    "error" => true,
                    "body" => "Categoria sin mapear.",
                    "httpCode" => ""
                ));
            }
        }

        return (array(
            "error" => false,
            "body" => "end process.",
            "httpCode" => "101"
        ));
    }

    public function insertLog($idArticulo, $accion, $resp,$cron=false,$id_evento=0){
        $this->adapter = $this->tableGateway->getAdapter();
        $status = $resp["body"]->status;
        $table = "logs";

        if($cron){
            $table = "logs_cron";
        }

        $sql = "INSERT INTO ".$table." (id_articulo, accion, respuesta, fecha, status,id_evento) VALUES ('".$idArticulo."','".$accion."','".json_encode($resp)."','".date("Y-m-h h:m:s")."','".$status."','".$id_evento."')";
        $res = $this->adapter->query($sql);

        if($res){
            try{
                $res->execute();
            }catch (\Exception $e){
                echo "<pre>";
                print_r($e->getMessage());die();
            }

            return true;
        }

        return false;
    }

    public function modificarEstadoPublicacion($pArticulo,$mapeo,$estado,$id_evento=0){

        $pArticulo = $pArticulo["articulo"];
        $articulo = array();
        $this->mapeoArticulo($pArticulo,$mapeo);

        $params = array('access_token' => $_SESSION['access_token']);
        $meli = new Meli($this->config["appId"], $this->config["key"], $_SESSION['access_token'], $_SESSION['refresh_token']);

        $articuloMl = $this->getArticuloMl($pArticulo["ml_articulo_id"]);
        $error_estado = false;
        $idEstado = "error";

        if($estado == "PAUSAR"){
            if($articuloMl->status == "active"){
                $articulo["status"] = "paused";
            }else{
                $error_estado = true;
            }
        }

        if($estado == "DESPAUSAR"){
            if($articuloMl->status == "paused"){
                $articulo["status"] = "active";
            }else{
                $error_estado = true;
            }
        }

        if($estado == "FINALIZAR"){
            if($articuloMl->status != "closed"){
                $articulo["status"] = "closed";
            }else{
                $error_estado = true;
            }
        }

        if($error_estado){
            $this->sincronizarEstadoConMl($articuloMl);
            return (array(
                "error" => false,
                "body" => "status updated",
                "httpCode" => ""
            ));
        }

        $resp = $meli->put("items/".$pArticulo["ml_articulo_id"]."/",$articulo,$params);

        $idEstado = $resp["body"]->status;
        $error = false;

        $this->insertLog($pArticulo["id"], strtolower($estado), $resp,false,$id_evento);

        if($resp["httpCode"] == "200" || $resp["httpCode"] == "201"){
            $this->actualizarEstadoArticulo($pArticulo,$resp,$idEstado);
            return (array(
                "id" => $pArticulo["id"],
                "error" => false,
                "body" => $resp["body"],
                "httpCode" => $resp["httpCode"]
            ));
        }else{
            return (array(
                "id" => $pArticulo["id"],
                "error" => true,
                "body" => $resp["body"],
                "httpCode" => $resp["httpCode"]
            ));
        }
    }

    private function file_contents($path,$id) {
        $str = @file_get_contents($path);
        if ($str === FALSE) {
            throw new \Exception(json_encode(array(
                "error" => true,
                "id" => $id,
                "message" => "Cannot access '$path' to read contents.",
            )));
        } else {
            return $str;
        }
    }

    public function getArticuloMl($id){
        $resp = false;
        try{
            $resp = $this->file_contents("https://api.mercadolibre.com/items/".$id,$id);
            $resp = json_decode($resp);
        }catch(\Exception $e){

            die(json_encode(array("error" => true, "message " => $e->getMessage())));
        }
        return $resp;
    }

    public function parseoMensajesMl($mlMsg){
        $mensaje = $mlMsg;

        switch($mlMsg){
            case "seller.unable_to_list":
                $mensaje = "El usuario de mercadolibre no esta habilitado para vender.";//USUARIO_ML_NO_HABILITADO;
                break;
        }

        return $mensaje;
    }

    public function getUserData(){
        $params = array('access_token' => $_SESSION['access_token']);
        $meli = new Meli($this->config["appId"], $this->config["key"], $_SESSION['access_token'], $_SESSION['refresh_token']);

        $resp = $meli->get("/users/me",$params);

    }

    public function publicarArticuloSendJson($pArticulo,$mapeo=null,$id_evento=0){
        $pArticulo = $pArticulo["articulo"];
        $articulo = $this->mapeoArticulo($pArticulo,$mapeo);

        $params = array('access_token' => $_SESSION['access_token']);
        $meli = new Meli($this->config["appId"], $this->config["key"], $_SESSION['access_token'], $_SESSION['refresh_token']);

        $resp = $meli->post("items",$articulo,$params);

        $error = false;

        //"id" => $pArticulo["id"],

        if($resp["httpCode"] == "200" || $resp["httpCode"] == "201"){
            $this->actualizarEstadoArticulo($pArticulo,$resp);
            $this->insertLog($pArticulo["id"], "publicar", $resp,false,$id_evento);
            return (array(
                "id" => $pArticulo["id"],
                "error" => false,
                "body" => $resp["body"],
                "httpCode" => $resp["httpCode"]
            ));
        }else{
            return (array(
                "id" => $pArticulo["id"],
                "error" => true,
                "body" => $resp["body"],
                "httpCode" => $resp["httpCode"]
            ));
        }

    }


    public function isEnabled($pArticulo){
        if($pArticulo["articulo"]["visibility"]){
            return true;
        }

        return false;
    }

    public function getArticulo($filtros,$id_evento=null){

        $articulos = $this->getArticuloProceso($id_evento);


        $this->adapter = $this->tableGateway->getAdapter();

        $where = " WHERE visibility='4' ";

        if($filtros["categorias"] && $filtros["categorias"] != "todas"){
            $where .= "AND categories='".$filtros["categorias"]."'";
        }

        if($filtros["estado"]){
            $where .= " AND estado='".$filtros["estado"]."'";
        }

        if($articulos){
            $where .= " AND id NOT IN (".implode(',', $articulos).")";
        }

        $sql = "SELECT * FROM articulos $where ORDER BY id ASC LIMIT 1";

        $res = $this->adapter->query($sql);

        if($res){
            $result = $res->execute();
            foreach($result as $r){
                $this->setArticuloProceso($r["id"],$id_evento);
                return array("articulo" => $r);
            }
        }

        return array("articulo" => false);
    }

    public function getArticulosCron(){
        $this->adapter = $this->tableGateway->getAdapter();
        $articulos = array();

        $sql = "
        SELECT ar.id,ar._attribute_set,ar._type,ar.controled,ar.categories,ar._root_category,ar._product_websites,ar.description,ar.name,ar.price,ar.special_price,ar.short_description,ar.status,ar.tax_class_id,ar.visibility,ar.weight,ar.image,ar.small_image,ar.thumbnail,ar.qty,ar.estado,ar.fecha_publicacion,ar.permalink,ar.date_created,ar.last_updated,ar.ml_articulo_id,al.estado as ".self::columnItemEstatus."
        FROM ".self::tableNameArticlulos." as ar
        INNER JOIN ".self::tableNameAlerts." as al on ar.id = al.id";

        $res = $this->adapter->query($sql);

        if($res){
            $result = $res->execute();
            foreach($result as $r){
                $articulos[] = $r;
            }
        }

        return array("articulos" => $articulos);
    }

    public function sincronizarEstadoConMl($articuloMl){
        $this->adapter = $this->tableGateway->getAdapter();
        $estado = $articuloMl->status;


        $date_created = "";
        $last_updated = "";
        $articuloMlId = "";
        $permalink = "";

        if(isset($articuloMl->date_created)){
            $date_created = $articuloMl->date_created;
        }

        if(isset($articuloMl->last_updated)){
            $last_updated = $articuloMl->last_updated;
        }

        if(isset($articuloMl->id)){
            $articuloMlId = $articuloMl->id;
        }

        if(isset($articuloMl->permalink)){
            $permalink = $articuloMl->permalink;
        }

        $sql = "UPDATE articulos SET estado='".$estado."',
				permalink='".$permalink."',
				date_created='".$date_created."',
				last_updated='".$last_updated."'
				WHERE ml_articulo_id='".$articuloMlId."'";

        $res = $this->adapter->query($sql);

        if($res){
            $res->execute();
            return true;
        }

        return false;
    }

    public function actualizarEstadoArticulo($articulo,$resp){
        $this->adapter = $this->tableGateway->getAdapter();
        $resp = $resp["body"];
        $estado = $resp->status;

        $sql = "UPDATE articulos SET estado='".$estado."', ml_articulo_id='".$resp->id."', permalink='".$resp->permalink."', date_created='".$resp->date_created."', last_updated='".$resp->last_updated."' WHERE id='".$articulo["id"]."'";

        $res = $this->adapter->query($sql);

        if($res){
            $res->execute();
            return true;
        }

        return false;
    }

    public function mapeoArticulo($articulo,$mapeo){

        if(isset($articulo["short_description"]) && $articulo["short_description"]){
            $articulo["description"] = $articulo["short_description"];
        }

        $template = $this->setTemplate($articulo);

        $categoryId = explode(",",$mapeo["categoria_ml"]);
        $categoryId = $categoryId[count($categoryId)-1];

        $price = (int) $this->fixSpecialPrice($articulo);

        $articulo = array(
            "title" => $articulo["name"],
            "category_id"=> $categoryId,
            "price" => $price,
            "currency_id" => "UYU",
            "available_quantity" => $articulo["qty"],
            "buying_mode" => "buy_it_now",
            "listing_type_id" => $this->config["listing_type"],
            "condition" => "new",
            "description" => $template,
            "video_id" => "",
            "warranty" => "",
            "pictures" => array(
                array("source" => $this->config["urlServidorImagenes"].$articulo["id"] . "." . $this->config["extensionImagenes"]),
            ),
        );

        if(isset($this->config["store_id"]) && $this->config["store_id"]){
            $articulo["official_store_id"] = $this->config["store_id"];
        }


        return $articulo;
    }

    public function setTemplate($articulo){
        $productImg = $this->config["urlServidorImagenes"].$articulo["id"] . "." . $this->config["extensionImagenes"];
        $config = $this->config;

        $sql = "SELECT categorias_ml,categoria_geopos,template FROM mapeo_categorias WHERE categoria_geopos='".$articulo["categories"]."'";
        $res = $this->adapter->query($sql);

        $template = "";

        if($res){
            $result = $res->execute();
            foreach($result as $r){
                $template["template"] = $r["template"];
                $template["categorias_ml"] = $r["categorias_ml"];
                $template["categoria_geopos"] = $r["categoria_geopos"];
            }
        }

        $template = $_SERVER['DOCUMENT_ROOT']."/templates/".$template["template"];

        if(is_file($template)){
            $template = include $template;
        }


        return $template;
    }

    public function getCategoriasFromMl($category=null){

        if(!$category){
            return file_get_contents("https://api.mercadolibre.com/sites/MLU/categories");
        }

        if(is_array($category)){
            if(!$category[0]){
                return file_get_contents("https://api.mercadolibre.com/sites/MLU/categories");
            }
            $category = $category[count($category)-1];
        }

        return file_get_contents("https://api.mercadolibre.com/categories/".$category);
        //path_from_root
    }

    public function getCateogriaMlDb($id){
        $this->adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT * FROM categorias_ml WHERE id_categoria_ml='".$id."'";

        $res = $this->adapter->query($sql);
        if($res){
            $result = $res->execute();
            foreach($result as $r){
                return $r;
            }
        }

        return false;
    }

    public function insertCategoriasML($cat){
        $this->adapter = $this->tableGateway->getAdapter();

        //$categorias = $cat["catMl"];

        foreach($cat["catMl"] as $categoria){
            $objCategoria = json_decode($this->getCategoriasFromMl($categoria));
            $nombreCategoria = $objCategoria->name;
            if(!$this->getCateogriaMlDb($objCategoria->id)){
                $sql = "INSERT INTO categorias_ml(id_categoria_ml, nombre_categoria_ml) VALUES ('".$categoria."','".$nombreCategoria."')";
                $res = $this->adapter->query($sql);
                if($res){
                    $res->execute();
                }
            }
        }

        return false;
    }

    public function editCategoriasArticulos($cat){
        $this->insertCategoriasML($cat);
        $this->adapter = $this->tableGateway->getAdapter();

        $exist = $this->getCategoriaMapeo($cat["name_cat"]);

        $categorias = implode(",",$cat["catMl"]);
        $categoria = $cat["catMl"][count($cat["catMl"])-1];

        $template = $cat["template"];

        if(!$exist){
            $sql = "INSERT INTO mapeo_categorias(input_id, categoria_geopos, categoria_ml,categorias_ml, template) 
                    VALUES ('".$cat["name_cat"]."','".$cat["catGeopos"]."','".$categoria."','".$categorias."', '".$template."')";
        }else{
            $sql = "UPDATE mapeo_categorias 
                    set categoria_geopos='".$cat["catGeopos"]."',
                    categoria_ml='".$categoria."',
                    categorias_ml='".$categorias."',
                    template = '".$template."'
                    WHERE input_id='".$cat["name_cat"]."'";
        }

        $res = $this->adapter->query($sql);

        if($res){
            $res->execute();
            return true;
        }

        return false;
    }

    public function getCategoriaMapeo($inputId,$categoria_geopos=null){
        $this->adapter = $this->tableGateway->getAdapter();

        $campo = "input_id";
        if($categoria_geopos){
            $inputId = $categoria_geopos;
            $campo = "categoria_geopos";
        }

        $sql = "SELECT * FROM mapeo_categorias WHERE $campo='".$inputId."'";
        $res = $this->adapter->query($sql);

        if($res){
            $result = $res->execute();
            foreach($result as $r){
                return $r;
            }
        }

        return false;
    }

    public function validarCategoria($category){
        $ret = file_get_contents("https://api.mercadolibre.com/categories/".$category);

        if($ret){
            $ret = json_decode($ret);
            if(!$ret->settings->listing_allowed){
                return json_encode(array(
                    "error" => true,
                    "message" => "La categoria(s) seleccionada(s) no son aplicables, seleccione una Categoria hija."
                ));
            }
        }
        return json_encode(array(
            "error" => false,
            "message" => ""
        ));
    }

    public function fixSpecialPrice($r){
        if(isset($r["special_price"]) && $r["special_price"]){
            if($r["special_price"] > 0 && ($r["special_price"] < $r["price"])){
                $r["price"] = $r["special_price"];
            }
        }

        return $r["price"];
    }

    public function getTemplates(){
        $tmp = array();
        $templates = scandir(getcwd()."/public/templates");
        foreach($templates as $template){
            if($template != '.'){
                if($template != '..'){
                    $tmp[] = $template;
                }

            }
        }

        return $tmp;
    }

    public function obtenerTodos($filtros = null) {

        $articulos = array();

        $sql = $this->tableGateway->getSql();

        $select = $this->tableGateway->getSql()->select();

        $filtroLike = array();
        $filtroWhere = array("visibility"=>"4");

        if($filtros){
            foreach($filtros as $key => $value){
                if($key != "name"){
                    $filtroWhere[$key] = $value;
                }else{
                    $filtroLike[$key] = $value;
                }
            }
        }


        $select->where($filtroWhere);
        if($filtroLike){
            $select->where->like(key($filtroLike),"%".($filtroLike[key($filtroLike)])."%");
        }

        $select->order('id ASC');

        //$salida = $select->getSqlString();

        $this->listaUsuario = $this->tableGateway->selectWith($select);

        $adapter = new \Zend\Paginator\Adapter\DbSelect($select, $sql);
        $paginator = new \Zend\Paginator\Paginator($adapter);

        foreach ($this->listaUsuario as $item) {
            $unArticulo = new Articulo();
            $unArticulo->setId($item["id"]);
            $unArticulo->setNombre($item["name"]);
            $unArticulo->setCategorias($item["categories"]);
            $unArticulo->setEstado($item["estado"]);
            $articulos[] = $unArticulo;
        }

        return array("articulos" => $articulos, "paginator" => $paginator);
    }

    public function getEstadosArticulos(){
        $estados = array();
        $this->adapter = $this->tableGateway->getAdapter();

        $sql = "SELECT DISTINCT estado FROM articulos";

        $res = $this->adapter->query($sql);

        if($res){
            $result = $res->execute();
            foreach($result as $r){
                $estados[] = $r;
            }
        }

        return $estados;
    }

    public function getMaxIdEvento($table = "logs_cron"){
        $this->adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT MAX(id_evento) as last_id FROM ".$table." WHERE 1";

        $res = $this->adapter->query($sql);

        if($res){
            $result = $res->execute();
            foreach($result as $r){
                if(!$r["last_id"]){
                    $r["last_id"] = 0;
                }
                return $r["last_id"]+1;
            }
        }

        return 0;
    }

    public function getMaxIdProceso(){
        $this->adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT MAX(id_proceso) as last_id FROM articulo_proceso WHERE 1";

        $res = $this->adapter->query($sql);

        if($res){
            $result = $res->execute();
            foreach($result as $r){
                if(!$r["last_id"]){
                    $r["last_id"] = 0;
                }
                return $r["last_id"]+1;
            }
        }

        return 0;
    }

    public function setArticuloProceso($idArticulo,$idProceso){
        $this->adapter = $this->tableGateway->getAdapter();

        $sql = "INSERT INTO articulo_proceso (id_articulo, id_proceso) VALUES (".$idArticulo.", ".$idProceso.")";


        $res = $this->adapter->query($sql);

        if($res){
            if($res->execute()){
                return true;
            }
        }

        return false;
    }

    public function getArticuloProceso($idProceso){
        $this->adapter = $this->tableGateway->getAdapter();
        $sql = "SELECT id_articulo FROM articulo_proceso WHERE id_proceso=".$idProceso;
        $res = $this->adapter->query($sql);
        $articulos = array();
        if($res){
            $result = $res->execute();
            foreach($result as $r){
                $articulos[] = $r["id_articulo"];
            }
        }

        return $articulos;
    }

    private function isJson($string) {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }

}

