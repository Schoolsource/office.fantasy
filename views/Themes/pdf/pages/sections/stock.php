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
$seq = 0;

foreach ($this->datalist as $i => $item) {
    ++$seq;
    $input = $item['receive'] + $item['adjust'];
    // $balance = $input - $item['output'];
    $balance = $item['receive'] - $item['output'] - $item['adjust'];

    $listsbox .= '<tr>
        <td width="5%" class="td-seq">'.$seq.'</td>
        <td style="text-align:left;" >'.$item['name'].'</td>
        <td style="text-align:center;" width="10%">'.(!empty($item['vat']) ? '<span style="color:#792dff">/</span>' : '<span style="color:#FF5722">x</span>').'</td>
        <td style="text-align:right;" width="10%">'.($item['receive'] == 0 ? '-' : number_format($item['receive'])).'</td>
        <td style="text-align:right;" width="10%">'.($item['adjust'] == 0 ? '-' : number_format($item['adjust'])).'</td>
        <td style="text-align:right;background-color:#f2f2f2;" class="price" width="10%">'.($item['output'] == 0 ? '-' : number_format($item['output'])).'</td>
        <td style="text-align:right; class="price" width="10%">'.($balance < 0 ? ' td-minus' : '').'">'.($balance == 0 ? '-' : number_format($balance)).'</td>
    </tr>';
    // $listsbox .= '<tr>
    //     <td width="5%" class="td-seq">'.$seq.'</td>
    //     <td style="text-align:left;" >'.$item['name'].'</td>
    //     <td style="text-align:center;" width="10%">'.(!empty($item['vat']) ? '<span style="color:#792dff">/</span>' : '<span style="color:#FF5722">x</span>').'</td>
    //     <td style="text-align:right;" width="10%">'.($item['receive'] == 0 ? '-' : number_format($item['receive'])).'</td>
    //     <td style="text-align:right;" width="10%">'.($item['adjust'] == 0 ? '-' : number_format($item['adjust'])).'</td>
    //     <td style="text-align:right;" width="10%">'.($input == 0 ? '-' : number_format($input)).'</td>
    //     <td style="text-align:right;background-color:#f2f2f2;" class="price" width="10%">'.($item['output'] == 0 ? '-' : number_format($item['output'])).'</td>
    //     <td style="text-align:right; class="price" width="10%">'.($balance < 0 ? ' td-minus' : '').'">'.($balance == 0 ? '-' : number_format($balance)).'</td>
    // </tr>';
}

// Datalist
$html .= '<table class="tRep-masIncome" style="margin-top:5mm;">
            <thead>
                <tr>
                    <th class="tal" width="5%">#</th>
                    <th class="name">Product Name</th>
                    <th width="10%">Vat</th>
                    <th width="10%">Input</th>
                    <th width="10%">Adjust</th>
                    <th class="price" width="10%">Output</th>
                    <th class="price" width="10%">Balance</th>
                </tr>
            </thead>
            <tbody>'.$listsbox.'</tbody>

    </table>';
