<?php
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var GlobalVariables $app */
/** @var $view TimedPhpEngine */
/** @var $user User */
/** @var Payment $payment */

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/account.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/account.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="account">
        <div class="pane-content">
            <h2>Account settings</h2>
            <form action="<?php print $view['router']->generate('account_update'); ?>" method="post">
                <div class="account-info read-only">
                    <div class="first-name">
                        <label class="input"><span>First name</span>
                            <input type="text" placeholder="First name" value="<?php print $user->getFirst(); ?>">
                        </label>
                    </div>
                    <div class="last-name">
                        <label class="input"><span>Last name</span>
                            <input type="text" placeholder="Last name" value="<?php print $user->getLast(); ?>">
                        </label>
                    </div>
                    <div class="email">
                        <label class="input"><span>E-mail address</span>
                            <input type="text" placeholder="Email" value="<?php print $user->getEmail(); ?>" autocomplete="off">
                        </label>
                    </div>
                </div>
                <div class="social-login">
                    <?php foreach($services as $o => $url) {
                        $getter = 'get' . ucfirst($o) . 'AccessToken';
                        ?>
                        <label><span><?php print ($o == 'gcal' ? 'Google Calendar' : ucfirst($o)); ?> account</span>
                        <?php if (!empty($user->$getter())) { ?>
                            Connected <a href="#remove-<?php print $o; ?>"></a>
                        <?php } else { ?>
                            <a href="<?php print $url; ?>?_target=<?php print $view['router']->generate('account'); ?>" class="more">Connect</a></label>
                        <?php }
                    } ?>
                </div>
                <div class="password">
                    <label class="input"><span>Current password</span>
                        <input type="password" placeholder="Enter password" value="">
                    </label>
                </div>
                <div class="new-password">
                    <label class="input"><span>New password</span>
                        <input type="password" placeholder="New password" value="">
                    </label>
                </div>
                <div class="confirm-password">
                    <label class="input"><span>Confirm password</span>
                        <input type="password" placeholder="Confirm password" value="">
                    </label>
                </div>
                <div class="highlighted-link">
                    <div class="edit-icons">
                        <a href="#edit-account">Edit information</a>
                        <a href="#edit-password">Change password</a>
                        <a href="<?php print $view['router']->generate('reset'); ?>">Forgot password</a>
                    </div>
                    <div class="form-actions">
                        <div class="invalid-only">You must complete all fields before moving on.</div>
                        <button type="submit" value="#save-account" class="more">Save</button>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
            </form>
        </div>
    </div>
<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'cancel-confirm']), ['strategy' => 'sinclude']);
$view['slots']->stop();

