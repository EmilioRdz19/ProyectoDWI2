<?php

use App\Config\ErrorLog;
use App\Config\ResponseHttp;

require './vendor/autoload.php';



header("Content-type: application/json; charset=utf-8");
//Cors para permitir el acceso a la api desde cualquier origen, ////////////////solo para pruebas///////////////////////////
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Allow: GET, POST, OPTIONS, PUT, DELETE");
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    die();
}


ErrorLog::activateErrorLog();       //Activamos el log de errores

if (isset($_GET['route'])) {

    $params = explode('/', $_GET['route']);
    $list = [
        'auth',
        'email',
        'productos',
        'categorias',
        'subcategorias',
        'imagenes',
    ];

    $file = './src/Routes/' . $params[0] . '.php';

    if (!in_array($params[0], $list)) {
        echo json_encode(ResponseHttp::status400());
        exit;
    }

    if (is_readable($file)) {
        require $file;
        exit;
    } else {
        echo json_encode(ResponseHttp::status500('El archivo de la ruta no esta creado'));
    }
} else {
    echo json_encode(ResponseHttp::status500());
    exit;
}
