<?php
//print_r($this->results['lists']); die;
$tr = "";
$tr_total = "";

if( !empty($this->results['lists']) ){
    //print_r($this->results); die;

    $seq = 0;
    foreach ($this->results['lists'] as $i => $item) {

        $image = !empty($item['image_url'])
            ? $this->fn->imageBox($item['image_url'], 100)
            : '';

        $cls = $i%2 ? 'even' : "odd";

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'"">'.

            '<td class="status_str">('.$item['category_name_en'].') '.$item['category_name'].'</td>'.

            // '<td class="image">'.$image.'</td>'.

            '<td class="name">'.
                '<div class="ellipsis"><a title="'.$item['pds_name'].'" class="fwb" href="'.URL.'products/settings/basic/'.$item['id'].'">'.(!empty($item['pds_name']) ? $item['pds_name'] : "-").'</a></div>'.
                '<div class="date-float fsm fcg">Add on: '. ( $item['created_at'] != '0000-00-00 00:00:00' ? $this->fn->q('time')->live( $item['created_at'] ):'-' ) .'</div>'.

                '<div class="date-float fsm fcg">Recent changes: '. ( $item['updated_at'] != '0000-00-00 00:00:00' ? $this->fn->q('time')->live( $item['updated_at'] ):'-' ) .'</div>'.

            '</td>'.

            '<td class="price">'.$item['frontend'].'</td>'.
            
            '<td class="price">'.$item['website'].'</td>'.

            '<td class="status">'.$item['pds_comission'].' %</td>'.

            '<td class="status_str">'.( $item['pds_status'] == "A" ? "Active" : "Inactive" ).'</td>'.

            '<td class="status">
                <label class="checkbox"><input'.(!empty($item['pds_is_mobile']) ? ' checked="1"' : ' ').' type="checkbox" name="pds_is_mobile" data-plugins="_update" data-options="'.$this->fn->stringify(array(
                        'url' => URL. 'products/_update/'.$item['id'].'/pds_is_mobile'
                )).'" /></label>
            </td>'.

            '<td class="status">
                <label class="checkbox"><input'.(!empty($item['pds_is_show']) ? ' checked="1"' : ' ').' type="checkbox" name="pds_is_show" data-plugins="_update" data-options="'.$this->fn->stringify(array(
                        'url' => URL. 'products/_update/'.$item['id'].'/pds_is_show'
                )).'" /></label>
            </td>'.

            '<td class="status">
                <label class="checkbox"><input'.(!empty($item['pds_has_vat']) ? ' checked="1"' : ' ').' type="checkbox" name="pds_has_vat" data-plugins="_update" data-options="'.$this->fn->stringify(array(
                        'url' => URL. 'products/_update/'.$item['id'].'/pds_has_vat'
                )).'" /></label>
            </td>'.

            '<td class="status">
                <label class="checkbox"><input'.(!empty($item['pds_is_hot']) ? ' checked="1"' : ' ').' type="checkbox" name="pds_is_hot" data-plugins="_update" data-options="'.$this->fn->stringify(array(
                        'url' => URL. 'products/_update/'.$item['id'].'/pds_is_hot'
                )).'" /></label>
            </td>'.

            '<td class="actions">
                <div class="group-btn whitespace">'.
                    '<span class="gbtn">'.
                    '<a class="btn btn-orange btn-no-padding" data-plugins="dialog" href="'.URL.'products/set_price/'.$item['id'].'"><i class="icon-money"></i></a>'.
                    '</span>'.
                    '<span class="gbtn">'.
                    '<a class="btn btn-green btn-no-padding" data-plugins="dialog" href="'.URL.'products/set_comission/'.$item['id'].'"><i class="icon-money"></i></a>'.
                    '</span>'.
                '</div>
            </td>';

        '</tr>';
    }
}

$table = '<table><tbody>'. $tr. '</tbody>'.$tr_total.'</table>';
