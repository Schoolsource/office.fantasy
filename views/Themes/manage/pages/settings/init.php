<?php

$this->count_nav = 0;

/* System */
$sub = array();
$sub[] = array('text' => $this->lang->translate('Company'),'key' => 'company','url' => URL.'settings/company');
// $sub[] = array('text'=>'Dealer','key'=>'dealer','url'=>URL.'settings/dealer');
$sub[] = array('text' => 'Profile','key' => 'my','url' => URL.'settings/my');

foreach ($sub as $key => $value) {
	if( empty($this->permit[$value['key']]['view']) ) unset($sub[$key]);
}
if( !empty($sub) ){
	$this->count_nav+=count($sub);
	$menu[] = array('text' => '', 'url' => URL.'settings/company', 'sub' => $sub);
}

$sub = array();
$sub[] = array('text'=>'Manage Account', 'key'=>'admins', 'url'=>URL.'settings/accounts/admins', 'permit'=>'admin');

foreach ($sub as $key => $value) {
	if( empty($this->permit[$value['permit']]['view']) ) unset($sub[$key]);
}
if( !empty($sub) ){
	$this->count_nav+=count($sub);
	$menu[] = array('text'=>'User Account', 'url'=>URL.'settings/admins', 'sub'=>$sub);
}

$sub = array();
// $sub[] = array('text' => 'ธนาคาร', 'key'=>'bank', 'url' => URL.'settings/payments/bank');
$sub[] = array('text' => 'Bank account', 'key'=>'account', 'url' => URL.'settings/payments/account', 'permit'=>'payment');
foreach ($sub as $key => $value) {
	if( empty($this->permit[$value['permit']]['view']) ) unset($sub[$key]);
}
if( !empty($sub) ){
	$this->count_nav+=count($sub);
	$menu[] = array('text' => 'Payments', 'url' => URL.'settings/company', 'sub' => $sub);
}

$sub = array();
$sub[] = array('text' => 'Supplier categories', 'key'=>'type', 'url' => URL.'settings/suppliers/type', 'permit'=>'suppliers');
$sub[] = array('text' => 'Tax categories', 'key'=>'category', 'url' => URL.'settings/suppliers/category', 'permit'=>'suppliers');
foreach ($sub as $key => $value) {
	if( empty($this->permit[ $value['permit'] ]['view']) ) unset($sub[$key]);
}
if( !empty($sub) ){
	$this->count_nav+=count($sub);
	$menu[] = array('text' => 'Suppliers', 'url' => URL.'settings/suppliers', 'sub' => $sub);
}

$sub = array();
$sub[] = array('text' => 'Adjust Categories', 'key'=>'categories', 'url' => URL.'settings/export/categories', 'permit'=>'stock');
foreach ($sub as $key => $value) {
	if( empty($this->permit[$value['permit']]['view']) ) unset($sub[$key]);
}
if( !empty($sub) ){
	$this->count_nav+=count($sub);
	$menu[] = array('text' => 'Stock', 'url' => URL.'settings/export', 'sub' => $sub);
}


$sub = array();
$sub[] = array('text' => 'Project', 'key'=>'settings_customer_project', 'url' => URL.'settings/customer/project');
foreach ($sub as $key => $value) {
	if( empty($this->permit[$value['key']]['view']) ) unset($sub[$key]);
}
if( !empty($sub) ){
	$this->count_nav+=count($sub);
	$menu[] = array('text' => 'Customer', 'url' => URL.'settings/customer/project', 'sub' => $sub);
}
