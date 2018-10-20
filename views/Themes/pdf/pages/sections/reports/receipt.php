<?php


// Header
$html .= '<table>
	<tbody>
		<tr>
			<th class="tal">'.$settings['title'].'</th>
			<th class="tar"></th>
		</tr>
	</tbody>
</table>';

$listsbox = '';
$totalAmount = 0;
$totalReceived = 0;

$vary = array('code', 'cus_name', 'sale_name', 'account_number', 'ord_net_price', 'amount');

foreach ($this->datalist as $i => $item) {
    $dateStr = date('d/m/Y', strtotime($item['date']));

    $cls = '#ccc';
    if ($item['ord_net_price'] != $item['amount']) {
        $cls = '#ff8484';
    }

    $totalAmount += $item['ord_net_price'];
    $totalReceived += $item['amount'];

    foreach ($vary as $key) {
        $item[$key] = !empty($item[$key]) ? $item[$key] : '-';
    }

    $bankDate = '-';
    if ($item['bank_date'] != '0000-00-00') {
        $bankDate = date('d/m/Y', strtotime($item['bank_date'].' 00:00:00'));
    }

    $listsbox .= '<tr>
		<td width="5%">'.$dateStr.'</td>
		<td style="text-align:left;" width="10%">'.$item['code'].'</td>
		<td style="text-align:left;" width="25%">'.$item['cus_name'].'</td>
		<td width="10%">'.$item['sale_name'].'</td>
		<td width="10%">'.$item['account_number'].'</td>
		<td width="10%">'.$bankDate.'</td>
		<td style="text-align:right;background-color:#f2f2f2;" class="price" width="8%">'.number_format($item['ord_net_price']).'</td>
		<td style="text-align:right;background-color:'.$cls.'" class="price" width="8%">'.number_format($item['amount']).'</td>
	</tr>';
}

// Datalist
$html .= '<table class="tRep-masIncome" style="margin-top:5mm;">
			<thead>
				<tr>
					<th class="tal" width="5%">Date</th>
					<th class="name" width="10%">Order Code</th>
					<th width="25%">Customer</th>
					<th width="10%">Sale</th>
					<th width="10%">Bank</th>
					<th width="10%">Bank Date</th>
					<th class="price" width="10%">Amount</th>
					<th class="price" width="10%">Received</th>
				</tr>
			</thead>
			<tbody>'.$listsbox.'</tbody>


			<tbody>
				<tr>
					<th class="tar" colspan="6">TOTAL</th>
					<th style="text-align:right;background-color:#92b6ea" class="tac">'.number_format($totalAmount).'</th>
					<th style="text-align:right;background-color:#ff8484" class="tac">'.number_format($totalReceived).'</th>
				</tr>
			</tbody>
	</table>';
