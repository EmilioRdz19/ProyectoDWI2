<?php

use App\Config\ResponseHttp;
use App\Controllers\AuthController;

$params  = explode('/' , $_GET['route']);
$app = new AuthController();

/***********************Rutas*********************/
$app->login("auth/login/");
$app->register("auth/register/");
$app->verify("auth/verify/");
$app->updatePassword("auth/password/");
/****************Error 404*****************/
echo json_encode(ResponseHttp::status404()); 