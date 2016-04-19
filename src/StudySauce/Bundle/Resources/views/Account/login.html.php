<?php
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var $view TimedPhpEngine */
/** @var $user User */

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets([
        '@StudySauceBundle/Resources/public/css/account.css'
    ], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):
    ?><link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts([
        '@StudySauceBundle/Resources/public/js/login.js'
    ], [], ['output' => 'bundles/studysauce/js/*.js']
) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>

<div class="panel-pane" id="login">
    <div class="pane-content">
        <?php if(!empty($error)) { ?>
            <h2>Account not found.&nbsp; Please register or log in with email.</h2>
            <div class="social-login highlighted-link">
                <a href="<?php print $view['router']->generate('register'); ?>" class="more">Register</a>
            </div>
        <?php } else { ?>
            <h2>Welcome back!</h2>
            <div class="social-login">
                <?php foreach($services as $o => $url) {
                    if($o == 'evernote' || $o == 'gcal')
                        continue;
                    ?>
                    <a href="<?php print $url; ?>" class="more">Sign in</a>
                <?php } ?>
            </div>
        <?php } ?>
        <div class="signup-or"><span>Or</span></div>
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
                <a href="<?php print $view['router']->generate('reset'); ?>">Forgot password?</a>
                <div class="invalid-only">You must complete all fields before moving on.</div>
                <button type="submit" value="#user-login" class="more">Sign in</button>
            </div>
        </form>
        <?php if(!empty($error)) { ?>
            <div><br /><br /><br /><br /><br />* Note - You can connect with Facebook or Google once you log in.</div>
        <?php } ?>
    </div>
</div>

<?php $view['slots']->stop();

$view['slots']->start('sincludes');

$view['slots']->stop();
