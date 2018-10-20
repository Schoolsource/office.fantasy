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

        	'<td class="number">'.$item['number'].'</td>'.

            '<td class="date">'.date("d/m/Y", strtotime( $item["date"] )).'</td>'.

            '<td class="contact">'.$item["code"].'</td>'.

            '<td class="name">'.
                '<div class="ellipsis"><a title="" class="fwb" href="'.URL.'import/set/'.$item["id"].'">'.$item["sup_name"].'</a></div>'.
                '<div class="date-float fsm fcg">Code : '.$item["sup_code"].'</div>'.
            '</td>'.

            '<td class="status">'.number_format($item["total_qty"]).'</td>'.

            '<td class="price">'.number_format($item["total_price"],2).'</td>'.

            '<td class="actions whitespace">
                <span class="gbtn">
                    <a href="'.URL.'import/set/'.$item["id"].'" class="btn btn-orange btn-no-padding"><i class="icon-pencil"></i></a>
                </span>
            </td>'.

        '</tr>';
    }
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';
