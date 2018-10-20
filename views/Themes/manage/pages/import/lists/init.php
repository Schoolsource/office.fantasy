<?php

$title[] = array('key'=>'number','text'=>'Code', 'sort'=>'id');
$title[] = array('key'=>'date', 'text'=>'Date', 'sort'=>'date');
$title[] = array('key'=>'contact', 'text'=>'Invoice No.', 'sort'=>'code');
$title[] = array('key'=>'name', 'text'=>'Supplier Name');
$title[] = array('key'=>'status', 'text'=>'QTY');
$title[] = array('key'=>'price', 'text'=>'Amount');
$title[] = array('key'=>'actions', 'text'=>'Manage');

$this->tabletitle = $title;
$this->getURL =  URL.'import/';
