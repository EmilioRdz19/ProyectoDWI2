<?php

namespace App\Config;

class UploadFile
{

    final public static function addFileOrUpdate($file, $producto, $idioma)
    {
        try {
            if($idioma == 'es') {
                $archivo = $file['ficha_tecnica_es'];
                $rutaEliminar = $producto['ficha_tecnica_es'];
            } else if($idioma == 'in') {
                $archivo = $file['ficha_tecnica_in'];
                $rutaEliminar = $producto['ficha_tecnica_in'];
            }

            if ($rutaEliminar != null) {
                unlink($rutaEliminar);
            }

            $numero = rand(1, 999);
            $id = $producto['id_producto'];
            $nombreArchivo = pathinfo($archivo['name'], PATHINFO_FILENAME);
            $letras = chr(rand(97, 122)) . chr(rand(97, 122)) . chr(rand(97, 122));
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);

            if ($extension == "pdf") {
                if ($archivo['size'] <  5000000) {
                    if (!file_exists('public/productos/' . $id . '/ficha_tecnica')) {
                        mkdir('public/productos/' . $id . '/ficha_tecnica', 0777, true);
                    }

                    $archivoName = $nombreArchivo . "_" . $letras . $numero . '_' . '.' . $extension;
                    $ruta = 'public/productos/' . $id . '/'.'ficha_tecnica/' . $archivoName;


                    if (move_uploaded_file($archivo['tmp_name'], $ruta)) {
                        move_uploaded_file($archivo['tmp_name'],  $ruta);
                        chmod($ruta, 0777);

                        $data = [
                            'path' => $ruta,
                            'name' => $archivoName
                        ];

                        return $data;
                    } {
                        die(json_encode(ResponseHttp::status400('Error al subir el archivo PDF')));
                    }
                } else {
                    //Retornamos un mensaje de error
                    $data = [
                        'message' => 'El archivo es demasiado grande'
                    ];
                    return $data;
                }
            } else {
                $data = [
                    'message' => 'El archivo no es un PDF'
                ];
                return $data;
            }
        } catch (\PDOException $e) {
            echo json_encode(ResponseHttp::status400($e->getMessage()));
        }
    }


    final public static function deleteFile($rutaFichaTecnica)
    {
        if (file_exists($rutaFichaTecnica)) {
            unlink($rutaFichaTecnica);
            return true;
        }
        return false;
    }
}
