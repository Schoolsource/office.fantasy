<?php
$i = 0;
$tr = '';
foreach ($this->pageMenu as $key => $value) {
    $menu = '';
    $checked = '';
    $ck_add = '';
    $ck_edit = '';
    $ck_del = '';
    $hidden = '';
    ++$i;

    if (!empty($this->item['permission'][$value['key']])) {
        foreach ($this->item['permission'][$value['key']] as $key => $val) {
            if (!empty($val)) {
                if ($key == 'view') {
                    $checked = ' checked="1"';
                }

                if ($key == 'add') {
                    $ck_add = ' checked="1"';
                }

                if ($key == 'edit') {
                    $ck_edit = ' checked="1"';
                }

                if ($key == 'del') {
                    $ck_del = ' checked="1"';
                }
            }
        }
    }
    // $menu .= '<div class="fullname fwb">'.$value['name'].'</div>';

    if ($value['key'] == 'comission' || $value['key'] == 'revenue' || $value['key'] == 'vatbuy' || $value['key'] == 'vatsale' || $value['key'] == 'due' || $value['key'] == 'project') {
        $hidden .= '<input type="hidden" name="permission['.$value['key'].'][view]" value="0">';

        $menu .= '<td class="status">'.$hidden.'<label class="checkbox"><input'.$checked.' type="checkbox" name="permission['.$value['key'].'][view]" value="1"></label></td>';
        $menu .= '<td class="status"></td>';
        $menu .= '<td class="status"></td>';
        $menu .= '<td class="status"></td>';
    } else {
        /* default */
        $hidden .= '<input type="hidden" name="permission['.$value['key'].'][view]" value="0">';
        $hidden .= '<input type="hidden" name="permission['.$value['key'].'][add]" value="0">';
        $hidden .= '<input type="hidden" name="permission['.$value['key'].'][edit]" value="0">';
        $hidden .= '<input type="hidden" name="permission['.$value['key'].'][del]" value="0">';

        /* Form */
        $menu .= '<td class="status">'.$hidden.'<label class="checkbox"><input'.$checked.' type="checkbox" name="permission['.$value['key'].'][view]" value="1"></label></td>';
        $menu .= '<td class="status"><label class="checkbox"><input'.$ck_add.' type="checkbox" name="permission['.$value['key'].'][add]" value="1"></label></td>';
        $menu .= '<td class="status"><label class="checkbox"><input'.$ck_edit.' type="checkbox" name="permission['.$value['key'].'][edit]" value="1"></label></td>';
        $menu .= '<td class="status"><label class="checkbox"><input'.$ck_del.' type="checkbox" name="permission['.$value['key'].'][del]" value="1"></label></td>';
    }

    $tr .= '<tr>'.
        // '<td class="ID"></td>'.
        '<td class="name">'.$i.'. '.$value['name'].'</td>'.
        $menu.
    '</tr>';
}

// body
$arr['body'] = '<table class="table-permit">'.
    '<thead>'.
        '<tr>'.
            // '<th class="ID" rowspan="2">#</th>'.
            '<th class="name" rowspan="2">Menu</th>'.
            '<th class="status" colspan="4">Access</th>'.
        '</tr>'.
        '<tr>'.
            '<th class="status">View</th>'.
            '<th class="status">Add</th>'.
            '<th class="status">Edit</th>'.
            '<th class="status">Delete</th>'.
        '</tr>'.
    '</thead>'.

    '<tbody>'.$tr.'</tbody>'.
'</table>';

/*$form   ->field($value['name'])
            ->label($value['name'])
            ->text( $menu );*/

// set form
$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL.'admins/permission"></form>';

$name = $this->item['name'];

// title
$arr['title'] = 'Set Permission Access';
$arr['hiddenInput'][] = array('name' => 'id', 'value' => $this->item['id']);
// $arr['hiddenInput'][] = array('name'=>'section','value'=>'permit');
// $arr['hiddenInput'][] = array('name'=>'type','value'=>$_REQUEST['type']);

// fotter: button
$arr['width'] = 620;
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">Save</span></button>';
$arr['bottom_msg'] = '<a class="btn btn-red" role="dialog-close"><span class="btn-text">Cancel</span></a>';

echo json_encode($arr);
