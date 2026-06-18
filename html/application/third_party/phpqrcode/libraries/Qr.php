<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Pdf.php
*
* Version: 1.0.0
*
* Author: Pedro Ruiz Hidalgo
*		  ruizhidalgopedro@gmail.com
*         @pedroruizhidalg
*
* Location: application/third_party/fpdf/libraries/Pdf.php
*
* Created:  2018-02-27
*
* Description:  This manages FPDF
*
* Requirements: PHP5 or above
*
*/

require_once __DIR__ . '/qrlib.php';
class Qr extends QRcode
{
      function __construct(){
          $ci =   & get_instance();
      }
}
