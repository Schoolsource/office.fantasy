<?php

$users[] = array('key' => 'customers', 'text' => 'Customers', 'link' => $url.'customers', 'icon' => 'users');
$users[] = array('key' => 'sales', 'text' => 'Staff', 'link' => $url.'sales', 'icon' => 'user');
foreach ($users as $key => $value) {
    if (empty($this->permit[$value['key']]['view'])) {
        unset($users[$key]);
    }
}
if (!empty($users)) {
    echo $this->fn->manage_nav($users, $this->getPage('on'));
}

$events[] = array('key' => 'events', 'text' => 'Calendar', 'link' => $url.'events', 'icon' => 'calendar-check-o');
foreach ($events as $key => $value) {
    if (empty($this->permit[$value['key']]['view'])) {
        unset($events[$key]);
    }
}
if (!empty($events)) {
    echo $this->fn->manage_nav($events, $this->getPage('on'));
}


foreach ($order as $key => $value) {
    if (empty($this->permit[$value['key']]['view'])) {
        unset($order[$key]);
    }
}
if (!empty($order)) {
    echo $this->fn->manage_nav($order, $this->getPage('on'));
}

$payments[] = array('key' => 'lists', 'text' => 'Receipts Report', 'link' => $url.'payments/lists', 'icon' => 'money');
// $payments[] = array('key'=>'lists2', 'text'=>'Payments bank', 'link'=>$url.'payments/bank', 'icon'=>'cc-visa');
// $payments[] = array('key'=>'lists3', 'text'=>'Payments check', 'link'=>$url.'payments/check', 'icon'=>'credit-card-alt');
foreach ($payments as $key => $value) {
    if (empty($this->permit[$value['key']]['view'])) {
        unset($payments[$key]);
    }
}
if (!empty($payments)) {
    echo $this->fn->manage_nav($payments, $this->getPage('on'));
}

$paycheck[] = array('key' => 'paycheck', 'text' => 'Pay Cheque', 'link' => $url.'paycheck', 'icon' => 'credit-card');
foreach ($paycheck as $key => $value) {
    if (empty($this->permit[$value['key']]['view'])) {
        unset($paycheck[$key]);
    }
}
if (!empty($paycheck)) {
    echo $this->fn->manage_nav($paycheck, $this->getPage('on'));
}

$suppliers[] = array('key' => 'suppliers', 'text' => 'Suppliers', 'link' => $url.'suppliers', 'icon' => 'handshake-o');
$suppliers[] = array('key' => 'tax', 'text' => 'VAT Buy', 'link' => $url.'tax', 'icon' => 'diamond');
$suppliers[] = array('key' => 'bills', 'text' => 'VAT Sale', 'link' => $url.'bills', 'icon' => 'university');
foreach ($suppliers as $key => $value) {
    if (empty($this->permit[$value['key']]['view'])) {
        unset($suppliers[$key]);
    }
}
if (!empty($suppliers)) {
    echo $this->fn->manage_nav($suppliers, $this->getPage('on'));
}

$products[] = array('key' => 'discounts', 'text' => 'Discounts', 'link' => $url.'discounts', 'icon' => 'cart-arrow-down');
$products[] = array('key' => 'categories', 'text' => 'Categories', 'link' => $url.'categories', 'icon' => 'database');
$products[] = array('key' => 'products', 'text' => 'Products', 'link' => $url.'products', 'icon' => 'cart-arrow-down');
$products[] = array('key' => 'import', 'text' => 'Product Receive', 'link' => $url.'import', 'icon' => 'product-hunt');
$products[] = array('key' => 'export', 'text' => 'Stock Adjust', 'link' => $url.'export', 'icon' => 'upload');
$products[] = array('key' => 'stock_balance', 'text' => 'Stock Balance', 'link' => $url.'stock/balance', 'icon' => 'balance-scale');
foreach ($products as $key => $value) {
    if (empty($this->permit[$value['key']]['view'])) {
        unset($products[$key]);
    }
}//mflv[#TRM
if (!empty($products)) {
    echo $this->fn->manage_nav($products, $this->getPage('on'));
}

$reports[] = array('key' => 'comission', 'text' => 'Commission', 'link' => $url.'reports/comission', 'icon' => 'signal');
$reports[] = array('key' => 'revenue', 'text' => 'Sale Reports', 'link' => $url.'reports/revenue', 'icon' => 'line-chart');
$reports[] = array('key' => 'vatbuy', 'text' => 'Vat Buy Reports', 'link' => $url.'reports/vatbuy', 'icon' => 'diamond');
$reports[] = array('key' => 'vatsale', 'text' => 'Vat Sale Reports', 'link' => $url.'reports/vatsale', 'icon' => 'university');
$reports[] = array('key' => 'due', 'text' => 'Debtor Report', 'link' => $url.'reports/due', 'icon' => 'money');
$reports[] = array('key' => 'dailycollect', 'text' => 'Daily collect', 'link' => $url.'collectdaily', 'icon' => 'ship');

$reports[] = array('key' => 'project', 'text' => 'Project Report', 'link' => $url.'reports/project', 'icon' => 'free-code-camp');
foreach ($reports as $key => $value) {
    if (empty($this->permit[$value['key']]['view'])) {
        unset($reports[$key]);
    }
}
if (!empty($reports)) {
    echo $this->fn->manage_nav($reports, $this->getPage('on'));
}

$cog[] = array('key' => 'settings', 'text' => $this->lang->translate('menu', 'Settings'), 'link' => $url.'settings', 'icon' => 'cog');
echo $this->fn->manage_nav($cog, $this->getPage('on'));
