<?php

$f = new Form();
$form = $f->create()
    
    // attr, options
    ->addClass('login-form-container form-insert form-large')
    ->method('post')
    ->url( $this->redirect )

    // set field
    ->field("email")
        ->placeholder("Username")
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off")
        ->value( !empty($this->post['email'])? $this->post['email'] : '' )
        ->notify( !empty($this->error['email']) ? $this->error['email'] : '' )

    ->field("pass")
        ->type('password')
        ->required(true)
        ->addClass('inputtext')
        ->placeholder("Password")
        ->notify( !empty($this->error['pass']) ? $this->error['pass'] : '' );


    if( !empty($this->captcha) ){

    $form->field("captcha")
        ->text('<div class="g-recaptcha" data-sitekey="'.RECAPTCHA_SITE_KEY.'"></div>')
        ->notify( !empty($this->error['captcha']) ? $this->error['captcha'] : '' );

    }

    $form->hr( !empty($this->next)
        ? '<input type="hidden" autocomplete="off" value="'.$this->next .'" name="next">' 
        : ''
    )

    ->hr('<input type="hidden" autocomplete="off" value="1" name="path_admin">' )

    ->submit()
        ->addClass('btn btn-blue btn-large')
        ->value('Log In');

?>

<div class="bgs "><div class="bg" style="background-image: url(<?=IMAGES?>carousel/c4.jpg);display: block;"></div></div>

<div class="section">
    <div class="content-wrapper<?=!empty($this->captcha)? ' has-captcha':''?>">

        <div class="login-header-bar login-logo">
            <div class="text">
                <h2><?= !empty($this->system['title']) ? $this->system['title']:'Login' ?></h2>
                <!-- <a><i class="icon-home mrs"></i>Back To Home</a> -->
            </div>
            <?php if( !empty($this->system['name']) ){
                echo '<div class="subtext mvm"><span>'.$this->system['name'].'</span></div>';
            }
            ?>
        </div>
        <!-- end: login-header -->

        <div class="login-container-wrapper auth-box">
            <div class="login-container">
                <div class="login-title hidden_elem"><span class="fwb"></span></div>
                <?=$form->html()?>
            </div>

        </div>
        <!-- end: login-container-wrapper -->

        <div class="login-footer-text">
            <!-- <a href="<?=URL?>" class="forgot_password">Back To Home</a> -->
            <!-- <a href="<?=URL?>users/password/new" class="forgot_password">Forgot password?</a> -->
        </div>
        <!-- end: login-footer -->
        
    </div>
<!-- end: content-wrapper -->

</div>
<!-- /section -->

