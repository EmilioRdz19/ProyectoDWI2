<?php

use App\Config\ResponseHttp;
use App\Controllers\ProductoController;

$params  = explode('/' ,$_GET['route']);

$app = new ProductoController();

$app->registerProducto('productos/register/');
$app->getProductos('productos/');
$app->getProductoById("productos/{$params[1]}/");
$app->getProductosBySubcategorias("productos/subcategoria/{$params[2]}/");
$app->updateProducto('productos/');
$app->deleteProducto("productos/{$params[1]}/");
$app->getProductosBySearch("productos/search/");
$app->cloneProducto("productos/clone/");
$app->ordenarProducto("productos/ordenar/");

$app->addOrUpdateFileEs("productos/ficha_tecnica/es/");
$app->addOrUpdateFileIn("productos/ficha_tecnica/in/");
$app->deleteFileEs("productos/ficha_tecnica/es/{$params[3]}/");
$app->deleteFileIn("productos/ficha_tecnica/in/{$params[3]}/");

$app->addProductoRelacionado("productos/relacionados/");
$app->deleteProductoRelacionado("productos/relacionados/{$params[2]}/");

/****************Error 404*****************/
echo json_encode(ResponseHttp::status404());