<?php

use App\Config\ResponseHttp;
use App\Controllers\CategoriaController;

$params  = explode('/', $_GET['route']);

$app = new CategoriaController();

$app->registerCategoria('categorias/register/');
$app->getCategorias('categorias/');
$app->updateCategoria('categorias/');
$app->deleteCategoria("categorias/{$params[1]}/");

echo json_encode(ResponseHttp::status404());
