<?php

$title[] = array('key' => 'date', 'text' => 'Date', 'sort' => 'date');
$title[] = array('key' => 'name', 'text' => 'Order Code', 'sort' => 'code');
$title[] = array('key' => 'status', 'text' => 'Project', 'sort' => 'project');
$title[] = array('key' => 'contact', 'text' => 'Sale', 'sort' => 'sale_code');
$title[] = array('key' => 'address', 'text' => 'Shop Name / Order');
$title[] = array('key' => 'price', 'text' => 'Total price', 'sort' => 'prices');
$title[] = array('key' => 'price', 'text' => 'Already paid');
// $title[] = array('key'=>'price', 'text'=>'ยอดเงินค้าง');
$title[] = array('key' => 'status', 'text' => 'Status');
// $title[] = array('key'=>'status', 'text'=>'Project');
$title[] = array('key' => 'status', 'text' => 'History');
$title[] = array('key' => 'actions', 'text' => '');

$this->tabletitle = $title;
$this->getURL = URL.'payments/';
