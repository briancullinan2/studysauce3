<?php $view->extend('AdminBundle:Admin:dialog.html.php');

$view['slots']->start('modal-header') ?>
    No account found. Please register or log in with email
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<div class="social-login highlighted-link">
    <a href="<?php print $view['router']->generate('register'); ?>" class="more">Register</a>
</div>
<form action="<?php print $view['router']->generate('account_auth'); ?>" method="post">
    <input type="hidden" name="_remember_me" value="on" />
    <div class="email">
        <label class="input"><input type="text" placeholder="Email" value="<?php print (isset($email) ? $email : ''); ?>"></label>
    </div>
    <div class="password">
        <label class="input"><input type="password" placeholder="Password" value=""></label>
    </div>
    <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
    <div class="form-actions highlighted-link invalid">
        <br />
        <a href="<?php print $view['router']->generate('reset'); ?>">Forgot password?</a>
        <div class="invalid-only">You must complete all fields before moving on.</div>
        <button type="submit" value="#user-login" class="more">Sign in</button>
        <br />
        <br />
    </div>
</form>
<div>* Note - You can connect with Facebook or Google once you log in.</div>
<?php $view['slots']->stop();
