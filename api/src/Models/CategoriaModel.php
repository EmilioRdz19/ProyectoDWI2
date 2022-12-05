<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\DB\ConnectionDB;

class CategoriaModel extends ConnectionDB
{

    //Propiedades de la base de datos
    private static $id_categoria;
    private static $nombre;

    public function __construct(array $data)
    {
        self::$nombre = $data['nombre'];
    }

    /************************Metodos Getter**************************/
    public static function getIdCategoria()
    {
        return self::$id_categoria;
    }
    public static function getNombre()
    {
        return self::$nombre;
    }

    /**********************************Metodos Setter***********************************/
    public static function setIdCategoria($id_categoria)
    {
        self::$id_categoria = $id_categoria;
    }
    public static function setNombre($nombre)
    {
        self::$nombre = $nombre;
    }


    final public static function registerCategoria()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare('INSERT INTO categorias (nombre) VALUES (:nombre)');
            $query->execute([
                ':nombre' => self::getNombre(),
            ]);
            if ($query->rowCount() > 0) {
                return ResponseHttp::status200('Categoria registrada con exito');
            } else {
                return ResponseHttp::status500('No se pudo registrar la categoria');
            }
        } catch (\PDOException $e) {
            error_log("::registerCategoria -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function getCategorias()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare('SELECT * FROM categorias WHERE estado = 1');
            $query->execute();
            $categorias = $query->fetchAll(\PDO::FETCH_ASSOC);
            //Si no hay categorias registradas
            if (count($categorias) == 0) {
                return ResponseHttp::status200('No hay categorias registradas');
            } else {
                foreach ($categorias as $key => $categoria) {
                    $query = $con->prepare('SELECT * FROM subcategorias WHERE id_categoria = :id_categoria AND estado = 1');
                    $query->execute([
                        ':id_categoria' => $categoria['id_categoria'],
                    ]);
                    $categorias[$key]['subcategorias'] = $query->fetchAll(\PDO::FETCH_ASSOC);
                }
                return ResponseHttp::status200($categorias);
            }
        } catch (\PDOException $e) {
            error_log("CategoriaModel::getCategorias -> " . $e);
            die(json_encode(ResponseHttp::status500('No se pueden obtener los datos')));
        }
    }

    final public static function updateCategoria()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("UPDATE categorias SET nombre = :nombre WHERE id_categoria = :id_categoria");
            $query->execute([
                ':nombre' => self::getNombre(),
                ':id_categoria' => self::getIdCategoria(),
            ]);
            if ($query->rowCount() > 0) {
                return ResponseHttp::status200('Categoria actualizada con exito');
            } else {
                return ResponseHttp::status500('No se puede actualizar la categoria');
            }

            return true;
        } catch (\PDOException $e) {
            error_log("CategoriaModel::updateCategoria -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function deleteCategoria()
    {
        try {
            $con = self::getConnection();
            //Comprobamos que no existan subcategorias activas en la categoria a eliminar
            $query = $con->prepare('SELECT * FROM subcategorias WHERE id_categoria = :id_categoria AND estado = 1');
            $query->execute([
                ':id_categoria' => self::getIdCategoria(),
            ]);
            $subcategorias = $query->fetchAll(\PDO::FETCH_ASSOC);
            if (count($subcategorias) > 0) {
                return ResponseHttp::status500('No se puede eliminar la categoria, existen subcategorias activas');
            } else {

                $query = $con->prepare("UPDATE categorias SET estado = 0 WHERE id_categoria = :id_categoria AND estado = 1");
                $query->execute([
                    ':id_categoria' => self::getIdCategoria(),
                ]);
                if ($query->rowCount() > 0) {
                    return ResponseHttp::status200('Categoria eliminada con exito');
                } else {
                    return ResponseHttp::status404('No se pudo eliminar la categoria');
                }
            }
        } catch (\PDOException $e) {
            error_log("CategoriaModel::deleteCategoria -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }
}
