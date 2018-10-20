<?php

$th = "";
$tr = array();
$cal = 0;
foreach ($this->tabletitle as $key => $value) {

	if( !empty($value['subtext']) ){
		$value['key'] .= ' sub';
	}

    $th .= '<th class="'.$value['key'].'" data-col="'.$cal.'">'.

        ( !empty($value['sort'])
            ? '<span class="hdr-text sorttable"><a class="link-sort mrs" data-plugins="tooltip" data-options="'.$this->fn->stringify(array('text'=>'เรียงลำดับ '.$value['text'])).'" data-sort-val="'.$value['sort'].'">'.$value['text'].'</a><i class="icon-long-arrow-up up"></i><i class="icon-long-arrow-down down"></i></span>'
            : '<span class="hdr-text">'.$value['text'].'</span>'
        ).

        ( !empty($value['subtext'])
            ? '<span class="hdr-subtext">('.$value['subtext'].')</span>'
            : ''
        ).

    '</th>';

    $cal++;
}


$tabletitle = "<table><tbody><tr>{$th}</tr></tbody></table>";
