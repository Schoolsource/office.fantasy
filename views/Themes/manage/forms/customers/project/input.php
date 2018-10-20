<?php


if (!empty($this->item)) {
    $arr['title'] = 'Uodate Project';
    $arr['hiddenInput'][] = array('name' => 'id', 'value' => $this->item['project_id']);
} else {
    $arr['title'] = 'New Create Project';
}

$form = new Form();
$form = $form->create()
    // set From
    ->elem('div')
    ->addClass('form-insert');

$form->field('project_name')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->attr('autoselect', 1)
        ->placeholder('Project Name')
        ->value(!empty($this->item['project_name']) ? $this->item['project_name'] : '');

$form->field('project_target')
        ->autocomplete('off')
        ->addClass('inputtext')
        ->attr('autoselect', 1)
        ->placeholder('Target')
        ->type('number')
        ->value(!empty($this->item['project_target']) ? $this->item['project_target'] : '');

// set form
$arr['form'] = '<form class="js-submit-form" method="post" action="'.URL.'customers/saveProject"></form>';

// body
$arr['body'] = $form->html();

// fotter: button
$arr['button'] = '<button type="submit" class="btn btn-primary btn-submit"><span class="btn-text">Save</span></button>';
$arr['bottom_msg'] = '<a class="btn" role="dialog-close"><span class="btn-text">Cancel</span></a>';

echo json_encode($arr);
