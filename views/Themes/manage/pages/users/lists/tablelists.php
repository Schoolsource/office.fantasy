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

            '<td class="ID">'.$item['id'].'</td>'.
            '<td class="name">'.$item['name'].'</td>'.
            '<td class="email">'.$item['email'].'</td>'.


            '<td class="actions">'.
                '<div class="group-btn whitespace">'.
                    '<span class="gbtn">'.
                      '<a href="'.URL.'users/change_password/'.$item['id'].'" data-plugins="dialog" class="btn btn-no-padding btn-blue"><i class="icon-key"></i></a>'.
                    '</span>'.
                    '<span class="gbtn">'.
                        '<a href="'.URL.'users/edit/'.$item['id'].'" data-plugins="dialog" class="btn btn-no-padding btn-orange"><i class="icon-pencil"></i></a>'.
                    '</span>'.
                    '<span class="gbtn">'.
                        '<a href="'.URL.'users/del/'.$item['id'].'" data-plugins="dialog" class="btn btn-no-padding btn-red"><i class="icon-trash"></i></a>'.
                    '</span>'.
                '</div>'.
            '</td>'.

        '</tr>';
    }
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';
