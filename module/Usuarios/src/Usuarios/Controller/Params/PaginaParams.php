<?php
namespace Usuarios\Controller\Params;

class PaginaParams{
    
    private $params;
    
    public function __construct(){
        
        $this->setParams(array(
            "table" => "paginas",
            "plural" => "Form Pages",
            "singular" => "Form Page",
            "controller" => "pagina",
            "entity" => "Usuarios\Model\Entity\Pagina",
            "attrs" => array("id", "titulo", "orden", "estado"),
            "add_attrs" => array("titulo", "orden"),
            "edit_attrs" => array("titulo", "orden"),
            "list_attrs" => array("titulo", "orden",
                array("estado" => array(
                        "true" => '<span class="label label-success">%value%</span>',
                        "false" => '<span class="label label-danger">%value%</span>'))),
            "validate" => array(
                "save" => array(
                    "strExist" => array("titulo"),
                    "strlen" => array("titulo" => array(
                            "min" => "5",
                            "max" => "-1")),
                    "numeric" => array("orden")
                ),
                "edit" => array(
                    "strlen" => array("titulo" => array(
                            "min" => "5",
                            "max" => "-1")),
                    "numeric" => array("orden")
                )
            )
        ));
    }
    function getParams() {
        
        return $this->params;
    }

    function setParams($params) {
        $this->params = $params;
    }


}


