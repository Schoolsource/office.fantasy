<?php

$arr['title'] = 'Importing Data.';

if( isset($_REQUEST['next']) ){
	$arr['hiddenInput'][] = array('name'=>'next','value'=>$_REQUEST['next']);
}

$arr['form'] = '<form _class="js-submit-form" action="'.URL.'customers/import" method="post" enctype="multipart/form-data"></form>';

// 
$arr['body'] = ''.

	// '<div class="mbm pam uiBoxYellow">ตัวอย่างไฟล์อัพโหลด <a href="http://localhost/events/assess/files/example/upload_user.xls">upload_user.xls</a></div>'.
	/*  accept="application/vnd.ms-excel"*/

	'<div><div class="mbs fwb">Browse your computer: <span class="fcg">(Max: 25 MB)</span></div><input id="signup-upload-file" name="file1" type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"></div>';


$arr['button'] = '<button type="submit" class="btn btn-blue btn-submit"><span class="btn-text">'.$this->lang->translate('Import').'</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';

echo json_encode($arr);