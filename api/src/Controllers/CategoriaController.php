<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Models\CategoriaModel;
use Rakit\Validation\Validator;


class CategoriaController extends BaseController
{

    final public function registerCategoria(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'nombre' => 'required|min:3|max:30'
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                new CategoriaModel($this->getParam());
                echo json_encode(CategoriaModel::registerCategoria());
            }

            exit;
        }
    }

    final public function getCategorias(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            echo json_encode(CategoriaModel::getCategorias());
            exit;
        } 
    }

    final public function updateCategoria(string $endPoint)
    {
        if ($this->getMethod() == 'put' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'id_categoria' => 'required|integer',
                'nombre' => 'required|min:3|max:200'
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                CategoriaModel::setIdCategoria($this->getParam()['id_categoria']);
                new CategoriaModel($this->getParam());
                echo json_encode(CategoriaModel::updateCategoria());
            }
            exit;
        }
    }

    final public function deleteCategoria(string $endPoint)
    {
        if ($this->getMethod() == 'delete' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $id = $this->getAttribute()[1];
            if (!isset($id)) {
                echo json_encode(ResponseHttp::status400('Es requerido el parametro'));
            } else {
                CategoriaModel::setIdCategoria($id);
                echo json_encode(CategoriaModel::deleteCategoria());
                exit;
            }
            exit;
        }
    }
}
