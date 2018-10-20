<?php
//print_r($this->results['lists']); die;
$tr = "";
$tr_total = "";

if( !empty($this->results['lists']) ){
    //print_r($this->results); die;

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) {

        $cls = $i%2 ? 'even' : "odd";

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'"">'.

            '<td class="number" style="text-align:center;">'.$item["number"].'</td>'.

            '<td class="number">'.$item["sub_code"].'</td>'.

            '<td class="name">'.
                '<div class="ellipsis"><a title="'.$item['name_store'].'" class="fwb" href="'.URL.'bills/edit/'.$item['id'].'">'.(!empty($item['name_store']) ? $item['name_store'] : "-").'</a></div>'.
                '<div class="date-float fsm fcg">Add on: '. ( $item['created'] != '0000-00-00 00:00:00' ? $this->fn->q('time')->live( $item['created'] ):'-' ) .'</div>'.

                '<div class="date-float fsm fcg">Recent changes: '. ( $item['updated'] != '0000-00-00 00:00:00' ? $this->fn->q('time')->live( $item['updated'] ):'-' ) .'</div>'.

            '</td>'.

            '<td class="status">'.$item["term_of_payment_arr"]["name"].'</td>'.

            '<td class="price fwb">'.number_format($item['total'],2).'</td>'.
            '<td class="price">'.number_format($item['vat'],2).'</td>'.
            '<td class="price fwb">'.number_format($item['amount'],2).'</td>'.

            '<td class="status">'.$item['vat_persent'].'%</td>'.

            '<td class="actions">
                <div class="group-btn whitespace">'.
                    '<a class="btn btn-green" href="'.URL.'pdf/vat_sale/'.$item['id'].'?type=copy" target="_blank"><i class="icon-file-pdf-o mrs"></i>Copy</a>'.
                    '<a class="btn btn-blue" href="'.URL.'pdf/vat_sale/'.$item['id'].'?type=full" target="_blank"><i class="icon-file-pdf-o mrs"></i>MS</a>'.
                    '<a class="btn" href="'.URL.'pdf/vat_sale/'.$item['id'].'?type=slip" target="_blank"><i class="icon-file-pdf-o mrs"></i>SLIP</a>'.
                    '<a class="btn btn-no-padding btn-orange" href="'.URL.'bills/edit/'.$item['id'].'"><i class="icon-pencil"></i></a>'.
                    // '<a class="btn btn-no-padding btn-red" data-plugins="dialog" href="'.URL.'bills/del/'.$item['id'].'"><i class="icon-trash"></i></a>'.
                '</div>
            </td>';

        '</tr>';
    }
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';
