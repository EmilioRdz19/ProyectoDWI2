<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\DB\ConnectionDB;

class SubcategoriaModel extends ConnectionDB
{
    final public static function registerSubcategoria($id_categoria, $nombre, $descripcion)
    {
        try {
            //Buscamos que exista la categoria donde se registrara la subcategoria
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM categorias WHERE id_categoria = :id_categoria AND estado = 1");
            $query->execute([
                ':id_categoria' => $id_categoria,
            ]);

            /* Buscamos en subcategorias la ultima posicion para agregarlo ahi */
            $query = $con->prepare("SELECT MAX(posicion) AS posicion FROM subcategorias");
            $query->execute();
            $posicion = $query->fetchAll(\PDO::FETCH_ASSOC)[0]['posicion'];
            $posicion = $posicion + 1;
            if ($query->rowCount() > 0) {
                $query = $con->prepare("INSERT INTO subcategorias (id_categoria, nombre , descripcion, posicion) VALUES (:id_categoria, :nombre, :descripcion, :posicion)");
                $query->execute([
                    ':id_categoria' => $id_categoria,
                    ':nombre' => $nombre,
                    ':descripcion' => $descripcion,
                    ':posicion' => $posicion
                ]);
                if ($query->rowCount() > 0) {
                    return ResponseHttp::status200('Subcategoria registrada con exito');
                } else {
                    return ResponseHttp::status500('No se pudo registrar la subcategoria');
                }
            } else {
                return ResponseHttp::status404('No se encontro la categoria');
            }
        } catch (\PDOException $e) {
            error_log("SubcategoriaModel::registerSubcategoria -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function getSubcategorias()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM subcategorias WHERE estado = 1");
            $query->execute();
            if ($query->rowCount() > 0) {
                $subcategorias = $query->fetchAll();
                foreach ($subcategorias as $key => $subcategoria) {
                    $query = $con->prepare("SELECT COUNT(*) AS cantidad FROM productos WHERE id_subcategoria = :id_subcategoria AND estado = 1");
                    $query->execute([
                        ':id_subcategoria' => $subcategoria['id_subcategoria'],
                    ]);
                    $subcategorias[$key]['cantidad_productos'] = $query->fetch()['cantidad'];
                }

                foreach ($subcategorias as $key => $subcategoria) {
                    $query = $con->prepare("SELECT nombre FROM categorias WHERE id_categoria = :id_categoria");
                    $query->execute([
                        ':id_categoria' => $subcategoria['id_categoria'],
                    ]);
                    $subcategorias[$key]['categoria_nombre'] = $query->fetch()['nombre'];
                }

                return ResponseHttp::status200($subcategorias);
            } else {
                return ResponseHttp::status404('No se encontraron subcategorias');
            }
        } catch (\PDOException $e) {
            error_log("SubcategoriaModel::getSubcategorias -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function updateSubcategoria($id_categoria, $id_subcategoria, $nombre, $descripcion)
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM categorias WHERE id_categoria = :id_categoria AND estado = 1");
            $query->execute([
                ':id_categoria' => $id_categoria,
            ]);
            if ($query->rowCount() > 0) {
                $query = $con->prepare("UPDATE subcategorias SET id_categoria = :id_categoria, nombre = :nombre, descripcion = :descripcion WHERE id_subcategoria = :id_subcategoria");
                $query->execute([
                    ':id_categoria' => $id_categoria,
                    ':nombre' => $nombre,
                    ':id_subcategoria' => $id_subcategoria,
                    ':descripcion' => $descripcion
                ]);
                if ($query->rowCount() > 0) {
                    return ResponseHttp::status200('Subcategoria actualizada con exito');
                } else {
                    return ResponseHttp::status500('No se pudo actualizar la subcategoria');
                }
            } else {
                return ResponseHttp::status404('No se encontro la categoria');
            }
        } catch (\PDOException $e) {
            error_log("SubcategoriaModel::updateSubcategorias -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function deleteSubcategoria($id_subcategoria)
    {
        try {
            $con = self::getConnection();

            $query = $con->prepare("SELECT COUNT(*) AS cantidad FROM productos WHERE id_subcategoria = :id_subcategoria AND estado = 1");
            $query->execute([
                ':id_subcategoria' => $id_subcategoria,
            ]);
            if ($query->fetch()['cantidad'] > 0) {
                return ResponseHttp::status500('No se puede eliminar la subcategoria porque tiene productos asociados');
            } else {
                $query = $con->prepare("UPDATE subcategorias SET estado = 0 WHERE id_subcategoria = :id_subcategoria");
                $query->execute([
                    ':id_subcategoria' => $id_subcategoria,
                ]);
                if ($query->rowCount() > 0) {
                    return ResponseHttp::status200('Subcategoria eliminada con exito');
                } else {
                    return ResponseHttp::status500('No se pudo eliminar la subcategoria');
                }
            }
        } catch (\PDOException $e) {
            error_log("SubcategoriaModel::deleteSubcategoria -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function updateOrder($data)
    {
        try {
            $con = self::getConnection();
            // Data es una array ordenado con las nuevas posiciones
            foreach ($data as $key => $value) {
                $query = $con->prepare("UPDATE subcategorias SET posicion = :posicion WHERE id_subcategoria = :id_subcategoria");
                $query->execute([
                    ':posicion' => $key,
                    ':id_subcategoria' => $value['_id'],
                ]);
            }
            return ResponseHttp::status200("Actualizado con exito");
        } catch (\PDOException $e) {
            error_log("SubcategoriaModel::updateOrder -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        } catch (\PDOException $e) {
            error_log("SubcategoriaModel::updateOrder -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }
}
