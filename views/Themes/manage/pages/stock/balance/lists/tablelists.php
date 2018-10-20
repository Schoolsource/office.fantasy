<?php

$tr = '';
$tr_total = '';

if (!empty($this->results['items'])) {
    $seq = 0;
    foreach ($this->results['items'] as $i => $item) {
        ++$seq;
        $cls = $i % 2 ? 'even' : 'odd';

        $input = $item['receive'] + $item['adjust'];
        // $balance = $input - $item['output'];
        $balance = $item['receive'] - $item['output'] - $item['adjust'];

        $tr .= '<tr class="'.$cls.'" data-id="'.$item['id'].'"">'.

            '<td class="td-seq">'.$seq.'</td>'.

            '<td class="name">'.$item['name'].

                '<div class="fsm fcg">'.
                    (!empty($item['category_name_en']) ? $item['category_name_en'] : '').
                    (!empty($item['category_name']) ? '('.$item['category_name'].')' : '').
                '</div>'.

            '</td>'.

            '<td class="status">'.(!empty($item['vat']) ? '<span style="color:#792dff">✔</span>' : '<span style="color:#FF5722">✖</span>').'</td>'.

            '<td class="price td-price">'.($item['receive'] == 0 ? '-' : number_format($item['receive'])).'</td>'.
            '<td class="price td-price">'.($item['adjust'] == 0 ? '-' : number_format($item['adjust'])).'</td>'.

            // '<td class="price td-price td-input">'. ($input==0? '-': number_format($input) ).'</td>'.
            '<td class="price td-price td-output">'.($item['output'] == 0 ? '-' : number_format($item['output'])).'</td>'.
            '<td class="price td-price td-balance'.($balance < 0 ? ' td-minus' : '').'">'.($balance == 0 ? '-' : number_format($balance)).'</td>';

        '</tr>';
    }
}

$table = '<table><tbody>'.$tr.'</tbody>'.$tr_total.'</table>';
