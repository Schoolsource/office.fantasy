<?php
//print_r($this->results['lists']); die;
$tr = "";
$tr_total = "";

if( !empty($this->results['lists']) ){
    //print_r($this->results); die;

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) {

        $cls = $i%2 ? 'even' : "odd";

        $invite = "";
        if( empty($item['has_invite']) ){
            if( !empty($item['invite']['orders']) ){
                $orders = $item['invite']['orders'];
                $invite .= '<a href="'.URL.'payments/'.$orders[0]['id'].'" class="fwb" target="_blank"><i class="icon-cube mrs"></i>'.$orders[0]["code"].'</a>';
            }

            if( !empty($item['invite']['customers']) ){
                $customer = $item['invite']['customers'];
                $invite .= !empty($invite) ? '<br/>' : '';
                $invite .= '<i class="icon-user mrs"></i>'.$customer[0]['name'];
            }

            if( !empty($item['invite']['suppliers']) ){
                $supplier = $item['invite']['suppliers'];
                $invite .= !empty($invite) ? "<br/>" : '';
                $invite .= '<a href="'.URL.'suppliers/'.$supplier[0]['id'].'" class="fwb" target="_blank"><i class="icon-handshake-o mrs"></i>'.$supplier[0]['name'].' (Supplier)</a>';
            }
        }
        else{
            $invite .= '<span class="gbtn"><a href="'.URL.'events/listsInvite/'.$item['id'].'" class="btn btn-blue btn-no-padding"><i class="icon-eye"></i></a></span>';
        }

        $time = date("H:i", strtotime($item['start']));
        if( $time == '00:00' ){
            $time = '(all day)';
        }

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'"">'.

            '<td class="date">'.date("d/m/Y", strtotime($item['start'])).' '.$time.'</td>'.

            //href="'.URL.'events/'.$item['id'].'"

            '<td class="name">'.
                '<div class="ellipsis"><a title="'.$item['title'].'" class="fwb">'.(!empty($item['title']) ? $item['title'] : "-").'</a></div>'.
                // '<div class="fsm fcg">ธนาคาร : '.$item['bank_name'].'</div>'.
            '</td>'.

            '<td class="contact">'.$invite.'</td>'.

            '<td class="contact">'.$item['location'].'</td>'.

            '<td class="contact">'.
                '<div class="ellipsis">'.
                    '<i class="icon-user-circle-o mrs"></i> '.$item['user_name'].
                '</div>'.
                '<div class="fsm fcg">'.$this->fn->q('time')->live($item['created']).'</div>'.
            '</td>'.

            '<td class="actions">
                <div class="group-btn whitespace">'.
                    '<span class="gbtn">'.
                        '<a class="btn btn-orange btn-no-padding" data-plugins="dialog" href="'.URL.'events/edit/'.$item['id'].'"><i class="icon-pencil"></i></a>'.
                    '</span>'.
                    '<span class="gbtn">'.
                        '<a class="btn btn-red btn-no-padding" data-plugins="dialog" href="'.URL.'events/del/'.$item['id'].'"><i class="icon-trash"></i></a>'.
                    '</span>'.
                '</div>
            </td>'.

        '</tr>';
    }
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';
