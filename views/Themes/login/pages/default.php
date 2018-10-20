<?php

$f = new Form();
$form = $f->create(); 

    // attr, options
$form   ->addClass('login-form-container form-insert form-large')
        ->method('post')
        ->url( $this->redirect );

    // set field
$form   ->field("email")
        ->label('<i class="icon-user"></i>')
        ->placeholder("Username")
        ->addClass('inputtext')
        ->required(true)
        ->autocomplete("off");

if( (!empty($this->post['email']) && !empty($this->error['email'])) || empty($this->post['email']) ){
$form   ->attr('autofocus', '1');
}

$form   ->value( !empty($this->post['email'])? $this->post['email'] : '' )
        ->notify( !empty($this->error['email']) ? $this->error['email'] : '' );

$form   ->field("pass")
        ->label('<i class="icon-key"></i>')
        ->type('password')
        ->required(true);

if( (!empty($this->post['email']) && empty($this->error['email'])) ){
$form   ->attr('autofocus', '1');
}

$form   ->addClass('inputtext')
        ->placeholder("Password")
        ->notify( !empty($this->error['pass']) ? $this->error['pass'] : '' );


if( !empty($this->captcha) ){

    $form->field("captcha")
    ->text('<div class="g-recaptcha" data-sitekey="'.RECAPTCHA_SITE_KEY.'"></div>')
    ->notify( !empty($this->error['captcha']) ? $this->error['captcha'] : '' );

}

$form->hr( !empty($this->next) ? '<input type="hidden" autocomplete="off" value="'.$this->next .'" name="next">': '' )

// ->hr('<input type="hidden" autocomplete="off" value="1" name="path_admin">' )

->submit()
->addClass('btn btn-blue btn-large')
->value('Log In');


echo '<div class="bgs">'.
'<div class="VYMape" aria-hidden="true"><svg jsname="BUfzDd" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 810" preserveAspectRatio="xMinYMin slice" aria-hidden="true"><path fill="#efefee" d="M592.66 0c-15 64.092-30.7 125.285-46.598 183.777C634.056 325.56 748.348 550.932 819.642 809.5h419.672C1184.518 593.727 1083.124 290.064 902.637 0H592.66z"></path><path fill="#f6f6f6" d="M545.962 183.777c-53.796 196.576-111.592 361.156-163.49 490.74 11.7 44.494 22.8 89.49 33.1 134.883h404.07c-71.294-258.468-185.586-483.84-273.68-625.623z"></path><path fill="#f7f7f7" d="M153.89 0c74.094 180.678 161.088 417.448 228.483 674.517C449.67 506.337 527.063 279.465 592.56 0H153.89z"></path><path fill="#fbfbfc" d="M153.89 0H0v809.5h415.57C345.477 500.938 240.884 211.874 153.89 0z"></path><path fill="#ebebec" d="M1144.22 501.538c52.596-134.583 101.492-290.964 134.09-463.343 1.2-6.1 2.3-12.298 3.4-18.497 0-.2.1-.4.1-.6 1.1-6.3 2.3-12.7 3.4-19.098H902.536c105.293 169.28 183.688 343.158 241.684 501.638v-.1z"></path><path fill="#e1e1e1" d="M1285.31 0c-2.2 12.798-4.5 25.597-6.9 38.195C1321.507 86.39 1379.603 158.98 1440 257.168V0h-154.69z"></path><path fill="#e7e7e7" d="M1278.31,38.196C1245.81,209.874 1197.22,365.556 1144.82,499.838L1144.82,503.638C1185.82,615.924 1216.41,720.211 1239.11,809.6L1439.7,810L1439.7,256.768C1379.4,158.78 1321.41,86.288 1278.31,38.195L1278.31,38.196z"></path></svg></div>'.
'</div>';

$title = $this->getPage('title');
$name = $this->getPage('name');
$image = $this->getPage('image');
?>

<div class="section">
    <div class="content-wrapper<?=!empty($this->captcha)? ' has-captcha':''?>">

        <div class="login-header-bar login-logo">
            <div class="text">
                <?php if( !empty($image) ){ ?><div class="pic"><img src="http://fantasy.co.th/images/logo_site_1.jpg"></div><?php } ?>
                <h2><?= !empty( $title ) ? $title :''?></h2>
            </div>

            <div class="subtext mvm"></div>

            
        </div>
        <!-- end: login-header -->

        <div class="login-container-wrapper auth-box">
            <div class="login-container">
                <div class="login-title"><span class="fwb">Login</span></div>
                <?=$form->html()?>
            </div>

        </div>
        <!-- end: login-container-wrapper -->

        <div class="login-footer-text">
            <!-- <a href="<?=URL?>"><i class="icon-home mrs"></i><span>Back To Home</span></a><span class="mhm">·</span> -->
            <!-- <a href="<?=URL?>forgot_password" class="forgot_password"><span>Forgot password?</span></a> -->
        </div>
        <!-- end: login-footer -->
        
    </div>
    <!-- end: content-wrapper -->

</div>
<!-- /section -->


<div class="fixed bottom right">
    <!-- <a  href="<?=URL?>?login_mode=pin" class="btn btn-blue">PIN Login</a> -->
</div>