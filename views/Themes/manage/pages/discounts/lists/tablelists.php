<?php
//print_r($this->results['lists']); die;
$tr = "";
$tr_total = "";

if( !empty($this->results['lists']) ){
    //print_r($this->results); die;

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) {

        $cls = $i%2 ? 'even' : "odd";

        $dateStr = date("d/m/Y", strtotime($item['created']));

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'"">'.

            '<td class="date">'.$dateStr.'</td>'.

            '<td class="name">'.
                '<div class="ellipsis"><a title="'.$item['name'].'" class="fwb" href="'.URL.'discounts/'.$item['id'].'">'.(!empty($item['name']) ? $item['name'] : "-").'</a></div>'.
                '<div class="date-float fsm fcg">Add on: '. ( $item['created'] != '0000-00-00 00:00:00' ? $this->fn->q('time')->live( $item['created'] ):'-' ) .'</div>'.

                '<div class="date-float fsm fcg">Recent changes: '. ( $item['updated'] != '0000-00-00 00:00:00' ? $this->fn->q('time')->live( $item['updated'] ):'-' ) .'</div>'.

            '</td>'.

            '<td class="price" style="text-align:center;">'.(!empty($item['item']) ? number_format($item['item']) : "-").'</td>'.

            '<td class="actions">
                <div class="group-btn whitespace">'.
                    '<a class="btn btn-no-padding btn-orange" href="'.URL.'discounts/edit/'.$item['id'].'"><i class="icon-pencil"></i></a>'.
                    '<a class="btn btn-no-padding btn-red" data-plugins="dialog" href="'.URL.'discounts/del/'.$item['id'].'"><i class="icon-trash"></i></a>'.
                '</div>
            </td>';

        '</tr>';
    }
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';
