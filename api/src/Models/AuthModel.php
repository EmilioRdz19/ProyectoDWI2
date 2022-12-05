<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\DB\ConnectionDB;
use App\DB\Sql;
use Dotenv\Dotenv;


class AuthModel extends ConnectionDB
{

    //Propiedades de la base de datos
    private static $id_usuario;
    private static $nombre;
    private static $usuario;
    private static $password;
    private static $foto;
    private static $id_perfil;
    private static $estado;
    private static $ultimo_login;

    public function __construct(array $data)
    {
        self::$id_usuario = $data['id_usuario'];
        self::$nombre = $data['nombre'];
        self::$usuario = $data['usuario'];
        self::$password = $data['password'];
        self::$foto = $data['foto'];
        self::$id_perfil = $data['id_perfil'];
        self::$estado = $data['estado'];
        self::$ultimo_login = $data['ultimo_login'];
    }

    /************************Metodos Getter**************************/
    final public static function getId_Usuario()
    {
        return self::$id_usuario;
    }
    final public static function getNombre()
    {
        return self::$nombre;
    }
    final public static function getUsuario()
    {
        return self::$usuario;
    }
    final public static function getPassword()
    {
        return self::$password;
    }
    final public static function getFoto()
    {
        return self::$foto;
    }
    final public static function getId_Perfil()
    {
        return self::$id_perfil;
    }
    final public static function getEstado()
    {
        return self::$estado;
    }
    final public static function getUltimo_Login()
    {
        return self::$ultimo_login;
    }



    /**********************************Metodos Setter***********************************/
    final public static function setId_Usuario(string $id_usuario)
    {
        self::$id_usuario = $id_usuario;
    }
    final public static function setNombre(string $nombre)
    {
        self::$nombre = $nombre;
    }
    final public static function setUsuario(string $usuario)
    {
        self::$usuario = $usuario;
    }
    final public static function setPassword(string $password)
    {
        self::$password = $password;
    }
    final public static function setFoto(string $foto)
    {
        self::$foto = $foto;
    }
    final public static function setId_Perfil(string $id_perfil)
    {
        self::$id_perfil = $id_perfil;
    }
    final public static function setEstado(string $estado)
    {
        self::$estado = $estado;
    }
    final public static function setUltimo_Login(string $ultimo_login)
    {
        self::$ultimo_login = $ultimo_login;
    }



    /*********************************************Login******************************************/
    final public static function login($tokenCaptcha)
    {
        try {
            $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
            $dotenv->load();
            $_ENV['HCAPTCHA_SECRET_KEY'];
            $_ENV['HCAPTCHA_SITE_KEY'];
            $VERIFY_URL = "https://hcaptcha.com/siteverify";

            $dataCaptcha = array(
                'secret' => $_ENV['HCAPTCHA_SECRET_KEY'],
                'response' => $tokenCaptcha,
                'sitekey' => $_ENV['HCAPTCHA_SITE_KEY']
            );


            $options = array(
                'http' => array(
                    'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method'  => 'POST',
                    'content' => http_build_query($dataCaptcha)
                )
            );

            $context  = stream_context_create($options);

            $result = file_get_contents($VERIFY_URL, false, $context);

            $result = json_decode($result, true);

            if ($result['success']) {
                $con = self::getConnection();
                $query = $con->prepare("SELECT * FROM usuarios WHERE usuarios.usuario = :usuario AND usuarios.estado = 1 ");
                $query->execute([
                    ':usuario' => self::getUsuario(),
                ]);

                if ($query->rowCount() === 0) {
                    return ResponseHttp::status400('El usuario o contraseña son incorrectos');
                } else {
                    foreach ($query as $res) {
                        if (Security::validatePassword(self::getPassword(), $res['password'])) {
                            $payload = [
                                "usuario" => self::getUsuario(),
                                "id_usuario" => $res['id_usuario'],
                            ];
                            $token = Security::createTokenJwt(Security::secretKey(), $payload);
                            $data = [
                                'usuario' => $res['usuario'],
                                'token' => $token,
                            ];
                            return ResponseHttp::status200($data);
                            exit;
                        } else {
                            return ResponseHttp::status400('El usuario o contraseña son incorrectos');
                        }
                    }
                }
            } else {
                return ResponseHttp::status400('El captcha no es valido');
            }
        } catch (\PDOException $e) {
            error_log("UsuarioModel::Login -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }


    final public static function registrarUsuario()
    {
        if (Sql::exists("SELECT usuario FROM usuarios WHERE usuario = :usuario", ":usuario", self::getUsuario())) {
            return ResponseHttp::status400('Ya existe un usuario con este nombre');
        } else {
            try {
                $con = self::getConnection();
                $query1 = "INSERT INTO usuarios (id_perfil, usuario, password, nombre ) VALUES";
                $query2 = "(:id_perfil,:usuario,:password,:nombre)";
                $query = $con->prepare($query1 . $query2);
                $query->execute([
                    ':usuario' => self::getUsuario(),
                    ':password' => Security::createPassword(self::getPassword()),
                    ':nombre' => self::getNombre(),
                    ':id_perfil' => self::getId_Perfil(),
                ]);
                if ($query->rowCount() > 0) {
                    return ResponseHttp::status200('Usuario registrado exitosamente');
                } else {
                    return ResponseHttp::status500('No se puedo registrar el usuario');
                }
            } catch (\PDOException $e) {
                error_log('UsuarioModel::post -> ' . $e);
                die(json_encode(ResponseHttp::status500()));
            }
        }
    }

    final public static function updatePassword($id,$newPassword,$oldPassword)
    {
        try {
            $con = self::getConnection();
            /* Obtenemos la contraseña en la base de datos y verificamos que sea la misma que la oldPassword */

            $query = $con->prepare("SELECT password FROM usuarios WHERE id_usuario = :id");
            $query->execute([
                ':id' => $id,
            ]);
            $res = $query->fetch();
            if (Security::validatePassword($oldPassword, $res['password'])) {
                $query = $con->prepare("UPDATE usuarios SET password = :password WHERE id_usuario = :id");
                $query->execute([
                    ':password' => Security::createPassword($newPassword),
                    ':id' => $id,
                ]);
                if ($query->rowCount() > 0) {
                    return ResponseHttp::status200('Contraseña actualizada exitosamente');
                } else {
                    return ResponseHttp::status500('No se pudo actualizar la contraseña');
                }
            } else {
                return ResponseHttp::status400('La contraseña actual no es correcta');
            }
        } catch (\PDOException $e) {
            error_log('UsuarioModel::updatePassword -> ' . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }
}
