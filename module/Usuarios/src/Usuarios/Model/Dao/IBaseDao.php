<?php

namespace Usuarios\Model\Dao;


interface IBaseDao {

    public function obtenerTodos();

    public function obtenerPorId($id);

    public function buscarPorNombre($nombre);
    
    public function guardar($object);
}

