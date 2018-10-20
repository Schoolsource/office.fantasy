<?php 

$arr["title"] = "Print vat buy";

$form = new Form();
$form = $form->create()
			 ->elem('div')
			 ->addClass("form-insert");

$month = '';
for($i=1;$i<=12;$i++){
	$sel = '';
	if( $i == date("n") ) $sel = ' selected="1"';
	$month .= '<option'.$sel.' value="'.$i.'">'.$this->fn->q('time')->month($i, true).'</option>';
}
$month = '<select class="inputtext" name="month">'.$month.'</select>';

$form 	->field("month")
		->label("Select Month")
		->text( $month );

$year = '';
$nowYear = date("Y");
for($i=0; $i<5; $i++){
	$sel = '';
	if( ($nowYear-$i) == date("Y") ) $sel = ' selected="1"';
	$year .= '<option'.$sel.' value="'.($nowYear-$i).'">'.( ($nowYear-$i)+543 ).'</option>';
}
$year = '<select class="inputtext" name="year">'.$year.'</select>';

$form 	->field("year")
		->label("Select Year")
		->text( $year );

$arr['form'] = '<form class="" action="'.URL. 'pdf/vat_buy" target="_blank"></form>';
$arr["body"] = $form->html();
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">'.$this->lang->translate('Print').'</span></button>';
$arr['bottom_msg'] = '<a class="btn btn-red" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';

$arr["is_close_bg"] = true;

echo json_encode($arr);