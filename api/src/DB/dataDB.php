<?php

use App\DB\ConnectionDB;

$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__,2));
$dotenv->load();

$data = array(
    'user'=> $_ENV['DB_USER'],
    'password'=> $_ENV['DB_PASSWORD'],
    'db'=> $_ENV['DB_DATABASE'],
    'host'=> $_ENV['DB_HOST'],
    'port'=> $_ENV['DB_PORT']
);

$host = 'mysql:host='.$data['host'].';port='.$data['port'].';dbname='.$data['db'];

ConnectionDB::from($host,$data['user'],$data['password']);