<?php

$title[] = array('key'=>'status_str', 'text'=>'Category');
// $title[] = array('key'=>'image', 'text'=>'');
$title[] = array('key'=>'name', 'text'=>'Product Name', 'sort'=>'pds_name');
$title[] = array('key'=>'price', 'text'=>'Member Price', 'sort'=>'frontend');
$title[] = array('key'=>'price', 'text'=>'Website Price', 'sort'=>'website');
$title[] = array('key'=>'status', 'text'=>'Com (%)', 'sort'=>'pds_comission');
$title[] = array('key'=>'status_str', 'text'=>'Status');
$title[] = array('key'=>'status', 'text'=>'Salon');
$title[] = array('key'=>'status', 'text'=>'Website');
$title[] = array('key'=>'status', 'text'=>'Vat');
$title[] = array('key'=>'status', 'text'=>'HOT');
$title[] = array('key'=>'actions', 'text'=>'Actions');

$this->tabletitle = $title;
$this->getURL =  URL.'products/';
