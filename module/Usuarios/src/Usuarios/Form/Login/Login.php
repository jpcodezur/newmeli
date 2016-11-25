<?php

namespace Usuarios\Form\Login;

use Zend\Form\Element;
use Zend\Form\Element\Password;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;

class Login extends Form {

    public function __construct($name = "") {

        parent::__construct();
        
        $this->add(array(
            'name' => 'nombre',
            'atributes' => array(
                'type' => 'text')
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'mensaje',
            'options' => array(
                'label' => 'Mensaje',
            )
        ));
        
        $this->add(array(
            'name' => 'clave',
            'type' => 'Zend\Form\Element\Password'
        ));
        $this->add(array(
            'name' => 'send',
            'type' => 'submit'
        ));
    }

}

