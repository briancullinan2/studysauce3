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
                <div class="results">
                <h2>Basic information</h2>
                <div class="ss_user-row read-only">
                    <label class="input first"><span>First name</span>
                        <input name="first" type="text" placeholder="First name"
                               value="<?php print ($user->getFirst()); ?>">
                    </label>
                    <label class="input last"><span>Last name</span>
                        <input name="last" type="text" placeholder="Last name"
                               value="<?php print ($user->getLast()); ?>">
                    </label>
                    <label class="input email"><span>E-mail address</span>
                        <input name="email" type="text" placeholder="Email"
                               value="<?php print ($user->getEmail()); ?>" autocomplete="off">
                    </label>
                </div>
                <label class="input pass"><span>Current password</span>
                    <input name="pass" type="password" placeholder="Enter password" value="">
                </label>
                <label class="input new-password"><span>New password</span>
                    <input name="new-password" type="password" placeholder="New password" value="">
                </label>
                <label class="input confirm-password"><span>Confirm password</span>
                    <input name="confirm-password" type="password" placeholder="Confirm password" value="">
                </label>
                <div class="highlighted-link">
                    <div class="edit-icons">
                        <a href="#edit-account">Edit information</a>
                        <a href="#edit-password">Change password</a>
                        <a href="<?php print ($view['router']->generate('reset')); ?>">Forgot password</a>
                    </div>
                    <div class="form-actions invalid">
                        <div class="invalid-error">You must complete all fields before moving on.</div>
                        <a href="#cancel-edit" class="cancel-edit">Cancel</a>
                        <button type="submit" value="#save-account" class="more">Save</button>
                    </div>
                </div>
                <input type="hidden" name="csrf_token" value="<?php print ($csrf_token); ?>"/>
                </div>
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
                $request['tables']['invite'] = ['idTilesSummary' => ['id', 'first', 'last', 'user', 'invitee', 'email', 'group', 'code', '_code', 'childFirst', 'childLast']];
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