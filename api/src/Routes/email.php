<?php

use App\Config\ResponseHttp;
use App\Controllers\EmailController;

$params  = explode('/' , $_GET['route']);
$app = new EmailController();

/***********************Rutas*********************/
$app->sendEmailContacto("email/send/");
$app->getEmail("email/get/");
$app->updateEmail("email/update/");

/****************Error 404*****************/
echo json_encode(ResponseHttp::status404()); 