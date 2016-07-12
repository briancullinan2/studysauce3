<?php
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var GlobalVariables $app */
/** @var $view TimedPhpEngine */
/** @var $user User */
/** @var string $code */
$context = !empty($context) ? $context : jQuery($this);
$tab = $context->filter('.panel-pane');

$httpRequest = $app->getRequest();
$cookie = $httpRequest->cookies->get('hasChild');
$hasChild = (empty($cookie) && $cookie !== 'false') || $cookie == 'true';

// update existing tab
if($tab->length > 0) {
    $tab->find('[name="first"]')->attr('placeholder', implode('', [$hasChild ? 'Parent first' : 'First', ' name']));
    $tab->find('[name="last"]')->attr('placeholder', implode('', [$hasChild ? 'Parent last' : 'Last', ' name']));
    $tab->find('[type="submit"]')->text($hasChild ? 'Next' : 'Register');
}
else {

    $view->extend('StudySauceBundle:Shared:dashboard.html.php');

    $view['slots']->start('stylesheets');
    foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/account.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url) { ?>
        <link type="text/css" rel="stylesheet" href="<?php echo ($view->escape($url)); ?>" />
    <?php }
    $view['slots']->stop();

    $view['slots']->start('javascripts');
    foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/register.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
        <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('body'); ?>

    <div class="panel-pane" id="register">
        <div class="pane-content">
            <h2>Welcome, let&rsquo;s get started.</h2>

            <form action="<?php print ($view['router']->generate('account_create')); ?>" method="post">
                <?php if (!empty($code)) { ?>
                    <input type="hidden" name="_code" value="<?php print ($code); ?>"/>
                <?php } ?>
                <input type="hidden" name="_remember_me" value="on"/>
                <label class="input first"><input type="text" name="first" placeholder="<?php print ($hasChild ? 'Parent first' : 'First'); ?> name"
                                            value="<?php print (isset($first) ? $first : ''); ?>"></label>
                <label class="input last"><input type="text" name="last" placeholder="<?php print ($hasChild ? 'Parent last' : 'Last'); ?> name"
                                            value="<?php print (isset($last) ? $last : ''); ?>"></label>
                <label class="input email"><input type="text" name="email" placeholder="Email"
                                            value="<?php print (isset($email) ? $email : ''); ?>"></label>
                <label class="input password"><input type="password" name="pass" placeholder="Enter password"
                                            value=""></label>
                <input type="hidden" name="csrf_token" value="<?php print ($csrf_token); ?>"/>
                <div class="form-actions highlighted-link invalid">
                    <label class="checkbox hasChild"><input name="hasChild" type="checkbox" value="true" <?php print ($hasChild ? 'checked="checked"' : ''); ?>><i></i><span>A child will be using this account</span></label>
                    <div class="invalid-error">You must complete all fields before moving on.</div>
                    <button type="submit" value="#save-user" class="more"><?php print ($hasChild ? 'Next' : 'Register'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <?php $view['slots']->stop();

    $view['slots']->start('sincludes');
    print ($view['actions']->render(new ControllerReference('StudySauceBundle:Account:registerChild', ['_format' => 'tab']), ['strategy' => 'sinclude']));
    $view['slots']->stop();

}

