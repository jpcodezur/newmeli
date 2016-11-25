<?php

namespace Usuarios\Form\Login;

use Zend\Validator\StringLength;
use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;

class LoginValidator extends InputFilter {

    public function __construct() {
        $this->add(
                array(
                    'name' => 'nombre',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'min' => 4,
                                'max' => 50,
                                'messages' => array(
                                    StringLength::TOO_SHORT => 'El nombre de usuario debe tener minimo 4 caracteres.',
                                    StringLength::TOO_LONG => 'El nombre de usuario debe tener maximo 50 caracteres.',
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    NotEmpty::IS_EMPTY => 'El campo nombre no puede estar vacio.',
                                )
                            )
                        )
                    )
                )
        );
        
        $this->add(
                array(
                    'name' => 'clave',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'StripTags'),
                        array('name' => 'StringTrim'),
                    ),
                    'validators' => array(
                        array(
                            'name' => 'StringLength',
                            'options' => array(
                                'min' => 4,
                                'messages' => array(
                                    StringLength::TOO_SHORT => 'El password de usuario debe tener minimo 4 caracteres.',
                                )
                            )
                        ),
                        array(
                            'name' => 'NotEmpty',
                            'options' => array(
                                'messages' => array(
                                    NotEmpty::IS_EMPTY => 'El campo password no puede estar vacio.',
                                )
                            )
                        )
                    )
                )
        );
    }

}