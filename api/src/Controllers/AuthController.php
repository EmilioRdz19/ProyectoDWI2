<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Models\AuthModel;
use Rakit\Validation\Validator;
use App\Config\Security;

class AuthController extends BaseController
{
    final public function login(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $usuario = strtolower($this->getParam()["usuario"]);
            $password = ($this->getParam()["password"]);
            $tokenCaptcha = $this->getParam()["token"];
            if (empty($usuario) || empty($password)  || empty($tokenCaptcha)) {
                echo json_encode(ResponseHttp::status400('Todos los campos son necesarios'));
            } else {
                AuthModel::setUsuario($usuario);
                AuthModel::setPassword($password);
                echo json_encode(AuthModel::login($tokenCaptcha));
            }
            exit;
        }
    }

    final public function register(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'nombre' => 'required|regex:/^[a-zA-Z0-9_]+$/|min:3|max:20',
                'usuario' => 'required|regex:/^[a-zA-Z0-9_]+$/|min:3|max:20',
                'password' => 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/',
                'id_perfil' => 'required|numeric|min:1',
            ]);

            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                new AuthModel($this->getParam());
                echo json_encode(AuthModel::registrarUsuario());
            }

            exit;
        }
    }

    final public function verify(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $jwtUserData = Security::getDataJwt();

            echo json_encode(ResponseHttp::status200($jwtUserData));

            exit;
        }
    }

    final public function updatePassword(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            $jwtUserData = Security::getDataJwt();
            $validator = new Validator;
            $validation = $validator->validate($this->getParam(), [
                'newPassword' => 'required|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[$@$!%*?&])[A-Za-z\d$@$!%*?&]{8,}/',
                'oldPassword' => 'required',
            ]);
            if ($validation->fails()) {
                $errors = $validation->errors();
                echo json_encode(ResponseHttp::status400($errors->all()[0]));
            } else {
                /* Validamos que la nueva contraseña y la vieja contraseña no sean la misma */
                if ($this->getParam()["newPassword"] == $this->getParam()["oldPassword"]) {
                    echo json_encode(ResponseHttp::status400('La nueva contraseña no puede ser igual a vieja contraseña'));
                } else {
                    echo json_encode(AuthModel::updatePassword($jwtUserData["id_usuario"], $this->getParam()["newPassword"], $this->getParam()["oldPassword"]));
                }
            }
            exit;
        }
    }
}
