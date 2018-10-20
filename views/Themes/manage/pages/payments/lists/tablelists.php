<?php

//print_r($this->results['lists']); die;
$tr = '';
$tr_total = '';

if (!empty($this->results['lists'])) {
    //print_r($this->results); die;

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) {
        $cls = $i % 2 ? 'even' : 'odd';

        $dateStr = date('d/m/Y', strtotime($item['date']));

        $dis = !empty($item['pay']) ? 'btn-orange' : 'btn-red disabled';

        $icon = $item['net_price'] == $item['pay'] ? 'check' : 'minus';

        $add = $item['net_price'] == $item['pay'] ? 'disabled' : '';
        $net_price = !empty($item['net_price']) ? number_format($item['net_price'], 2) : '-';
        if ($item['process']['id'] == 7) {
            $add = 'disabled';
            $net_price = '<span class="fwb tac pas" style="background-color:'.$item['process']['color'].';color:'.$item['process']['t_color'].'; border-radius: 10px;">ยกเลิก</span>';
        }

        $projectList = '<option value="">-</option>';
        foreach ($this->projectList as $key => $value) {
            $active = $value['project_id'] == $item['project_id'] ? ' selected' : '';

            $disabled = !empty($value['project_enabled']) ? '' : ' disabled';
            if (!empty($active) && !empty($disabled)) {
                $disabled = '';
            }

            if (!empty($disabled)) {
                continue;
            }
            $projectList .= '<option'.$active.$disabled.' value="'.$value['project_id'].'">'.$value['project_name'].'</option>';
        }

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'"">'.

            '<td class="date">'.$dateStr.'</td>'.

            '<td class="name">'.
                '<div class="ellipsis">
                    <a title="'.$item['code'].'" class="fwb" href="'.URL.'payments/'.$item['id'].'">'.(!empty($item['code']) ? $item['code'] : '-').'</a>

                </div>'.
                '<div class="date-float fsm fcg">Add on: '.($item['created_at'] != '0000-00-00 00:00:00' ? $this->fn->q('time')->live($item['created_at']) : '-').'</div>'.

                '<div class="date-float fsm fcg">Recent changes: '.($item['updated_at'] != '0000-00-00 00:00:00' ? $this->fn->q('time')->live($item['updated_at']) : '-').'</div>'.

                // '<div style="margin-top: 2px;"><label style="display:inline-block;padding: 5px 6px;background: #d9d9d9;vertical-align: top;font-size: 11px;">Project:</label><select class="inputtext" data-id="'.$item['id'].'" data-name="project_id" style="padding: 2px 6px;height: auto;line-height: initial;font-size: 12px;display: inline-block;vertical-align: top;">'.$projectList.'</select></div>'.

            '</td>'.

            '<td class="status">'.$item['project'].'</td>'.

            '<td class="contact">('.$item['sale_code'].') '.$item['sale_name'].'</td>'.

            '<td class="address">['.$item['user_code'].'] '.$item['user_name'].'</td>'.

            '<td class="price">'.$net_price.'</td>'.

            '<td class="price">'.(!empty($item['pay']) ? number_format($item['pay'], 2) : '-').'</td>'.

            // '<td class="price tac">'.(!empty($item['balance']) ? number_format($item['balance'], 2) : "-").'</td>'.

            '<td class="status"><i class="icon-'.$icon.'"></i></td>'.

            '<td class="status"><div class="group-btn whitespace">'.
                    '<span class="gbtn">'.
                    '<a class="btn btn-no-padding '.$dis.'" data-plugins="dialog" href="'.URL.'payments/listsPayments/'.$item['id'].'"><i class="icon-eye"></i></a>'.
                    '</span>'.
            '</td>'.

            '<td class="actions">
                <div class="group-btn whitespace">'.
                    '<span class="gbtn">'.
                    '<a class="btn btn-blue btn-no-padding '.$add.'" data-plugins="dialog" href="'.URL.'payments/add/'.$item['id'].'"><i class="icon-money"></i></a>'.
                    '</span>'.
                    /* '<a class="btn btn-blue" data-plugins="dialog" href="'.URL.'pallets/set_warehouse/'.$item['id'].'"><i class="icon-pencil"></i> ตั้งค่าที่ตั้ง</a>'.
                    '<a class="btn btn-no-padding btn-orange" data-plugins="dialog" href="'.URL.'pallets/edit/'.$item['id'].'"><i class="icon-pencil"></i></a>'.
                    '<a class="btn btn-no-padding btn-red" data-plugins="dialog" href="'.URL.'pallets/del/'.$item['id'].'"><i class="icon-trash"></i></a>'. */
                '</div>
            </td>';

        '</tr>';
    }
}

$table = '<table><tbody>'.$tr.'</tbody>'.$tr_total.'</table>';
