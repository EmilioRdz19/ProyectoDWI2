<?php

namespace App\Models;

use App\Config\ResponseHttp;
use App\DB\ConnectionDB;
use App\Config\UploadFile;

class ProductoModel extends ConnectionDB
{

    //Propiedades de la base de datos
    private static $id_producto;
    private static $id_subcategoria;
    private static $marca;
    private static $modelo;
    private static $descripcion;
    private static $caracteristicas;
    private static $imagen;
    private static $nombre;
    private static $url_formulario;

    public function __construct(array $data)
    {
        self::$id_subcategoria = $data['id_subcategoria'];
        self::$marca = $data['marca'];
        self::$modelo = $data['modelo'];
        self::$descripcion = $data['descripcion'];
        self::$caracteristicas = $data['caracteristicas'];
        self::$nombre = $data['nombre'];
        self::$url_formulario = $data['url_formulario'];
    }

    /************************Metodos Getter**************************/
    public static function getIdProducto()
    {
        return self::$id_producto;
    }
    public static function getIdSubcategoria()
    {
        return self::$id_subcategoria;
    }
    public static function getMarca()
    {
        return self::$marca;
    }
    public static function getModelo()
    {
        return self::$modelo;
    }
    public static function getDescripcion()
    {
        return self::$descripcion;
    }
    public static function getCaracteristicas()
    {
        return self::$caracteristicas;
    }
    public static function getImagen()
    {
        return self::$imagen;
    }
    public static function getNombre()
    {
        return self::$nombre;
    }
    public static function getUrlFormulario()
    {
        return self::$url_formulario;
    }

    /**********************************Metodos Setter***********************************/
    public static function setIdProducto($id_producto)
    {
        self::$id_producto = $id_producto;
    }
    public static function setIdSubcategoria($id_subcategoria)
    {
        self::$id_subcategoria = $id_subcategoria;
    }
    public static function setMarca($marca)
    {
        self::$marca = $marca;
    }
    public static function setModelo($modelo)
    {
        self::$modelo = $modelo;
    }
    public static function setDescripcion($descripcion)
    {
        self::$descripcion = $descripcion;
    }
    public static function setCaracteristicas($caracteristicas)
    {
        self::$caracteristicas = $caracteristicas;
    }
    public static function setImagen($imagen)
    {
        self::$imagen = $imagen;
    }
    public static function setNombre($nombre)
    {
        self::$nombre = $nombre;
    }
    public static function setUrlFormulario($url_formulario)
    {
        self::$url_formulario = $url_formulario;
    }

    /********************************************************************************** */


    final public static function registerProducto()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare('INSERT INTO productos(id_subcategoria, marca,modelo,descripcion,caracteristicas,nombre,url_formulario) VALUES (:id_subcategoria,:marca,:modelo,:descripcion,:caracteristicas,:nombre,:url_formulario)');
            $query->execute([
                ':id_subcategoria' => self::getIdSubcategoria(),
                ':marca' => self::getMarca(),
                ':modelo' => self::getModelo(),
                ':descripcion' => self::getDescripcion(),
                ':caracteristicas' => self::getCaracteristicas(),
                ':nombre' => self::getNombre(),
                ':url_formulario' => self::getUrlFormulario()
            ]);
            //Una vez registrado el producto devolvemos el id del producto
            if ($query->rowCount() > 0) {
                $producto["id_producto"] = $con->lastInsertId();
                $producto["message"] = "Producto registrado correctamente";
                return ResponseHttp::status200($producto);
            } else {
                return ResponseHttp::status500('No se puede registrar el producto');
            }
        } catch (\PDOException $e) {
            error_log("ProductoModel::registerProducto -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function cloneProducto()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare('SELECT * FROM productos WHERE id_producto = :id_producto AND estado = 1');
            $query->execute([
                ':id_producto' => self::getIdProducto()
            ]);
            $producto = $query->fetch();
            $query = $con->prepare('INSERT INTO productos(id_subcategoria, marca,modelo,descripcion,caracteristicas,nombre,url_formulario) VALUES (:id_subcategoria,:marca,:modelo,:descripcion,:caracteristicas,:nombre,:url_formulario)');
            $query->execute([
                ':id_subcategoria' => $producto['id_subcategoria'],
                ':marca' => $producto['marca'],
                ':modelo' => $producto['modelo'],
                ':descripcion' => $producto['descripcion'],
                ':caracteristicas' => $producto['caracteristicas'],
                ':nombre' => $producto['nombre'],
                ':url_formulario' => $producto['url_formulario']
            ]);
            if ($query->rowCount() > 0) {
                $producto["id_producto"] = $con->lastInsertId();
                $producto["message"] = "Producto clonado correctamente";
                return ResponseHttp::status200($producto);
            } else {
                return ResponseHttp::status500('No se puede clonar el producto');
            }
        } catch (\PDOException $e) {
            error_log("ProductoModel::CloneProducto -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function ordenarProducto($orden, $id_put_producto)
    {
        try {
            $con = self::getConnection();
            /* Buscamos el producto a ordenar */
            $query = $con->prepare('SELECT * FROM productos WHERE id_producto = :id_producto AND estado = 1');
            $query->execute([
                ':id_producto' => self::getIdProducto()
            ]);
            $producto = $query->fetch();
            if(!$producto)return ResponseHttp::status404('No se puede ordenar el producto');

            /* Buscamos el producto donde lo pondremos after o before id_put_producto */
            $query = $con->prepare('SELECT * FROM productos WHERE id_producto = :id_producto AND estado = 1');
            $query->execute([
                ':id_producto' => $id_put_producto
            ]);
            $productoPut = $query->fetch();
            if(!$productoPut)return ResponseHttp::status404('No se puede ordenar el producto');

            /* Obtenemos la lista de los productos dentro de las subcategorias de producto */
            $query = $con->prepare('SELECT * FROM productos WHERE id_subcategoria = :id_subcategoria AND estado = 1 ORDER BY posicion ASC');
            $query->execute([
                ':id_subcategoria' => $producto['id_subcategoria']
            ]);
            $productosList = $query->fetchAll();
            if(!$productosList)return ResponseHttp::status404('No se puede ordenar el producto');

            $posicionProductoPut = $productoPut['posicion'];
            $posicionProductoPut = $orden == "after" ? $posicionProductoPut + 1 : $posicionProductoPut;
            $query = $con->prepare('UPDATE productos SET posicion = :posicion WHERE id_producto = :id_producto');
            $query->execute([
                ':posicion' => $posicionProductoPut,
                ':id_producto' => $producto['id_producto']
            ]);
            /* Reordenamos los demas productos despues del producto */
            foreach ($productosList as $key => $productoList) {
                if ($productoList['id_producto'] != $producto['id_producto']) {
                    if ($productoList['posicion'] >= $posicionProductoPut) {
                        $query = $con->prepare('UPDATE productos SET posicion = :posicion WHERE id_producto = :id_producto');
                        $query->execute([
                            ':posicion' => $productoList['posicion'] + 1,
                            ':id_producto' => $productoList['id_producto']
                        ]);
                    }
                }
            }
            return ResponseHttp::status200('Producto ordenado correctamente');

        } catch (\PDOException $e) {
            error_log("ProductoModel::ordenarProducto -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function getProductos()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM productos WHERE estado = 1");
            $query->execute();
            $productos = $query->fetchAll(\PDO::FETCH_ASSOC);
            //Si el array no esta vacio
            if (count($productos) > 0) {
                foreach ($productos as $key => $producto) {
                    $query = $con->prepare("SELECT * FROM imagenes WHERE id_producto = :id_producto");
                    $query->execute([
                        ':id_producto' => $producto['id_producto']
                    ]);
                    $productos[$key]['imagenes'] = $query->fetchAll(\PDO::FETCH_ASSOC);
                    //Si no hay imagenes le pondremos una imagen por defecto
                    if (count($productos[$key]['imagenes']) == 0) {
                        $productos[$key]['imagenes'][0]['url'] = 'public/default/anonymous.png';
                        $productos[$key]['imagenes'][0]['default'] = true;
                    }
                    $query = $con->prepare("SELECT nombre, id_categoria FROM subcategorias WHERE id_subcategoria = :id_subcategoria");
                    $query->execute([
                        ':id_subcategoria' => $producto['id_subcategoria']
                    ]);
                    $subcategoria = $query->fetch(\PDO::FETCH_ASSOC);
                    $productos[$key]['nombre_subcategoria'] = $subcategoria['nombre'];

                    /*----------- Productos relacionados ------------------ */
                    $query = $con->prepare("SELECT p.id_producto, p.nombre, pr.id_productos_relacionados FROM productos p INNER JOIN productos_relacionados pr ON p.id_producto = pr.id_producto_relacionado WHERE pr.id_producto = :id_producto");
                    $query->execute([
                        ':id_producto' => $producto['id_producto']
                    ]);
                    $productos[$key]['productos_relacionados'] = $query->fetchAll(\PDO::FETCH_ASSOC);
                    foreach ($productos[$key]['productos_relacionados'] as $key2 => $producto_relacionado) {
                        $query = $con->prepare("SELECT * FROM imagenes WHERE id_producto = :id_producto");
                        $query->execute([
                            ':id_producto' => $producto_relacionado['id_producto']
                        ]);
                        $productos[$key]['productos_relacionados'][$key2]['imagenes'] = $query->fetchAll(\PDO::FETCH_ASSOC);
                        //Si no hay imagenes le pondremos una imagen por defecto
                        if (count($productos[$key]['productos_relacionados'][$key2]['imagenes']) == 0) {
                            $productos[$key]['productos_relacionados'][$key2]['imagenes'][0]['url'] = 'public/default/anonymous.png';
                            $productos[$key]['productos_relacionados'][$key2]['imagenes'][0]['default'] = true;
                        }
                    }
                }
                return ResponseHttp::status200($productos);
            } else {
                return ResponseHttp::status404('No se encontraron productos');
            }
        } catch (\PDOException $e) {
            error_log("ProductoModel::getProductos -> " . $e);
            die(json_encode(ResponseHttp::status500('No se pueden obtener los productos')));
        }
    }

    final public static function getProductoById()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM productos WHERE id_producto = :id_producto AND estado = 1");
            $query->execute([
                ':id_producto' => self::getIdProducto()
            ]);
            $productos = $query->fetchAll(\PDO::FETCH_ASSOC);
            if (count($productos) > 0) {
                foreach ($productos as $key => $producto) {
                    $query = $con->prepare("SELECT * FROM imagenes WHERE id_producto = :id_producto");
                    $query->execute([
                        ':id_producto' => $producto['id_producto']
                    ]);
                    $productos[$key]['imagenes'] = $query->fetchAll(\PDO::FETCH_ASSOC);
                    //Si no hay imagenes le pondremos una imagen por defecto
                    if (count($productos[$key]['imagenes']) == 0) {
                        $productos[$key]['imagenes'][0]['url'] = 'public/default/anonymous.png';
                        $productos[$key]['imagenes'][0]['default'] = true;
                    }
                }
                $query = $con->prepare("SELECT nombre, id_categoria FROM subcategorias WHERE id_subcategoria = :id_subcategoria");
                $query->execute([
                    ':id_subcategoria' => $productos[0]['id_subcategoria']
                ]);
                $subcategoria = $query->fetch(\PDO::FETCH_ASSOC);

                /* Productos relacionados ---------> */
                $query = $con->prepare("SELECT p.id_producto, p.nombre, pr.id_productos_relacionados FROM productos p INNER JOIN productos_relacionados pr ON p.id_producto = pr.id_producto_relacionado WHERE pr.id_producto = :id_producto");
                $query->execute([
                    ':id_producto' => $producto['id_producto']
                ]);
                $productos[0]['productos_relacionados'] = $query->fetchAll(\PDO::FETCH_ASSOC);
                foreach ($productos[$key]['productos_relacionados'] as $key2 => $producto_relacionado) {
                    $query = $con->prepare("SELECT * FROM imagenes WHERE id_producto = :id_producto");
                    $query->execute([
                        ':id_producto' => $producto_relacionado['id_producto']
                    ]);
                    $productos[$key]['productos_relacionados'][$key2]['imagenes'] = $query->fetchAll(\PDO::FETCH_ASSOC);
                    //Si no hay imagenes le pondremos una imagen por defecto
                    if (count($productos[$key]['productos_relacionados'][$key2]['imagenes']) == 0) {
                        $productos[$key]['productos_relacionados'][$key2]['imagenes'][0]['url'] = 'public/default/anonymous.png';
                        $productos[$key]['productos_relacionados'][$key2]['imagenes'][0]['default'] = true;
                    }
                }
                $query = $con->prepare("SELECT nombre FROM categorias WHERE id_categoria = :id_categoria");
                $query->execute([
                    ':id_categoria' => $subcategoria['id_categoria']
                ]);
                $categoria = $query->fetch(\PDO::FETCH_ASSOC);

                $productos[0]['nombre_categoria'] = $categoria['nombre'];
                $productos[0]['nombre_subcategoria'] = $subcategoria['nombre'];
                $respuesta = ResponseHttp::status200($productos[0]);
                return $respuesta;
            } else {
                return ResponseHttp::status404('No se encontraron productos');
            }
        } catch (\PDOException $e) {
            error_log("ProductoModel::getProductoById -> " . $e);
            die(json_encode(ResponseHttp::status500('No se pueden obtener los productos')));
        }
    }

    final public static function getProductosBySubcategorias()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM productos WHERE id_subcategoria = :id_subcategoria AND estado = 1");
            $query->execute([
                ':id_subcategoria' => self::getIdSubcategoria()
            ]);
            $productos["data"] = $query->fetchAll(\PDO::FETCH_ASSOC);
            $query = $con->prepare("SELECT nombre,descripcion FROM subcategorias WHERE id_subcategoria = :id_subcategoria");
            $query->execute([
                ':id_subcategoria' => self::getIdSubcategoria()
            ]);

            $subcategoria = $query->fetch(\PDO::FETCH_ASSOC);
            $productos['subcategoria_descripcion'] = $subcategoria['descripcion'];
            $productos['subcategoria_nombre'] = $subcategoria['nombre'];

            if (!count($productos["data"]) > 0) return ResponseHttp::status200($productos);

            foreach ($productos["data"] as $key => $producto) {
                $query = $con->prepare("SELECT * FROM imagenes WHERE id_producto = :id_producto");
                $query->execute([
                    ':id_producto' => $producto['id_producto']
                ]);
                $productos["data"][$key]['imagenes'] = $query->fetchAll(\PDO::FETCH_ASSOC);
                if (count($productos["data"][$key]['imagenes']) == 0) {
                    $productos["data"][$key]['imagenes'][0]['url'] = 'public/default/anonymous.png';
                    $productos["data"][$key]['imagenes'][0]['default'] = true;
                }
            }



            return ResponseHttp::status200($productos);
        } catch (\PDOException $e) {
            error_log("ProductoModel::getProductosBySubcategorias -> " . $e);
            die(json_encode(ResponseHttp::status500('No se pueden obtener los productos')));
        }
    }

    final public static function getProductosBySearch($search)
    {
        try {

            //Si la busqueda es vacia no se hara ninguna busqueda
            if (empty($search)) {
                return ResponseHttp::status404('No se encontraron productos');
            }
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM productos WHERE estado = 1 AND (marca LIKE :search OR modelo LIKE :search OR descripcion LIKE :search)");
            $query->execute([
                ':search' => '%' . $search . '%'
            ]);
            $productos = $query->fetchAll(\PDO::FETCH_ASSOC);
            //Si el array no esta vacio
            if (count($productos) > 0) {
                foreach ($productos as $key => $producto) {
                    $query = $con->prepare("SELECT * FROM imagenes WHERE id_producto = :id_producto");
                    $query->execute([
                        ':id_producto' => $producto['id_producto']
                    ]);
                    $productos[$key]['imagenes'] = $query->fetchAll(\PDO::FETCH_ASSOC);
                    //Si no hay imagenes le pondremos una imagen por defecto
                    if (count($productos[$key]['imagenes']) == 0) {
                        $productos[$key]['imagenes'][0]['url'] = 'public/default/anonymous.png';
                    }
                }
                return ResponseHttp::status200($productos);
            } else {
                return ResponseHttp::status404('No se encontraron productos');
            }
        } catch (\PDOException $e) {
            error_log("ProductoModel::deleteProducto -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function updateProducto()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare('UPDATE productos SET id_subcategoria = :id_subcategoria, marca = :marca, modelo = :modelo, descripcion = :descripcion, caracteristicas = :caracteristicas, nombre = :nombre, url_formulario = :url_formulario WHERE id_producto = :id_producto');
            $query->execute([
                ':id_subcategoria' => self::getIdSubcategoria(),
                ':marca' => self::getMarca(),
                ':modelo' => self::getModelo(),
                ':descripcion' => self::getDescripcion(),
                ':caracteristicas' => self::getCaracteristicas(),
                ':id_producto' => self::getIdProducto(),
                ':nombre' => self::getNombre(),
                ':url_formulario' => self::getUrlFormulario()
            ]);
            if ($query->rowCount() > 0) {
                return ResponseHttp::status200('Producto actualizado con exito');
            } else {
                return ResponseHttp::status404('No se puede actualizar el producto');
            }
        } catch (\PDOException $e) {
            error_log("ProductoModel::updateProducto -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function deleteProducto()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("UPDATE productos SET estado = 0 WHERE id_producto = :id_producto AND estado = 1");
            $query->execute([
                ':id_producto' => self::getIdProducto(),
            ]);
            if ($query->rowCount() > 0) {
                return ResponseHttp::status200('Producto eliminado con exito');
            } else {
                return ResponseHttp::status404('No se pudo eliminar el producto');
            }
            return true;
        } catch (\PDOException $e) {
            error_log("ProductoModel::deleteProducto -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function addOrUpdateFileEs($file)
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM productos WHERE id_producto = :id_producto");
            $query->execute([
                ':id_producto' => self::getIdProducto()
            ]);
            $producto = $query->fetch(\PDO::FETCH_ASSOC);
            if (empty($producto)) return ResponseHttp::status404('No se encontro el producto');

            $respuestaFile = UploadFile::addFileOrUpdate($file, $producto, "es");

            /* Si la respuestaFile trae el path todo salio bien */
            if (empty($respuestaFile['path'])) return ResponseHttp::status404($respuestaFile['message']);

            $query = $con->prepare("UPDATE productos SET ficha_tecnica_es = :ficha_tecnica_es WHERE id_producto = :id_producto");
            $query->execute([
                ':id_producto' => self::getIdProducto(),
                ':ficha_tecnica_es' => $respuestaFile['path']
            ]);
            if (!$query->rowCount() > 0) return ResponseHttp::status404('No se pudo actualizar la ficha tecnica en la base de datos');
            return ResponseHttp::status200('Ficha Tecnica subido con exito');
            exit;
        } catch (\PDOException $e) {
            error_log("ProductoModel::addFile -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }
    final public static function addOrUpdateFileIn($file)
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM productos WHERE id_producto = :id_producto");
            $query->execute([
                ':id_producto' => self::getIdProducto()
            ]);
            $producto = $query->fetch(\PDO::FETCH_ASSOC);
            if (empty($producto)) return ResponseHttp::status404('No se encontro el producto');

            $respuestaFile = UploadFile::addFileOrUpdate($file, $producto, "in");

            /* Si la respuestaFile trae el path todo salio bien */
            if (empty($respuestaFile['path'])) return ResponseHttp::status404($respuestaFile['message']);

            $query = $con->prepare("UPDATE productos SET ficha_tecnica_in = :ficha_tecnica_in WHERE id_producto = :id_producto");
            $query->execute([
                ':id_producto' => self::getIdProducto(),
                ':ficha_tecnica_in' => $respuestaFile['path']
            ]);
            if (!$query->rowCount() > 0) return ResponseHttp::status404('No se pudo actualizar la ficha tecnica en la base de datos');
            return ResponseHttp::status200('Ficha Tecnica subido con exito');
            exit;
        } catch (\PDOException $e) {
            error_log("ProductoModel::addFile -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function deleteFileEs()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM productos WHERE id_producto = :id_producto");
            $query->execute([
                ':id_producto' => self::getIdProducto()
            ]);
            $producto = $query->fetch(\PDO::FETCH_ASSOC);
            if (empty($producto)) return ResponseHttp::status404('No se encontro el producto');
            if (empty($producto['ficha_tecnica_es'])) return ResponseHttp::status404('No se encontro la ficha tecnica');
            $respuestaFile = UploadFile::deleteFile($producto['ficha_tecnica_es']);
            if (!$respuestaFile) return ResponseHttp::status404('No se pudo eliminar la ficha tecnica');
            $query = $con->prepare("UPDATE productos SET ficha_tecnica_es = NULL WHERE id_producto = :id_producto");
            $query->execute([
                ':id_producto' => self::getIdProducto()
            ]);
            if (!$query->rowCount() > 0) return ResponseHttp::status404('No se pudo eliminar la ficha tecnica en la base de datos');
            return ResponseHttp::status200('Ficha Tecnica eliminada con exito');
            exit;
        } catch (\PDOException $e) {
            error_log("ProductoModel::deleteFileEs -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }
    final public static function deleteFileIn()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM productos WHERE id_producto = :id_producto");
            $query->execute([
                ':id_producto' => self::getIdProducto()
            ]);
            $producto = $query->fetch(\PDO::FETCH_ASSOC);
            if (empty($producto)) return ResponseHttp::status404('No se encontro el producto');
            if (empty($producto['ficha_tecnica_in'])) return ResponseHttp::status404('No se encontro la ficha tecnica');
            $respuestaFile = UploadFile::deleteFile($producto['ficha_tecnica_in']);
            if (!$respuestaFile) return ResponseHttp::status404('No se pudo eliminar la ficha tecnica');
            $query = $con->prepare("UPDATE productos SET ficha_tecnica_in = NULL WHERE id_producto = :id_producto");
            $query->execute([
                ':id_producto' => self::getIdProducto()
            ]);
            if (!$query->rowCount() > 0) return ResponseHttp::status404('No se pudo eliminar la ficha tecnica en la base de datos');
            return ResponseHttp::status200('Ficha Tecnica eliminada con exito');
            exit;
        } catch (\PDOException $e) {
            error_log("ProductoModel::deleteFileIn -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function addProductoRelacionado($id_producto_relacionado)
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM productos WHERE id_producto = :id_producto");
            $query->execute([
                ':id_producto' => self::getIdProducto()
            ]);
            $producto = $query->fetch(\PDO::FETCH_ASSOC);
            if (empty($producto)) return ResponseHttp::status404('No se encontro el producto');

            $query = $con->prepare("INSERT INTO productos_relacionados (id_producto, id_producto_relacionado) VALUES (:id_producto, :id_producto_relacionado)");
            $query->execute([
                ':id_producto' => self::getIdProducto(),
                ':id_producto_relacionado' => $id_producto_relacionado
            ]);
            if (!$query->rowCount() > 0) return ResponseHttp::status404('No se pudo crear el producto relacionado');
            return ResponseHttp::status200('Producto relacionado creado con exito');
        } catch (\PDOException $e) {
            error_log("ProductoModel::addProductoRelacionado -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }

    final public static function deleteProductoRelacionado()
    {
        try {
            $con = self::getConnection();
            $query = $con->prepare("SELECT * FROM productos_relacionados WHERE id_productos_relacionados = :id_productos_relacionados");
            $query->execute([
                ':id_productos_relacionados' => self::getIdProducto()
            ]);
            $producto = $query->fetch(\PDO::FETCH_ASSOC);
            if (empty($producto)) return ResponseHttp::status404('No se encontro el producto relacionado');
            $query = $con->prepare("DELETE FROM productos_relacionados WHERE id_productos_relacionados = :id_productos_relacionados");
            $query->execute([
                ':id_productos_relacionados' => self::getIdProducto()
            ]);
            if (!$query->rowCount() > 0) return ResponseHttp::status404('No se pudo eliminar el producto relacionado');
            return ResponseHttp::status200('Producto relacionado eliminado con exito');
        } catch (\PDOException $e) {
            error_log("ProductoModel::deleteProductoRelacionado -> " . $e);
            die(json_encode(ResponseHttp::status500()));
        }
    }
}
