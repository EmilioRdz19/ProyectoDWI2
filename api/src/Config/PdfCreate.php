<?php

namespace App\Config;

use Dompdf\Dompdf;
use Dompdf\Exception as DomException;
use Dompdf\Options;
use App\Config\QrCreate;
use Dotenv\Dotenv;


class PdfCreate
{

  function __construct()
  {
  }

  static function createPdf($dataEvento, $dataBoletos, $dataZona, $rangos)
  {
    try {

      $dotenv = Dotenv::createImmutable(dirname(__DIR__, 2));
      $dotenv->load();
      $_ENV['URL_API'];
      $fecha = date('d/m/Y', strtotime($dataEvento['fecha']));
      $hora = date('H:i', strtotime($dataEvento['hora']));
      $imagen = $dataEvento['imagen'];
      $folio_formato = $dataEvento['folio_formato'];
      $lugar = substr($dataEvento['lugar'], 0, 50);
      $nombre = $dataEvento['nombre'];
      $descripcion = substr($dataEvento['descripcion'], 0, 50);
      $zona = $dataZona['nombre'];

      $html = <<<HTML
        <html>
          <head>
            <style>
              @page{
                margin: 0;
                margin-left: 15px;
            }
            
            .container{
              width: 302.3660px;
            }
            .container-qr {
              justify-content: right;
              display: flex;
              margin: 0;
              padding: 0;
            }
            .qr {
              margin: 0;
              padding: 0;
              text-align: right;
              width: 100px;
              height: 100px;
            }
            .in-text {
              font-style: normal;
              font-size: 12px;
              font-weight: normal;
              color: #000000;

            }
            .container-img {
              display: flex;
              justify-content: center;
            }
            .img {
              text-align: center;
              width: 100%;
              height: 100px;
            }
            p {
              font-size: 14px;
              font-weight: 300;
              margin: 5px;
              padding: 0;
            }
            table {
              font-family: arial, sans-serif;
              width: 100%;
              border-collapse: collapse;
            }
            td,
            th {
              padding: 0;
            }
            td{
              padding: 0;
            }
            .centrado {
              text-align: center;
            }
            .margin{
              margin-top: 0px;
            }
            .abajo{
              border: 1px dashed #000000;
              margin-top: 45px;
            }
            </style>
          </head>
        <body>
        HTML;
      foreach ($dataBoletos as $boleto) {
        $llave_unica = $boleto['llave_unica'];
        $qr = QrCreate::createQr($llave_unica);
        $html .=  '
          <div class="container">
            <table class="margin">
              <tr class="centrado">
                <td><p><span class="in-text">Fecha:</span> ' . $fecha . '</p></td>
                <td><p><span class="in-text">Hora:</span> ' . $hora . '</p></td>
              </tr>
              <tr>
                <td colspan="2">
                  <div class="container-img">
                    <img class="img" src="' . $_ENV['URL_API'] . '/' . $imagen . '" alt="' . $_ENV['URL_API'] . '/' . $imagen . '"/>
                  </div>
                </td>
              </tr>
              <tr>
                <td colspan="2"><p class="in-text">Folio: ' . $folio_formato . $boleto['folio'] . '</p></td>
              </tr>
              <tr>
                <td colspan="2"><p class="in-text">Evento: ' . $nombre . '</p></td>
              </tr>
      
              <tr>
                <td colspan="2"><p class="in-text">Descripcion: ' . $descripcion . '</p></td>
              </tr>
              <tr>
                <td colspan="2"><p class="in-text">Lugar: ' . $lugar . '</p></td>
              </tr>
              <tr>
                <td colspan="2"><p class="in-text">Costo: $' . $boleto['costo_boleto'] . '</p></td>
              </tr>
              <tr>
                <td colspan="2" ><p class="in-text">Zona: ' . $zona . '</p></td>
              </tr>
            </table>
      <table class="abajo">
          <tr>
            <td><p class="in-text">Folio: ' . $folio_formato . $boleto['folio'] . '</p></td>
            <td rowspan="3">
            <div class="container-qr"><img class="qr" src="' . $qr . '" alt="qr"/></div></td>
          </tr>
            <tr><td><p class="in-text">Costo: $' . $boleto['costo_boleto'] . '</p></td>
          </tr>
          <tr><td><p class="in-text">Zona: ' . $zona . '</p></td></tr>
      </table>
          </div>
          <div style="page-break-after:always;"></div>
            ';
      } //Termina el foreach
      $html .= '</body></html>';


      //Configuracion de la libreria
      $filename = "test.pdf";
      $options = new Options();
      $options->set('isRemoteEnabled', true);

      $dompdf = new Dompdf($options);
      $dompdf->loadHtml($html);
      $dompdf->setPaper(array(0, 0, 227.622, 398.338), 'portrait');

      $dompdf->render();

      //$dompdf->stream($filename, array("Attachment" => false));
      //Convertimos el pdf a base64 para poderlo mostrar en el modal
      $base64 = $dompdf->output();
      $base64 = base64_encode($base64);
      $data['pdf'] = $base64;
      $data['status'] = 'success';
      $data['message'] = 'Se ha generado el PDF correctamente';
      return $data;
    } catch (DomException $e) {
      echo $e->getMessage();
    }
  }
}
