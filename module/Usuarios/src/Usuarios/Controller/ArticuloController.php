<?php

namespace Usuarios\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Usuarios\Model\Dao\ArticuloDao;
use Zend\View\Model\JsonModel;

class ArticuloController extends AbstractActionController {

    private $articuloDao;

    public function setDao($dao){
        $this->dao = new ArticuloDao($dao);
    }

    public function setArticuloDao($articuloDao) {
        $this->articuloDao = $articuloDao;
    }

    public function indexAction(){
        $result = $this->articuloDao->getCategorias();
        $templates = $this->articuloDao->getTemplates();
		$categoriasBase = ($this->articuloDao->getCategoriasFromMl());
        return new ViewModel(array("templates" => $templates, "categorias" => $result["categorias"], "categoriasBase" => $categoriasBase));
    }

    public function publicarAction(){
        $result = $this->articuloDao->getCategorias();
        $estados = $this->articuloDao->getEstados();
        return new ViewModel(array("categorias" => $result["categorias"],"estados" => $estados));
    }
	
	public function importCSVAction(){
        return new ViewModel(array());
    }
	

    public function getTotalArticulosFilterAction(){
        $filters = $this->getRequest()->getPost("filters", null);
        $result = $this->articuloDao->getTotalArticulos($filters);
        return new JsonModel(array("ret" => $result["ret"]));
    }

	public function publicarArticuloAction(){
        $filters = $this->getRequest()->getPost("filters", null);
        $id_evento = $this->getRequest()->getPost("id_evento", null);
		$result = $this->articuloDao->publicarArticulo($filters,$id_evento);
		
		if(isset($result["httpCode"])){
			if($result["httpCode"] == "101"){
				
			}
		}
		
		return new JsonModel($result);
	}
	
	public function getCategoriasFromMlAction(){
		$category = $this->getRequest()->getQuery("category", null);
		$result = ($this->articuloDao->getCategoriasFromMl($category));
		$result = json_decode($result);
		
		if(isset($result->children_categories)){
			$result = $result->children_categories;
		}
		
		$items = array();
		
		foreach($result as $item){
			//echo "<pre>";print_r($item);die();
			$temp = new \stdClass();
			$temp->id = $item->id;
			$temp->text = $item->name;
			$items[] = $temp;
		}
		
		echo json_encode($items);
		die();
	}
	
	public function editCategoriasArticulosAction(){
		$res = array();
//		echo "<pre>";print_r($_REQUEST);die();
        $template = $_REQUEST["cat_template"];
		foreach($_REQUEST as $key => $value){
			$pos = strpos($key,"ml_categoria_");
			if($pos!==false){
				$name_cat = "geopos".substr($key,2,strlen($key));
				$catGeopos = $_REQUEST[$name_cat];
				$catMl = $_REQUEST[$key];
				$res[] = array(
					"name_cat" => $name_cat,
					"catGeopos" => $catGeopos,
					"catMl" => $catMl,
                    "template" => $template
				);

			}
				
		}
		
		foreach($res as $cat){
			$result = $this->articuloDao->editCategoriasArticulos($cat);
		}
		$this->flashMessenger()->addMessage('Categoria Mapeada.');
		return $this->redirect()->toRoute('usuarios', array('controller' => 'articulo', 'action' => 'index'));
	}
	
	public function validarCategoriaAction(){
		$category = $this->getRequest()->getPost("category", null);
		$result = $this->articuloDao->validarCategoria($category);
		return new JsonModel(array("result" => $result));
	}

	public function getArticulosFomrMlAction(){
        $result = $this->articuloDao->getArticulosFomrMlAction();
        return new JsonModel(array("result" => $result));
    }
	
	public function cronUpdatePublicacionesAction(){
        $cron = null;
        if(isset($_REQUEST["cron"]) && $_REQUEST["cron"]){
            $cron = true;
        }

        $id_evento = $this->articuloDao->getMaxIdEvento();
		$result = $this->articuloDao->cronUpdatePublicaciones($id_evento);
        $status = array(
            "complete" => 0,
            "error" => 0,
            "total" => 0,
            "items" => array()
        );

        foreach($result as $r){
            if($r["httpCode"] == "200" || $r["httpCode"] == "201"){
                $status["complete"] += 1;
                $status["total"] += 1;
                $status["items"][] = array(
                    "title" => $r["body"]->title,
                    "status" => $r["body"]->status,
                    "httpCode" => $r["httpCode"],
                    "success" => true,
                );
            }else{
                $status["total"] += 1;
                $status["error"] += 1;
                $status["items"][] = array(
                    "httpCode" => $r["httpCode"],
                    "success" => false,
                );
            }

        }

        if($cron){
            return new JsonModel(array("result" => $status));
        }
        return new ViewModel(array("result" => $status));
	}

    public function ListAction() {
        $usuario = $this->getServiceLocator()->get('articuloDao');
        $filtros = array();

        if(isset($_GET["inputBusqueda"]) && $_GET["inputBusqueda"]){
            $filtros["name"] = $_GET["inputBusqueda"];
        }

        if(isset($_GET["estado"]) && $_GET["estado"]){
            $filtros["estado"] = $_GET["estado"];
        }
        if(isset($_GET["categoria"]) && $_GET["categoria"]){
            $filtros["categories"] = $_GET["categoria"];
        }
        $result = $this->articuloDao->obtenerTodos($filtros);
        $paginator = $result["paginator"];
        $pn = (int) $this->getEvent()->getRouteMatch()->getParam('id', 1);
        $paginator->setCurrentPageNumber($pn);
        $categorias = $this->articuloDao->getCategorias();
        $estadosArticulos = $this->articuloDao->getEstadosArticulos();
        return new ViewModel(array("estados" => $estadosArticulos,"categorias" => $categorias["categorias"],"articulos" => $result["articulos"], "paginator" => $paginator));
    }

    public function getMaxIdEventoAction(){
        $table = $_REQUEST["table"];
        $result = $this->articuloDao->getMaxIdProceso();
        return new JsonModel(array("id_evento" => $result));
    }

}
