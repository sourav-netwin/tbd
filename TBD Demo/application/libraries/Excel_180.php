<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once APPPATH."/third_party/PHPExcel_180/Classes/PHPExcel.php"; 
 
class Excel_180 extends PHPExcel { 
    public function __construct() { 
        parent::__construct(); 
    } 
}