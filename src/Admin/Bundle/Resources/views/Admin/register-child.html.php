<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;use StudySauce\Bundle\Entity\User;
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
$hasChild = !empty($cookie) && $cookie == 'true';

// update existing tab

if($tab->length > 0) {
    $tab->find('[type="submit"]')->text($hasChild ? 'Next' : 'Done');
    $results = $tab->find('.results');
    $new = $view->render('AdminBundle:Admin:cell-idSingleCoupon-invite.html.php', ['context' => $results->find('.results-invite'), 'results' => $results->data('results'), 'request' => $results->data('request')]);
    $tab->find('.idSingleCoupon > *')->remove();
    $tab->find('.idSingleCoupon')->append($new);
}
else {

    $view->extend('StudySauceBundle:Shared:dashboard.html.php');

    $view['slots']->start('stylesheets');
    foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/account.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url) { ?>
        <link type="text/css" rel="stylesheet" href="<?php echo($view->escape($url)); ?>"/>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('javascripts');
    foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/register.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
        <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('body'); ?>

    <div class="panel-pane" id="register_child">
        <div class="pane-content">
            <h2>Register a child</h2>
            <form action="<?php print ($view['router']->generate('account_create')); ?>" method="post">
                <?php if (!empty($code)) { ?>
                    <input type="hidden" name="_code" value="<?php print ($code); ?>"/>
                <?php } ?>
                <input type="hidden" name="_remember_me" value="on"/>
                <label class="input childFirst"><input type="text" name="childFirst" placeholder="Child first name"
                                                       value="<?php print (isset($first) ? $first : ''); ?>"></label>
                <label class="input childLast"><input type="text" name="childLast" placeholder="Child last name"
                                                      value="<?php print (isset($last) ? $last : ''); ?>"></label>
                <input type="hidden" name="csrf_token" value="<?php print ($csrf_token); ?>"/>
                <?php
                $request['count-invite'] = 1;
                $request['count-ss_user'] = -1;
                $request['invite-1count-invite'] = 0;
                $request['invite-1new'] = [];
                $request['invite-1invite-properties'] = 's:13:"public_school";b:1;';
                $request['invite-1ss_group-id'] = '!NULL';
                $request['invite-1ss_group-deleted'] = '!1';
                $request['invite-1parent-ss_group-deleted'] = '!1';
                $request['count-ss_group'] = -1;
                $request['new'] = ['invite'];
                $request['edit'] = false;
                $request['read-only'] = false;
                $request['tables'] = (array)(new stdClass());
                $request['tables']['invite'] = ['idSingleCoupon' => ['id', 'first', 'last', 'user', 'invitee', 'email', 'group', 'code']];
                $request['tables']['ss_user'] = ['id' => ['id', 'first', 'last', 'groups']];
                $request['tables']['invite-1'] = ['id' => ['id', 'code', 'group', 'properties']];
                $request['tables']['ss_group'] = ['id' => ['name', 'id', 'parent', 'deleted']];
                $request['classes'] = [];
                $request['headers'] = false;
                $request['footers'] = false;
                if ($tab->length == 0) {
                    print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                } ?>
                <div class="form-actions highlighted-link invalid">
                    <label class="checkbox hasChild"><input name="hasChild" type="checkbox" value="true" <?php print ($hasChild ? 'checked="checked"' : ''); ?>><i></i><span>Register another child</span></label>
                    <div class="invalid-error">You must complete all fields before moving on.</div>
                    <a href="<?php print ($view['router']->generate('home')); ?>">Cancel</a>
                    <button type="submit" value="#save-user"
                            class="more"><?php print ($hasChild ? 'Next' : 'Done'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <?php $view['slots']->stop();
}