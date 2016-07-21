<?php
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var GlobalVariables $app */
/** @var $view TimedPhpEngine */
/** @var $user User */
$user = $app->getUser();
/** @var Payment $payment */
$context = !empty($context) ? $context : jQuery($this);
$tab = $context->filter('.panel-pane');

if($tab->length == 0) {

    $view->extend('StudySauceBundle:Shared:dashboard.html.php');

    $view['slots']->start('stylesheets');
    foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/account.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url) { ?>
        <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('javascripts');
    foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/account.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
        <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('body'); ?>
    <div class="panel-pane" id="account">
        <div class="pane-content">
            <form action="<?php print ($view['router']->generate('account_update')); ?>" method="post">

                <h2>Basic information</h2>
                <div class="account-info read-only">
                    <div class="first">
                        <label class="input"><span>First name</span>
                            <input name="first" type="text" placeholder="First name"
                                   value="<?php print ($user->getFirst()); ?>">
                        </label>
                    </div>
                    <div class="last">
                        <label class="input"><span>Last name</span>
                            <input name="last" type="text" placeholder="Last name"
                                   value="<?php print ($user->getLast()); ?>">
                        </label>
                    </div>
                    <div class="email">
                        <label class="input"><span>E-mail address</span>
                            <input name="email" type="text" placeholder="Email"
                                   value="<?php print ($user->getEmail()); ?>" autocomplete="off">
                        </label>
                    </div>
                </div>
                <div class="social-login">
                    <?php foreach ($services as $o => $url) {
                        $getter = implode('', ['get' , ucfirst($o) , 'AccessToken']);
                        ?>
                        <label><span><?php print ($o == 'gcal' ? 'Google Calendar' : ucfirst($o)); ?> account</span>
                        <?php if (!empty($user->$getter())) { ?>
                            Connected <a href="#remove-<?php print ($o); ?>"></a>
                        <?php } else { ?>
                            <a href="<?php print ($url); ?>?_target=<?php print ($view['router']->generate('account')); ?>"
                               class="more">Connect</a></label>
                        <?php }
                    } ?>
                </div>
                <div class="password">
                    <label class="input"><span>Current password</span>
                        <input name="password" type="password" placeholder="Enter password" value="">
                    </label>
                </div>
                <div class="new-password">
                    <label class="input"><span>New password</span>
                        <input name="new-password" type="password" placeholder="New password" value="">
                    </label>
                </div>
                <div class="confirm-password">
                    <label class="input"><span>Confirm password</span>
                        <input name="confirm-password" type="password" placeholder="Confirm password" value="">
                    </label>
                </div>
                <div class="highlighted-link">
                    <div class="edit-icons">
                        <a href="#edit-account">Edit information</a>
                        <a href="#edit-password">Change password</a>
                        <a href="<?php print ($view['router']->generate('reset')); ?>">Forgot password</a>
                    </div>
                    <div class="form-actions">
                        <div class="invalid-error">You must complete all fields before moving on.</div>
                        <button type="submit" value="#save-account" class="more">Save</button>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php print ($csrf_token); ?>"/>

                <h2>Child information</h2>
                <?php
                $request['count-invite'] = 0;
                $request['count-ss_user'] = -1;
                $request['invite-1invite-properties'] = 's:13:"public_school";b:1;';
                $request['invite-1ss_group-id'] = '!NULL';
                $request['invite-1ss_group-deleted'] = '!1';
                $request['invite-1parent-ss_group-deleted'] = '!1';
                $request['count-ss_group'] = -1;
                $request['ss_user-id'] = $user->getId();
                $request['invitee-ss_user-id'] = '!NULL';
                $request['tables'] = (array)(new stdClass());
                $request['tables']['invite'] = ['idTilesSummary' => ['id', 'first', 'last', 'user', 'invitee', 'email', 'group', 'code']];
                $request['tables']['ss_user'] = ['id' => ['id', 'first', 'last', 'groups']];
                $request['tables']['invite-1'] = ['id' => ['id', 'code', 'group', 'properties']];
                $request['tables']['ss_group'] = ['id' => ['name', 'id', 'parent', 'deleted']];
                $request['classes'] = ['tiles', 'summary'];
                $request['headers'] = false;
                $request['footers'] = false;
                if ($tab->length == 0) {
                    print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                } ?>
            </form>
        </div>
    </div>
    <?php $view['slots']->stop(); ?>

    <?php $view['slots']->start('sincludes');

    $view['slots']->stop();

}