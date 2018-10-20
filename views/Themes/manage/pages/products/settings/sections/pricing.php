<?php

$form = new Form();
$form = $form->create()
    // set From
    ->elem('div')
    // ->url( URL.'products/update/' . $this->section )
    // ->method('post')
    ->addClass('js-submit-form');

    $form->field('frontend')
         ->label('Member Price')
         ->addClass('inputtext')
         ->autocomplete('off')
         ->value(!empty($this->item['pricing']['frontend']) ? round($this->item['pricing']['frontend']) : '');

    $form->field('vat')
         ->label('Exclude VAT Price')
         ->addClass('inputtext')
         ->autocomplete('off')
         ->value(!empty($this->item['pricing']['vat']) ? round($this->item['pricing']['vat']) : '');

    $form->field('website')
         ->label('Website Price')
         ->addClass('inputtext')
         ->autocomplete('off')
         ->value(!empty($this->item['pricing']['website']) ? round($this->item['pricing']['website']) : '');

    $form->field('cost')
         ->label('Cost')
         ->addClass('inputtext')
         ->autocomplete('off')
         ->value(!empty($this->item['pricing']['cost']) ? round($this->item['pricing']['cost']) : '');

/* $form 	->field("member")
        ->label("ราคาสมาชิก")
        ->addClass('inputtext')
        ->autocomplete('off')
        ->value( !empty($this->item['pricing']['member']) ? $this->item['pricing']['member'] : '' ); */

echo $form->html();
