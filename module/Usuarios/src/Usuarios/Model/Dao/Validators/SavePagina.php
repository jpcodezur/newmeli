<?php

namespace Usuarios\Model\Dao\Validators;

use Usuarios\Model\Dao\Validators\SaveBasico;

class SavePagina extends SaveBasico{
    
    public function __construct($tableGateway,$dao,$params) {
        parent::__construct($tableGateway,$dao,$params);
    }
    
}