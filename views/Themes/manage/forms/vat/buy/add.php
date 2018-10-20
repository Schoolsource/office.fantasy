<?php 
$title = 'VAT BUY';
if( !empty($this->item) ){
	$arr['title'] = "Edit {$title}";
	$arr['hiddenInput'][] = array('name'=>'id', 'value'=>$this->item['id']);
}
else{
	$arr['title'] = "Add {$title}";
}

$form = new Form();
$form = $form->create()
	// set From
	->elem('div')
	->addClass('form-insert');

$form 	->field("tax_date")
		->label("DATE")
		->addClass("inputtext")
		->attr("data-plugins", "datepicker")
		->autocomplete('off')
		->value( !empty($this->item['date']) ? $this->item['date'] : '' );

$form 	->field("tax_credit")
		->label("Credit")
		->addClass("inputtext")
		->autocomplete('off')
		->select( $this->credit )
		->value( !empty($this->item['credit']) ? $this->item['credit'] : '' );

$form 	->field("tax_slipt")
		->label("SLIPT")
		->addClass("inputtext")
		->autocomplete('off')
		->value( !empty($this->item['slipt']) ? $this->item['slipt'] : '' );

$form 	->field("tax_category_id")
		->label("Category")
		->addClass("inputtext")
		->autocomplete('off')
		->select( $this->category )
		->value( !empty($this->item['category_id']) ? $this->item['category_id'] : '' );

$form 	->field("tax_desc")
		->label("DESCRIPTION")
		->addClass("inputtext")
		->autocomplete('off')
		->value( !empty($this->item['desc']) ? $this->item['desc'] : '' );

//  $form 	->field("tax_sup_code")
// 		->label("Supplier Code")
// 		->addClass("inputtext")
// 		->autocomplete("off")
// 		->value( !empty($this->item['sup_code']) ? $this->item['sup_code'] : '' );

// $form 	->field("tax_sup_name")
// 		->label("Supplier Name")
// 		->addClass("inputtext")
// 		->autocomplete("off")
// 		->value( !empty($this->item['sup_name']) ? $this->item['sup_name'] : '' ); 

$form 	->field("tax_sup_id")
		->label("Supplier")
		->addClass("inputtext")
		->autocomplete("off")
		->select( $this->supplier['lists'], 'id', 'name_str' )
		->value( !empty($this->item['sup_id']) ? $this->item['sup_id'] : '' );

$form 	->field("tax_total")
		->label("Total")
		->addClass("inputtext")
		->autocomplete("off")
		->value( !empty($this->item['total']) ? $this->item['total'] : '' );

$form 	->field("tax_vat")
		->label("VAT")
		->addClass("inputtext")
		->autocomplete("off")
		->value( !empty($this->item['vat']) ? $this->item['vat'] : '' );

$ch = !empty($this->item['is_report']) ? ' checked="1"' : "";
$ch_box = '<label class="checkbox"><input'.$ch.' type="checkbox" name="tax_is_report"> This is Report</label>';
$form 	->field("is_report")
		->label("Check for report monthly")
		->text( $ch_box );

# set form
$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL. 'tax/save"></form>';

#BODY
$arr['body'] = $form->html();

# fotter: button
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">'.$this->lang->translate('Save').'</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">'.$this->lang->translate('Cancel').'</span></a>';

$arr['width'] = 550;
// $arr['is_close_bg'] = true;

echo json_encode($arr);
