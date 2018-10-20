<?php

$title[] = array('key'=>'ID', 'text'=>'Customer code', 'sort'=>'sub_code');
$title[] = array('key'=>'name', 'text'=>'Shop name / customer', 'sort'=>'name_store');
$title[] = array('key'=>'contact', 'text'=>'Sale', 'sort'=>'sale_code');
$title[] = array('key'=>'email', 'text'=>'Province', 'sort'=>'province');
$title[] = array('key'=>'phone', 'text'=>'Telephone number', 'sort'=>'phone');
$title[] = array('key'=>'status', 'text'=>'Status');
$title[] = array('key'=>'actions', 'text'=>'Actions');

$this->tabletitle = $title;
$this->getURL =  URL.'customers/';
