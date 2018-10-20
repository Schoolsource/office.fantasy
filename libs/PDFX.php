<?php

require( 'PDF/fpdf.php' );

define('FPDF_FONTPATH', WWW_LIBS . 'PDF/font/');

class PDFX extends FPDF {

    	public function __construct() {
        parent::__construct();
        
        $this->fn = new _function();

        // เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวธรรมดา กำหนด ชื่อ เป็น angsana
		$this->AddFont('angsana','','angsa.php');
		 
		// เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวหนา  กำหนด ชื่อ เป็น angsana
		$this->AddFont('angsana','B','angsab.php');
		 
		// เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวหนา  กำหนด ชื่อ เป็น angsana
		$this->AddFont('angsana','I','angsai.php');
		 
		// เพิ่มฟอนต์ภาษาไทยเข้ามา ตัวหนา  กำหนด ชื่อ เป็น angsana
		$this->AddFont('angsana','BI','angsaz.php');
    }

}
