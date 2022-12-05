<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Models\ImagenesModel;
use Rakit\Validation\Validator;
use App\Config\Security;

class ImagenesController extends BaseController
{
    final public function subir(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam() + $_FILES, [
                'id_producto' => 'required|integer',
                'imagen' => 'required|uploaded_file:0,5000k,png,jpeg',
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                new ImagenesModel($this->getParam(), $_FILES);
                echo json_encode(ImagenesModel::subirImagen());
            }

            exit;

        }
    }

    final public function getImagenByIdProducto(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            $id = $this->getAttribute()[1];
            
            if(!empty($id)){
                echo json_encode(ImagenesModel::getImagenByIdProducto($id));
            }else{
                echo json_encode(ResponseHttp::status400('El id del producto es requerido'));
            }
            exit;
        }
    }

    final public function updateImagen(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam() + $_FILES, [
                'id_producto' => 'required|integer',
                'id_imagen' => 'required|integer',
                'imagen' => 'required|uploaded_file:0,5000k,png,jpeg',
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                $id_producto = $this->getParam()['id_producto'];
                $id_imagen = $this->getParam()['id_imagen'];
                echo json_encode(ImagenesModel::updateImagen($id_imagen, $id_producto, $_FILES));
            }
            exit;
        }
    }

    final public function deleteImagen(string $endPoint)
    {
        if ($this->getMethod() == 'delete' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $id = $this->getAttribute()[1];
            if (!isset($id)) {
                echo json_encode(ResponseHttp::status400('Es requerido el parametro'));
            } else {
                echo json_encode(ImagenesModel::deleteImagen($id));
                exit;
            }
            exit;
        }
    }

}
