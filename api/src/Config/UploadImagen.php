<?php

namespace App\Config;

class UploadImagen
{

    final public static function subidaImagenEvento($file, $producto)
    {
        try {
            $imagen = $file['imagen'];
            $id = $producto['id_producto'];
            $numero = rand(1, 999);
            $nombreImagen = pathinfo($imagen['name'], PATHINFO_FILENAME);
            $letras = chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122));
            $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
            if ($extension == "jpg" || $extension == "png" || $extension == "jpeg") {
                if ($imagen['size'] <  5000000) { 
                    if (!file_exists('public/productos/' . $id . '/imagenes')) {
                        mkdir('public/productos/' . $id . '/imagenes', 0777, true);
                    }

                    $imageName = $nombreImagen . "_" .$letras .$numero . '_' . '.' . $extension;

                    $ruta = 'public/productos/' . $id . '/'.'imagenes/' . $imageName;


                    if (move_uploaded_file($imagen['tmp_name'], $ruta)) {
                        move_uploaded_file($imagen['tmp_name'],  $ruta);
                        chmod($ruta, 0777);

                        $data = [
                            'path' => $ruta,
                            'name' => $imageName
                        ];

                        return $data;
                    } {
                        die(json_encode(ResponseHttp::status400('Error al subir la imagen')));
                    }
                } else {
                    //Retornamos un mensaje de error
                    return "El archivo es muy pesado";
                }
            } else {
                //Retornamos un mensaje de error
                return "El archivo no es una imagen";
            }
        } catch (\PDOException $e) {
            echo json_encode(ResponseHttp::status400($e->getMessage()));
        }
    }

    final public static function updateImagen($file, $producto, $rutaAnteriorImagen)
    {
        try {
            if (file_exists($rutaAnteriorImagen['url'])) {
                unlink($rutaAnteriorImagen['url']);
            }
            $imagen = $file['imagen'];
            $id = $producto['id_producto'];
            $nombreImagen = pathinfo($imagen['name'], PATHINFO_FILENAME);
            $numero = rand(1, 999);
            $letras = chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122));
            $extension = pathinfo($imagen['name'], PATHINFO_EXTENSION);
            if ($extension == "jpg" || $extension == "png" || $extension == "jpeg") {
                if ($imagen['size'] <  5000000) { 
                    if (!file_exists('public/productos/' . $id . '/imagenes')) {
                        mkdir('public/productos/' . $id . '/imagenes', 0777, true);
                    }

                    $imageName = $nombreImagen . "_" .$letras .$numero . '_' . '.' . $extension;
                    $ruta = 'public/productos/' . $id . '/'.'imagenes/' . $imageName;


                    if (move_uploaded_file($imagen['tmp_name'], $ruta)) {
                        move_uploaded_file($imagen['tmp_name'],  $ruta);
                        chmod($ruta, 0777);

                        $data = [
                            'path' => $ruta,
                            'name' => $imageName
                        ];

                        return $data;
                    } {
                        die(json_encode(ResponseHttp::status400('Error al subir la imagen')));
                    }
                } else {
                    //Retornamos un mensaje de error
                    return "El archivo es muy pesado";
                }
            } else {
                //Retornamos un mensaje de error
                return "El archivo no es una imagen";
            }
        } catch (\PDOException $e) {
            echo json_encode(ResponseHttp::status400($e->getMessage()));
        }
    }

    final public static function eliminar($rutaImagen)
    { 
        if (file_exists($rutaImagen)) {
            unlink($rutaImagen);
            return true;
        }
        return false;
    }

}
