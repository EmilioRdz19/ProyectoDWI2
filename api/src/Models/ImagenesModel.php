<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\Config\Security;
use App\DB\ConnectionDB;
use App\DB\Sql;
use App\Config\UploadImagen;

class ImagenesModel extends ConnectionDB
{

    //Propiedades de la base de datos
    private static $id_imagen;
    private static $id_producto;
    private static $imagen;
    private static $file;

    public function __construct(array $data, $file)
    {
        self::$id_producto = $data['id_producto'];
        self::$imagen = $file;
    }

    /************************Metodos Getter**************************/
    public static function getIdProducto()
    {
        return self::$id_producto;
    }
    public static function getIdImagen()
    {
        return self::$id_imagen;
    }
    public static function getImagen()
    {
        return self::$imagen;
    }

    /**********************************Metodos Setter***********************************/
    public static function setIdProducto($id_producto)
    {
        self::$id_producto = $id_producto;
    }
    public static function setIdImagen($id_imagen)
    {
        self::$id_imagen = $id_imagen;
    }
    public static function setImagen($imagen)
    {
        self::$imagen = $imagen;
    }
    /////////////////////////////////////////////////////////////////////////////////////////

    final public static function subirImagen()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare('SELECT * FROM productos WHERE id_producto = :id_producto AND estado = 1');
            $query->bindParam(':id_producto', self::getIdProducto());
            $query->execute();
            $producto = $query->fetch();
            if ($producto) {
                $respuestaImg = UploadImagen::subidaImagenEvento(self::getImagen(), $producto);
                $query = $con->prepare('INSERT INTO imagenes (id_producto, url) VALUES (:id_producto, :url)');
                $query->execute([
                    ':id_producto' => self::getIdProducto(),
                    ':url' => $respuestaImg['path']
                ]);

                if ($query->rowCount() > 0) {
                    return ResponseHttp::status200('Imagen subida con exito');
                } else {
                    return ResponseHttp::status500('No se pudo subir la imagen');
                }
            } else {
                return ResponseHttp::status404('No existe el producto');
            }
        } catch (\PDOException $e) {
            error_log("ImagenesModel::SubirImagen -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function getImagenByIdProducto($id)
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare('SELECT * FROM imagenes WHERE id_producto = :id_producto');
            $query->bindParam(':id_producto', $id);
            $query->execute();
            $imagenes = $query->fetchAll();
            if ($imagenes) {
                return ResponseHttp::status200($imagenes);
            } else {
                return ResponseHttp::status404('El producto aun no tiene imagenes');
            }
        } catch (\PDOException $e) {
            error_log("ImagenesModel::getImagenByIdProducto -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function updateImagen($id_imagen, $id_producto, $imagen)
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare('SELECT * FROM productos WHERE id_producto = :id_producto AND estado = 1');
            $query->bindParam(':id_producto', $id_producto);
            $query->execute();
            $producto = $query->fetch();
            if ($producto) {
                $query = $con->prepare('SELECT * FROM imagenes WHERE id_producto = :id_producto AND id_imagen = :id_imagen');
                $query->execute([
                    ':id_producto' => $id_producto,
                    ':id_imagen' => $id_imagen
                ]);
                $response_imagen = $query->fetch();
                if ($response_imagen) {
                    $respuestaImg = UploadImagen::updateImagen($imagen, $producto, $response_imagen);

                    $query = $con->prepare('UPDATE imagenes SET url = :url WHERE id_imagen = :id_imagen');
                    $query->execute([
                        ':id_imagen' => $id_imagen,
                        ':url' => $respuestaImg['path']
                    ]);

                    if ($query->rowCount() > 0) {
                        return ResponseHttp::status200('Imagen actualizada con exito');
                    } else {
                        return ResponseHttp::status500('No se pudo actualizar la imagen');
                    }
                } else {
                    return ResponseHttp::status404('No existe la imagen');
                }
            } else {
                return ResponseHttp::status404('No existe el producto');
            }
        } catch (\PDOException $e) {
            error_log("ImagenesModel::updateImagen -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function deleteImagen($id_imagen)
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare('SELECT * FROM imagenes WHERE id_imagen = :id_imagen');
            $query->bindParam(':id_imagen', $id_imagen);
            $query->execute();
            $imagen = $query->fetch();
            if ($imagen) {
                $response_delete = UploadImagen::eliminar($imagen['url']);
                if ($response_delete) {
                    $query = $con->prepare('DELETE FROM imagenes WHERE id_imagen = :id_imagen');
                    $query->bindParam(':id_imagen', $id_imagen);
                    $query->execute();
                    if ($query->rowCount() > 0) {
                        return ResponseHttp::status200('Imagen eliminada con exito');
                    } else {
                        return ResponseHttp::status500('No se pudo eliminar la imagen');
                    }
                } else {
                    return ResponseHttp::status500('No se pudo eliminar la imagen');
                }
            } else {
                return ResponseHttp::status200('No existe la imagen');
            }
        } catch (\PDOException $e) {
            error_log("ImagenesModel::deleteImagen -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }
}
