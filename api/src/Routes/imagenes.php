<?php

use App\Config\ResponseHttp;
use App\Controllers\ImagenesController;

$params  = explode('/' , $_GET['route']);
$app = new ImagenesController();

$app->subir("imagenes/subir/");
$app->getImagenByIdProducto("imagenes/{$params[1]}/");
$app->updateImagen("imagenes/");
$app->deleteImagen("imagenes/{$params[1]}/");
echo json_encode(ResponseHttp::status404()); 