<?php

use App\Config\ResponseHttp;
use App\Controllers\SubcategoriaController;

$params  = explode('/', $_GET['route']);

$app = new SubcategoriaController();

$app->registerSubcategoria('subcategorias/register/');
$app->getSubcategoria('subcategorias/');
$app->updateSubcategoria('subcategorias/');
$app->deleteSubcategoria("subcategorias/{$params[1]}/");

$app->updateOrder("subcategorias/order/");

echo json_encode(ResponseHttp::status404());
