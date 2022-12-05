<?php

namespace App\Controllers;

use App\Config\ResponseHttp;
use App\Models\EmailModel;
use App\Config\Security;

class EmailController extends BaseController
{
    final public function sendEmailContacto(string $endPoint)
    {
        if ($this->getMethod() == 'post' && $endPoint == $this->getRoute()) {
            $nombre = ($this->getParam()["nombre"]);
            $telefono = $this->getParam()["telefono"];
            $email = $this->getParam()["email"];
            $consulta = $this->getParam()["consulta"];

            /* Validamos que se nos envien todos los campos */
            if (empty($nombre) || empty($telefono) || empty($email) || empty($consulta)) {
                echo json_encode(ResponseHttp::status400("Faltan campos por llenar"));
            } else {
                echo json_encode(EmailModel::sendEmailContacto($nombre, $telefono, $email, $consulta));
            }
            exit;
        }
    }

    final public function getEmail(string $endPoint)
    {
        if ($this->getMethod() == 'get' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            echo json_encode(EmailModel::getEmail());
            exit;
        }
    }

    final public function updateEmail(string $endPoint)
    {
        if ($this->getMethod() == 'put' && $endPoint == $this->getRoute()) {
            Security::validateTokenJwt(Security::secretKey());
            if(!empty($this->getParam()["email"])) {
                echo json_encode(EmailModel::updateEmail($this->getParam()["email"]));
            } else {
                echo json_encode(ResponseHttp::status400("Faltan campos por llenar"));
            }
            exit;
        }
    }

}
