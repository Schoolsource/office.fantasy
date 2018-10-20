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

            '<td class="ID">'.$item['sub_code'].'</td>'.

            '<td class="name">'.
                '<div class="ellipsis"><a title="'.$item['name_store'].'" class="fwb" href="'.URL.'customers/'.$item['id'].'">'.(!empty($item['name_store']) ? $item['name_store'] : "-").'</a></div>'.
                '<div class="date-float fsm fcg">Add on: '. ( $item['created_at'] != '0000-00-00 00:00:00' ? $this->fn->q('time')->live( $item['created_at'] ):'-' ) .'</div>'.

                '<div class="date-float fsm fcg">Recent changes: '. ( $item['updated_at'] != '0000-00-00 00:00:00' ? $this->fn->q('time')->live( $item['updated_at'] ):'-' ) .'</div>'.

            '</td>'.

            '<td class="contact">('.$item['sale_code'].') '.$item['sale_name'].'</td>'.

            '<td class="email">'.$item['province'].'</td>'.

            '<td class="phone">'.$item['phone'].'</td>'.

            '<td class="status">'.( $item['status'] == "A" ? "Active" : "Inactive" ).'</td>'.

            '<td class="actions">
                <div class="group-btn whitespace">'.
                    '<span class="gbtn">'.
                    '<a class="btn btn-orange btn-no-padding" data-plugins="dialog" href="'.URL.'customers/edit/'.$item['id'].'"><i class="icon-pencil"></i></a>'.
                    '</span>'.
                    '<span class="gbtn">'.
                    '<a class="btn btn-no-padding btn-red" data-plugins="dialog" href="'.URL.'customers/del/'.$item['id'].'"><i class="icon-trash"></i></a>'.
                    '</span>'.
                '</div>
            </td>';

        '</tr>';
    }
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';
