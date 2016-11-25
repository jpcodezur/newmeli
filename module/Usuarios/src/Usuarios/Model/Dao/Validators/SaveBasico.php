<?php

namespace Usuarios\Model\Dao\Validators;

class SaveBasico {

    protected $tableGateway;
    protected $dao;
    protected $params;

    public function __construct($tableGateway="", $dao, $params) {
        $this->tableGateway = $tableGateway;
        $this->dao = $dao;
        $this->params = $params;
    }

    public function validate($entity) {

        $response = new \Usuarios\MisClases\Respuesta();

        $response->setError(false);

        if (isset($this->params["validate"]["save"]["required"])) {
            foreach ($this->params["validate"]["save"]["required"] as $key => $value) {
                $get = "get" . ucwords($value);
                if ($this->required($entity->$get()) == false) {
                    $response->setError(true);
                    $response->setMensaje($key . " is  required");
                    return $response;
                }
            }
        }

        if (isset($this->params["validate"]["save"]["strExist"])) {
            foreach ($this->params["validate"]["save"]["strExist"] as $exist) {
                $get = "get" . ucwords($exist);
                if ($this->strExist($exist, $entity->$get())) {
                    $response->setError(true);
                    $response->setMensaje(ucwords($exist) . " Already Exist");
                    return $response;
                }
            }
        }

        if (isset($this->params["validate"]["save"]["numeric"])) {
            foreach ($this->params["validate"]["save"]["numeric"] as $numeric) {
                $get = "get" . ucwords($numeric);
                if (!$this->isNumeric($entity->$get())) {
                    $response->setError(true);
                    $response->setMensaje(ucwords($numeric) . " must be a numer.");
                    return $response;
                }
            }
        }

        if (isset($this->params["validate"]["save"]["strlen"])) {
            foreach ($this->params["validate"]["save"]["strlen"] as $key => $value) {
                $get = "get" . ucwords($key);
                if (!$this->strLen($value, $entity->$get())) {
                    $response->setError(true);

                    $mensaje = ucwords($key) . " must have a minimum of " . $value["min"];

                    if ($value["max"] != -1) {
                        $mensaje .= "and a maximum of " . $value["max"];
                    }

                    $mensaje .= " characters.";

                    $response->setMensaje($mensaje);
                    return $response;
                }
            }
        }

        return $response;
    }

    public function required($field) {
        if (!$field) {
            return false;
        }

        return true;
    }

    public function strExist($exist, $value) {
        return $this->dao->fetchOne(array($exist => $value));
    }

    public function isNumeric($value) {
        return is_numeric($value);
    }

    public function strLen($params, $value) {

        if (isset($params["min"])) {
            if (strlen($value) < $params["min"]) {
                return false;
            }
        }

        if (isset($params["max"])) {
            if ($params["max"] != -1) {
                if (strlen($value) > $params["max"]) {
                    return false;
                }
            }
        }

        return true;
    }

}
