<?php

namespace Usuarios\Controller\Params;

class InputParams {

    private $params;

    public function __construct() {

        $this->setParams(array(
            "validate" => array(
                "save" => array(
                    "strExist" => array("nombre","label"),
                    "strlen" => array("nombre" => array(
                            "min" => "5",
                            "max" => "-1")),
                    "numeric" => array("orden")
                ),
                "edit" => array(
                    "strlen" => array("nombre" => array(
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
