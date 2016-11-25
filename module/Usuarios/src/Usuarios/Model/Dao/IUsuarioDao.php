<?php

namespace Usuarios\Model\Dao;


interface IUsuarioDao {

    public function obtenerTodos();

    public function obtenerPorId($id);

    public function buscarPorNombre($nombre);

    public function obtenerCuenta($email, $clave);
}

