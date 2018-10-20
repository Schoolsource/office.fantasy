<?php

$title[] = array('key'=>'date', 'text'=>'Date', 'sort'=>'date');
$title[] = array('key'=>'name', 'text'=>'ORDER CODE');
$title[] = array('key'=>'status', 'text'=>'Proof of payment');
$title[] = array('key'=>'contact', 'text'=>'Payment Method');
$title[] = array('key'=>'price', 'text'=>'Amount');

$this->tabletitle = $title;
$this->getURL =  URL.'payments/bank/';
