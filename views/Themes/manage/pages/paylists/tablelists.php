<?php

//print_r($this->results['lists']); die;
$tr = '';
$tr_total = '';
$total_amount = 0;
$total_receive = 0;
if (!empty($this->results['lists'])) {
    // print_r($this->results); die;

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) {
        $cls = $i % 2 ? 'even' : 'odd';

        $dateStr = date('d/m/Y', strtotime($item['date']));

        $type = '';
        if (!empty($item['type_is_cash'])) {
            $type = $item['type_name'];
        } elseif (!empty($item['type_is_bank'])) {
            $type = '<div>'.$item['type_name'].'</div>';
            $type .= '<div>('.$item['bank_code'].') '.$item['account_number'].'</div>';
        } elseif (!empty($item['type_is_check'])) {
            $type = '<div>'.$item['type_name'].'</div>';
            $type .= '<div>'.$item['bank_code'].'-'.$item['check_number'].'</div>';
        }

        $image = '-';
        if (!empty($item['image_arr'])) {
            $image = '<span class="gbtn"><a href="'.URL.'payments/showPicture/'.$item['id'].'" target="_blank" data-plugins="dialog" class="btn btn-no-padding btn-blue"><i class="icon-eye"></i></a></span>';
        }

        $cls = '#ccc';
        if ($item['ord_net_price'] != $item['amount']) {
            $cls = '#ff8484';
        }

        $bankDate = '-';
        if ($item['bank_date'] != '0000-00-00') {
            $bankDate = date('d/m/Y', strtotime($item['bank_date'].' 00:00:00'));
        }

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'"">'.

            '<td class="date">'.$dateStr.'</td>'.

            '<td class="name">'.
                '<div class="ellipsis"><a target="_blank" title="'.$item['code'].'" class="fwb" href="'.URL.'payments/'.$item['order_id'].'">'.(!empty($item['code']) ? $item['code'] : '-').'</a></div>'.
            '</td>'.

            '<td class="status" style="white-space: nowrap;text-align: left;border-right: 1px solid #e9e9e9;">'.$item['cus_name'].'</td>'.
            '<td class="status" style="white-space: nowrap;text-align: left;text-align: left;border-right: 1px solid #e9e9e9;">'.$item['sale_name'].'</td>'.
            '<td class="status " style="white-space: nowrap;text-align: left;border-right: 1px solid #e9e9e9;">'.$item['account_number'].'</td>'.

            '<td class="status">'.$bankDate.'</td>'.

            '<td class="price" style="background-color:#f2f2f2;border-right: 1px solid #e9e9e9;">'.number_format($item['ord_net_price']).'</td>'.
            '<td class="price" style="background-color:'.$cls.';border-right: 1px solid #e9e9e9">'.number_format($item['amount']).'</td>'.

        '</tr>';

        $total_amount += $item['ord_net_price'];
        $total_receive += $item['amount'];
    }

    $tr_total = '<tfoot style="font-size:20px; background-color:#5189da; color:#fff; font-weight:bold;">
                <tr>
                    <th colspan="6" style="text-align:right;">Total</th>
                    <th colspan="1" style="text-align:right;">'.number_format($total_amount).'</th>
                    <th colspan="1" style="text-align:right;background-color:#f44336;">'.number_format($total_receive).'</th>
                </tr>
             </tfoot>';
}

$table = '<table><tbody>'.$tr.'</tbody>'.$tr_total.'</table>';
