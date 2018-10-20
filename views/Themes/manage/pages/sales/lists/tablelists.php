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

            '<td class="ID">'.$item['sale_code'].'</td>'.
            '<td class="name"><a href="'.URL.'sales/'.$item['id'].'" class="fwb">'.$item['sale_fullname'].' ('.$item['sale_fullname'].')</a></td>'.
            '<td class="status">'.$item['department_arr']['name'].'</td>'.
            '<td class="status">'.$item['total_order'].'</td>'.
            '<td class="status">'.$item['status_arr']['name'].'</td>'.

            '<td class="actions">'.
                '<div class="group-btn whitespace">'.
                    '<span class="gbtn">'.
                      '<a href="'.URL.'sales/change_password/'.$item['id'].'" data-plugins="dialog" class="btn btn-no-padding btn-blue"><i class="icon-key"></i></a>'.
                    '</span>'.
                    '<span class="gbtn">'.
                        '<a href="'.URL.'sales/edit/'.$item['id'].'" data-plugins="dialog" class="btn btn-no-padding btn-orange"><i class="icon-pencil"></i></a>'.
                    '</span>'.
                    '<span class="gbtn">'.
                        '<a href="'.URL.'sales/del/'.$item['id'].'" data-plugins="dialog" class="btn btn-no-padding btn-red"><i class="icon-trash"></i></a>'.
                    '</span>'.
                '</div>'.
            '</td>'.

        '</tr>';
    }
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';
