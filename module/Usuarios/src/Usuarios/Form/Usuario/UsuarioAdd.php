<?php

namespace Usuarios\Form\Usuario;

use Zend\Form\Form;
use Zend\Form\Element;
use Usuarios\Model\Dao\UsuarioDao;

class UsuarioAdd extends Form {

    public function __construct($nombre = "", $tableGateway = "",$adapter="") {

        parent::__construct();

        $usuario = new UsuarioDao($tableGateway,$adapter);

        $tipos = $usuario->getTipos();
        
        $this->add(array(
            'name' => 'email-user',
            'atributes' => array(
                'type' => 'text'),
        ));
        
        $this->add(array(
            'name' => 'id-callcenter-user',
            'atributes' => array(
                'type' => 'text'),
        ));
        
        $this->add(array(
            'name' => 'first-name-user',
            'atributes' => array(
                'type' => 'text'),
        ));
        
        $this->add(array(
            'name' => 'last-name-user',
            'atributes' => array(
                'type' => 'text'),
        ));
        
        $this->add(array(
            'name' => 'pass-user',
            'type' => 'Zend\Form\Element\Password'
        ));
        
        $this->add(array(
            'name' => 'repass-user',
            'type' => 'Zend\Form\Element\Password'
        ));

        $selectLevel = new Element\Select('level-user');
        $selectLevel->setValueOptions($tipos);
        $this->add($selectLevel);
        
        $file = new Element\File('avatar-user');
        $file->setAttribute('id', 'id-input-file-3')
                ->setAttribute('multiple', true);
        $this->add($file);


        $this->add(array(
            'name' => 'send-user',
            'attributes' => array(
                'type' => 'button',
                'class' => 'btn btn-info'),
        ));

        $this->add(array(
            'name' => 'reset-user',
            'attributes' => array(
                'type' => 'reset',
                'class' => 'btn',
                'onclick' => ''),
        ));
    }

}
