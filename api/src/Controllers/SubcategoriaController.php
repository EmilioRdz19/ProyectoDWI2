<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Models\SubcategoriaModel;
use Rakit\Validation\Validator;


class SubcategoriaController extends BaseController
{
    final public function registerSubcategoria(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'id_categoria' => 'required|integer',
                'nombre' => 'required|min:3|max:80',
                'descripcion' => 'required'
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                $id_categoria = $this->getParam()['id_categoria'];
                $nombre = $this->getParam()['nombre'];
                $descripcion = $this->getParam()['descripcion'];
                echo json_encode(SubcategoriaModel::registerSubcategoria($id_categoria, $nombre, $descripcion));
            }

            exit;
        }
    }

    final public function getSubcategoria(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            echo json_encode(SubcategoriaModel::getSubcategorias());
            exit;
        }
    }

    final public function updateSubcategoria(string $endPoint)
    {
        if ($this->getMethod() == 'put' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'id_subcategoria' => 'required|integer',
                'id_categoria' => 'required|integer',
                'nombre' => 'required|min:3|max:200',
                'descripcion' => 'required'
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                $id_subcategoria = $this->getParam()['id_subcategoria'];
                $id_categoria = $this->getParam()['id_categoria'];
                $nombre = $this->getParam()['nombre'];
                $descripcion = $this->getParam()['descripcion'];
                echo json_encode(SubcategoriaModel::updateSubcategoria($id_categoria, $id_subcategoria, $nombre, $descripcion));
            }
            exit;
        }
    }

    final public function deleteSubcategoria(string $endPoint)
    {
        if ($this->getMethod() == 'delete' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $id = $this->getAttribute()[1];
            if (!isset($id)) {
                echo json_encode(ResponseHttp::status400('Es requerido el parametro'));
            } else {
                echo json_encode(SubcategoriaModel::deleteSubcategoria($id));
            }
            exit;
        }
    }

    final public function updateOrder(string $endPoint)
    {
        if ($this->getMethod() == 'put' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'data' => 'required',
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                $data = $this->getParam()['data'];
                echo json_encode(SubcategoriaModel::updateOrder($data));
            }
            exit;
        }
    }

}
