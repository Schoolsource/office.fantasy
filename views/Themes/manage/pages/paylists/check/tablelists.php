<?php
//print_r($this->results['lists']); die;
$tr = "";
$tr_total = "";

if( !empty($this->results['lists']) ){
    //print_r($this->results); die;

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) {

        $cls = $i%2 ? 'even' : "odd";

        $dateStr = date("d/m/Y", strtotime($item['date']));

        $type = "";
        if( !empty($item['type_is_cash']) ){
            $type = $item['type_name'];
        }
        elseif( !empty($item['type_is_bank']) ){
            $type = "<div>".$item['type_name'].'</div>';
            $type .= "<div>(".$item['bank_code'].") ".$item['account_number']."</div>";
        }
        elseif( !empty($item['type_is_check']) ){
            $type = "<div>".$item['type_name'].'</div>';
            $type .= "<div>".$item['bank_code']."-".$item['check_number']."</div>";
        }

        $image = '-';
        if( !empty($item['image_arr']) ){
            $image = '<span class="gbtn"><a href="'.URL.'payments/showPicture/'.$item['id'].'" target="_blank" data-plugins="dialog" class="btn btn-no-padding btn-blue"><i class="icon-eye"></i></a></span>';
        }

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'"">'.

            '<td class="date">'.$dateStr.'</td>'.

            '<td class="name">'.
                '<div class="ellipsis"><a title="'.$item['code'].'" class="fwb" href="'.URL.'payments/'.$item['order_id'].'">'.(!empty($item['code']) ? $item['code'] : "-").'</a></div>'.
                '<div class="date-float fsm fcg">Name of shop / customer: '.$item['cus_name'].'</div>'.
            '</td>'.

            '<td class="status">'.$image.'</td>'.

            '<td class="contact">'.$type.'</td>'.

            '<td class="price">'.$item['amount'].'</td>'.

        '</tr>';
    }
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';
