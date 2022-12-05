<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\Models\ProductoModel;
use Rakit\Validation\Validator;

class ProductoController extends BaseController
{

    final public function registerProducto(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());

            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'id_subcategoria' => 'required|integer',
                'nombre' => 'required|min:3|max:100',
                'marca' => 'required|min:3|max:50',
                'modelo' => 'required|min:3|max:50',
                'descripcion' => 'required|min:3',
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                new ProductoModel($this->getParam());
                echo json_encode(ProductoModel::registerProducto());
            }

            exit;
        }
    }

    final public function cloneProducto(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'id_producto' => 'required|integer',
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                ProductoModel::setIdProducto($this->getParam()['id_producto']);
                echo json_encode(ProductoModel::cloneProducto());
            }
            exit;
        }
    }

    final public function ordenarProducto(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'id_producto' => 'required|integer',
                'id_put_producto' => 'required|integer',
                'orden' => 'required',
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                ProductoModel::setIdProducto($this->getParam()['id_producto']);
                echo json_encode(ProductoModel::ordenarProducto($this->getParam()['orden'], $this->getParam()['id_put_producto']));
            }
            exit;
        }
    }

    final public function getProductos(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            echo json_encode(ProductoModel::getProductos());
            exit;
        }
    }

    final public function getProductoById(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            $id = $this->getAttribute()[1];

            if (!isset($id)) {
                echo json_encode(ResponseHttp::status400('Es requerido el parametro'));
            } else {
                ProductoModel::setIdProducto($id);
                echo json_encode(ProductoModel::getProductoById());
                exit;
            }
        }
    }

    final public function getProductosBySubcategorias(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            $id = $this->getAttribute()[2];

            if (!isset($id)) {
                echo json_encode(ResponseHttp::status400('Es requerido el parametro'));
            } else {
                ProductoModel::setIdSubcategoria($id);
                echo json_encode(ProductoModel::getProductosBySubcategorias());
                exit;
            }
            exit;
        }
    }

    final public function getProductosBySearch(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'search' => 'required',
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                echo json_encode(ProductoModel::getProductosBySearch($this->getParam()['search']));
                exit;
            }
            exit;
        }
    }

    final public function updateProducto(string $endPoint)
    {
        if ($this->getMethod() == 'put' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());

            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'id_producto' => 'required|integer',
                'id_subcategoria' => 'required|integer',
                'nombre' => 'required',
                'marca' => 'required',
                'modelo' => 'required',
                'descripcion' => 'required',
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                ProductoModel::setIdProducto($this->getParam()['id_producto']);
                new ProductoModel($this->getParam());
                echo json_encode(ProductoModel::updateProducto());
            }

            exit;
        }
    }

    final public function deleteProducto(string $endPoint)
    {
        if ($this->getMethod() == 'delete' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());

            $id = $this->getAttribute()[1];
            if (!isset($id)) {
                echo json_encode(ResponseHttp::status400('Es requerido el parametro'));
            } else {
                ProductoModel::setIdProducto($id);
                echo json_encode(ProductoModel::deleteProducto());
                exit;
            }
            exit;
        }
    }

    final public function addOrUpdateFileEs(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam() + $_FILES, [
                'id_producto' => 'required|integer',
                'ficha_tecnica_es' => 'required|uploaded_file:0,5000k,pdf',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                ProductoModel::setIdProducto($this->getParam()['id_producto']);
                echo json_encode(ProductoModel::addOrUpdateFileEs($_FILES));
                exit;
            }
            exit;
        }
    }

    final public function addOrUpdateFileIn(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam() + $_FILES, [
                'id_producto' => 'required|integer',
                'ficha_tecnica_in' => 'required|uploaded_file:0,5000k,pdf',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                ProductoModel::setIdProducto($this->getParam()['id_producto']);
                echo json_encode(ProductoModel::addOrUpdateFileIn($_FILES));
                exit;
            }
            exit;
        }
    }

    final public function deleteFileEs(string $endPoint)
    {
        if ($this->getMethod() == 'delete' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $id = $this->getAttribute()[3];
            if (!isset($id)) {
                echo json_encode(ResponseHttp::status400('Es requerido el parametro'));
            } else {
                ProductoModel::setIdProducto($id);
                echo json_encode(ProductoModel::deleteFileEs());
                exit;
            }
            exit;
        }
    }
    final public function deleteFileIn(string $endPoint)
    {
        if ($this->getMethod() == 'delete' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $id = $this->getAttribute()[3];
            if (!isset($id)) {
                echo json_encode(ResponseHttp::status400('Es requerido el parametro'));
            } else {
                ProductoModel::setIdProducto($id);
                echo json_encode(ProductoModel::deleteFileIn());
                exit;
            }
            exit;
        }
    }

    final public function addProductoRelacionado(string $endPoing)
    {
        if($this->getMethod() == 'post' && $endPoing == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'id_producto' => 'required|numeric',
                'id_producto_relacionado' => 'required|numeric',
            ]);
            if($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                ProductoModel::setIdProducto($this->getParam()['id_producto']);
                echo json_encode(ProductoModel::addProductoRelacionado($this->getParam()['id_producto_relacionado']));
                exit;
            }
            exit;
        }
    }

    final public function deleteProductoRelacionado(string $endPoing)
    {
        if($this->getMethod() == 'delete' && $endPoing == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $id = $this->getAttribute()[2];
            if(!isset($id)) {
                echo json_encode(ResponseHttp::status400('Es requerido el parametro'));
            } else {
                ProductoModel::setIdProducto($id);
                echo json_encode(ProductoModel::deleteProductoRelacionado());
                exit;
            }
            exit;
        }
    }
}
