<?php

namespace App\Config;
use Dotenv\Dotenv;
class ResponseHttp
{

    public static $message = array(
        'status' => '',
        'message' => ''
    );

    final public static function headerHttpPro($method)
    {
        $dotenv = Dotenv::createImmutable(dirname(__DIR__,2));
        $dotenv->load();
        $RUTA = $_ENV['RUTA_FRONTEND'];
        header("Access-Control-Allow-Origin: ${RUTA} ");
        header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
        header("Access-Control-Allow-Credentials: true");
        header('Content-Type: application/json');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        if ($method == "OPTIONS") {
            header('Access-Control-Allow-Origin: *');
            header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
            header("HTTP/1.1 200 OK");
            die();
        }
    }

    public static function status200($res)
    {
        http_response_code(200);
        self::$message['status'] = 'ok';
        self::$message['message'] = $res;
        return self::$message;
    }

    public static function status201(string $res = 'Recurso creado')
    {
        http_response_code(201);
        self::$message['status'] = 'ok';
        self::$message['message'] = $res;
        return self::$message;
    }

    public static function status400(string $res = 'solicitud enviada incompleta o en formato incorrecto')
    {
        http_response_code(400);
        self::$message['status'] = 'error';
        self::$message['message'] = $res;
        return self::$message;
    }

    public static function status401(string $res = 'No tiene privilegios para acceder al recurso solicitado')
    {
        http_response_code(401);
        self::$message['status'] = 'error';
        self::$message['message'] = $res;
        return self::$message;
    }

    public static function status404(string $res = 'Parece que estas perdido por favor verifica la documentación')
    {
        http_response_code(404);
        self::$message['status'] = 'error';
        self::$message['message'] = $res;
        return self::$message;
    }

    public static function status500(string $res = 'Error interno del servidor')
    {
        http_response_code(500);
        self::$message['status'] = 'error';
        self::$message['message'] = $res;
        return self::$message;
    }
}