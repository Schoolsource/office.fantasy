<?php
//print_r($this->results['lists']); die;
$tr = "";
$tr_total = "";

if( !empty($this->results['lists']) ){
    //print_r($this->results); die;

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) {

        $cls = $i%2 ? 'even' : "odd";

        $code = '';
        if( !empty($item['code']) ){
            $code = ' ('.$item['code'].')';
        }

        $address = '';
        if( !empty($item['address']) ){
            $address .= $item['address'];
        }
        if( !empty($item['street']) ){
            $address .= ' <span class="fwb">Road </span>'.$item['street'];
        }
        if( !empty($item['supdistrict']) ){
            $address .= ' <span class="fwb">District </span>'.$item['supdistrict'];
        }
        if( !empty($item['district']) ){
            $address .= ' <span class="fwb">Area </span>'.$item['district'];
        }
        if( !empty($item['province_name']) ){
            $address .= ' <span class="fwb">Province </span>'.$item['province_name'];
        }
        if( !empty($item['zip']) ){
            $address .= ' <span class="fwb">'.$item['zip'].'</span>';
        }

        $option = "";
        foreach ($this->status as $key => $value) {
            $sel = "";
            if( $item["status"] == $value["id"] ){
                $sel = ' selected="1"';
            }
            $option.= '<option'.$sel.' value="'.$value['id'].'">'.$value['name'].'</option>';
        }
        $status = '<select class="inputtext" data-plugins="_update" data-options="'.$this->fn->stringify(array('url' => URL. 'suppliers/setdata/'.$item['id'].'/status')).'">'.$option.'</select>';

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'"">'.

            '<td class="name">'.
                '<div class="ellipsis"><a title="'.$item['name'].'" class="fwb" href="'.URL.'suppliers/'.$item['id'].'">'.$item['name'].$code.'</a></div>'.
                // '<div class="date-float fsm fcg">ชื่อผู้ติดต่อ: '.$item['sup_contact'].'</div>'.
            '</td>'.

            '<td class="address">'.(!empty($address) ? $address : "-").'</td>'.

            '<td class="status_str">'.$item['country_name'].'</td>'.

            // '<td class="contact">'.( $item['sup_contact']!=" " ? $item['sup_contact'] : "-" ).'</td>'.

            // '<td class="address"></td>'.

            '<td class="phone_str">'.(!empty($item['mobile_phone']) ? $item['mobile_phone'] : "-").'</td>'.

            '<td class="phone_str">'.(!empty($item['phone']) ? $item['phone'] : "-").'</td>'.

            // '<td class="phone_str">'.(!empty($item['fax']) ? $item['fax'] : "-").'</td>'.

            '<td class="status_str">'.$status.'</td>'.

            '<td class="actions">'.
                '<div class="group-btn whitespace">'.
                    '<span class="gbtn">'.
                        '<a href="'.URL.'suppliers/edit/'.$item['id'].'" data-plugins="dialog" class="btn btn-no-padding btn-orange"><i class="icon-pencil"></i></a>'.
                    '</span>'.
                    '<span class="gbtn">'.
                        '<a href="'.URL.'suppliers/del/'.$item['id'].'" data-plugins="dialog" class="btn btn-no-padding btn-red"><i class="icon-trash"></i></a>'.
                    '</span>'.
                '</div>'.
            '</td>'.

        '</tr>';
    }
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';
