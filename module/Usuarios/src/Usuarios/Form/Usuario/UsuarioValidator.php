<?php

namespace Usuarios\Form\Usuario;

use Zend\Validator\StringLength;
use Zend\InputFilter\InputFilter;
use Zend\Validator\NotEmpty;

class UsuarioValidator extends InputFilter {

    public function __construct() {
        $this->add(
                array(
                    'name' => 'email-user',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Zend\Filter\StringTrim')
                    ),
                    'validators' => array(
                        array('name' => 'NotEmpty',
                            'options' => array('encoding' => 'UTF-8',
                                'messages' => array(
                                    NotEmpty::IS_EMPTY => 'Please enter E-mail')),
                            'break_chain_on_failure' => true),
                        array(
                            'name' => 'Zend\Validator\StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 3,
                                'max' => 30,
                                'messages' => array(
                                    StringLength::TOO_LONG => 'E-mail can not be more than 30 characters long',
                                    StringLength::TOO_SHORT => 'E-mail can not be less than 3 characters.')
                            ),
                            'break_chain_on_failure' => true
                        ),
                    )
                )
        );
        
        $this->add(
                array(
                    'name' => 'first-name-user',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Zend\Filter\StringTrim')
                    ),
                    'validators' => array(
                        array('name' => 'NotEmpty',
                            'options' => array('encoding' => 'UTF-8',
                                'messages' => array(
                                    NotEmpty::IS_EMPTY => 'Please enter First Name')),
                            'break_chain_on_failure' => true),
                        array(
                            'name' => 'Zend\Validator\StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 3,
                                'max' => 30,
                                'messages' => array(
                                    StringLength::TOO_LONG => 'First Name can not be more than 30 characters long',
                                    StringLength::TOO_SHORT => 'First Name can not be less than 3 characters.')
                            ),
                            'break_chain_on_failure' => true
                        )
                    )
                )
        );
        
        $this->add(
                array(
                    'name' => 'last-name-user',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Zend\Filter\StringTrim')
                    ),
                    'validators' => array(
                        array('name' => 'NotEmpty',
                            'options' => array('encoding' => 'UTF-8',
                                'messages' => array(
                                    NotEmpty::IS_EMPTY => 'Please enter Last Name')),
                            'break_chain_on_failure' => true),
                        array(
                            'name' => 'Zend\Validator\StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 3,
                                'max' => 30,
                                'messages' => array(
                                    StringLength::TOO_LONG => 'Last Name can not be more than 30 characters long',
                                    StringLength::TOO_SHORT => 'Last Name can not be less than 3 characters.')
                            ),
                            'break_chain_on_failure' => true
                        )
                    )
                )
        );
        
        $this->add(
                array(
                    'name' => 'pass-user',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Zend\Filter\StringTrim')
                    ),
                    'validators' => array(
                        array('name' => 'NotEmpty',
                            'options' => array('encoding' => 'UTF-8',
                                'messages' => array(
                                    NotEmpty::IS_EMPTY => 'Please enter Password')),
                            'break_chain_on_failure' => true),
                        array(
                            'name' => 'Zend\Validator\StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 4,
                                'messages' => array(
                                    StringLength::TOO_LONG => 'Password can not be less than 3 characters.'),
                            ),
                            'break_chain_on_failure' => true
                        )
                    )
                )
        );
        
        $this->add(
                array(
                    'name' => 'repass-user',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Zend\Filter\StringTrim')
                    ),
                    'validators' => array(
                        array('name' => 'NotEmpty',
                            'options' => array('encoding' => 'UTF-8',
                                'messages' => array(
                                    NotEmpty::IS_EMPTY => 'Please enter Retype Password')),
                            'break_chain_on_failure' => true),
                        array(
                            'name' => 'Zend\Validator\StringLength',
                            'options' => array(
                                'encoding' => 'UTF-8',
                                'min' => 4,
                                'messages' => array(
                                    StringLength::TOO_LONG => 'Retype Password can not be less than 3 characters.'),
                            ),
                            'break_chain_on_failure' => true
                        ),
                        array(
                            'name' => 'Identical',
                            'options' => array(
                                'token' => 'pass-user', // name of first password field
                                'messages'=>array(
                                    \Zend\Validator\Identical::NOT_SAME => "Password do not match."
                                )
                            ),
                        ),
                    )
                )
        );
        
        $this->add(
                array(
                    'name' => 'level-user',
                    'required' => true,
                    'filters' => array(
                        array('name' => 'Zend\Filter\StringTrim')
                    ),
                    'validators' => array(
                        array('name' => 'NotEmpty',
                            'options' => array('encoding' => 'UTF-8',
                                'messages' => array(
                                    NotEmpty::IS_EMPTY => 'Select a user type')),
                            'break_chain_on_failure' => true),
                    )
                )
        );
        
        $this->add(
                array(
                    'name' => 'campaign-user',
                    'required' => false,
                    
                    
                )
        );
    }

}