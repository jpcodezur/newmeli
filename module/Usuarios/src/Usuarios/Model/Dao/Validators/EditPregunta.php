<?php

namespace Usuarios\Model\Dao\Validators;

use Usuarios\Model\Dao\Validators\EditBasico;

class EditPregunta extends EditBasico{
    
    public function __construct($tableGateway,$dao,$params) {
        parent::__construct($tableGateway,$dao,$params);
    }
    
}