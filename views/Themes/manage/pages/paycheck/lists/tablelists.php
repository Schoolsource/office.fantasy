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
        $dateUpStr = date("d/m/Y", strtotime($item['up_date']));

        $image = '';
        if( !empty($item['image_id']) ){
            $image = '<span class="gbtn">'.
                        '<a class="btn btn-blue btn-no-padding" data-plugins="dialog" href="'.URL.'paycheck/showPicture/'.$item['id'].'"><i class="icon-eye"></i></a>'.
                    '</span>';
        }

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'"">'.

            '<td class="date">'.$dateStr.'</td>'.

            '<td class="date">'.$dateUpStr.'</td>'.

            '<td class="name">'.
                '<div class="ellipsis"><a title="'.$item['number'].'" class="fwb" data-plugins="dialog" href="'.URL.'paycheck/edit/'.$item['id'].'">'.(!empty($item['number']) ? $item['number'] : "-").' ('.$item['bank_code'].')</a></div>'.
                '<div class="fsm fcg">ธนาคาร : '.$item['bank_name'].'</div>'.
            '</td>'.

            '<td class="price">'.number_format($item['price'], 2).'</td>'.

            '<td class="contact">'.
                '<a href="'.URL.'suppliers/'.$item['sup_id'].'" class="fwb" target="_blank">'.$item['sup_name'].'</a>'.
            '</td>'.

            '<td class="contact">'.$item['sup_fullname'].'</td>'.

            '<td class="phone_str">'.(!empty($item['sup_phone']) ? $item['sup_phone'] : "-").'</td>'.

            '<td class="actions">
                <div class="group-btn whitespace">'.
                    $image.
                    '<span class="gbtn">'.
                        '<a class="btn btn-primary btn-no-padding" data-taget="'.URL.'paycheck/edit/'.$item['id'].'" href="'.URL.'pdf/costan/'.$item['id'].'"><i class="icon-print"></i></a>'.
                    '</span>'.
                    '<span class="gbtn">'.
                        '<a class="btn btn-orange btn-no-padding" data-plugins="dialog" href="'.URL.'paycheck/edit/'.$item['id'].'"><i class="icon-pencil"></i></a>'.
                    '</span>'.
                    '<span class="gbtn">'.
                        '<a class="btn btn-red btn-no-padding" data-plugins="dialog" href="'.URL.'paycheck/del/'.$item['id'].'"><i class="icon-trash"></i></a>'.
                    '</span>'.
                '</div>
            </td>'.

        '</tr>';
    }
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';