<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\DB\ConnectionDB;

class EmailModel extends ConnectionDB
{

    final public static function sendEmailContacto($nombre, $telefono, $email, $consulta)
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare('SELECT email_contacto FROM configuraciones WHERE id_configuracion = 1');
            $query->bindParam(':id_configuracion', $id);
            $query->execute();
            $data = $query->fetchAll();
            if ($data) {
                $headers = "MIME-Version: 1.0" . "\r\n";
                $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";


                /* Mensaje al remitente */
                $mensaje_remitente = "
                <html>
                    <head>
                        <title>Mi app</title>
                    </head>
                    <body>
                        <h1>Hola, " . $nombre . "</h1>
                        <p>Te escribimos para informarte que tu consulta ha sido recibida con éxito.</p>
                        <p>Nos pondremos en contacto contigo a la brevedad.</p>
                        <p>Gracias por confiar en nosotros.</p>
                    </body>
                ";


                /* Mensaje al destinatario */
                $mensaje_destinatario = "
                <hm>
                    <head>
                        <title>Te intenta contactar " . $nombre . "</title>
                    </head>
                    <body>
                        <p>Nombre: " . $nombre . "</p>
                        <p>Telefono: " . $telefono . "</p>
                        <p>Email: " . $email . "</p><br>
                        <p>Consulta: " . $consulta . "</p>
                    </body>
                ";

                /* Enviamos el mensaje */
                mail($email, "Consulta en Mi app", $mensaje_remitente, $headers);
                mail($data[0]["email_contacto"], "Te intenta contactar {$nombre}", $mensaje_destinatario, $headers);

                return ResponseHttp::status200("Mensaje enviado con éxito");
            } else {
                return ResponseHttp::status404('Ocurrio un error al enviar el correo');
            }
        } catch (\PDOException $e) {
            error_log("EmailModel::SendEmailContacto -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function getEmail()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare('SELECT email_contacto FROM configuraciones WHERE id_configuracion = 1');
            $query->bindParam(':id_configuracion', $id);
            $query->execute();
            $data = $query->fetchAll();
            if ($data) {
                return ResponseHttp::status200($data[0]);
            } else {
                return ResponseHttp::status404('Ocurrio un error al obtener el correo');
            }
        } catch (\PDOException $e) {
            error_log("EmailModel::GetEmail -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function updateEmail($email)
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare('UPDATE configuraciones SET email_contacto = :email_contacto WHERE id_configuracion = 1');
            $query->bindParam(':email_contacto', $email);
            $query->execute();
            $query->fetchAll();
            if ($query->rowCount() > 0) {
                return ResponseHttp::status200("Correo actualizado con éxito");
            } else {
                return ResponseHttp::status404('Ocurrio un error al actualizar el correo');
            }
        } catch (\PDOException $e) {
            error_log("EmailModel::UpdateEmail -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

}
