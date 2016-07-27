<?php

use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var \Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables $app */
$user = $app->getUser();
$context = !empty($context) ? $context : jQuery($this);
$tab = $context->filter('.panel-pane');

if($tab->length == 0) {

    $view->extend('StudySauceBundle:Shared:dashboard.html.php');

    $view['slots']->start('stylesheets');
    foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/buy.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url) { ?>
        <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('javascripts');
    foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/buy.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
        <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('body'); ?>
    <div class="panel-pane" id="thanks">
        <div class="pane-content clearfix">
            <h1>Thank you for your purchase</h1>

            <h2>We have added the new study packs to your account. Please feel free to contact us if you have any questions. Thank you.</h2>
            <div class="highlighted-link">
                <a href="<?php print ($view['router']->generate('home')); ?>" class="more">Go home</a>
                <br />
                <br />
            </div>
            <h2>- The Study Sauce Team</h2>
            <?php
            /*
            $request['count-payment'] = 1;
            $request['count-coupon'] = -1;
            $request['count-pack'] = 0;
            $request['count-invite'] = -1;
            $request['count-file'] = -1;
            $request['count-ss_user'] = -1;
            $request['count-user_pack'] = -1;
            $request['count-ss_group'] = -1;
            $request['read-only'] = false;
            $request['ss_user-id'] = $user->getId();
            $request['invitee-ss_user-id'] = '!NULL';
            $request['tables'] = (array)(new stdClass());
            $request['tables']['payment'] = ['id' => ['created', 'id', 'user', 'coupons', ]];
            $request['tables']['coupon'] = ['id' => ['id', 'code', 'packs']];
            $request['tables']['pack'] = ['idThanksSummary' => ['id', 'title', 'logo', 'cardCount']];
            $request['tables']['file'] = ['id' => ['id', 'url']];
            $request['tables']['invite'] = ['id' => ['id', 'first', 'last', 'user', 'invitee', 'email', 'group', 'code']];
            $request['tables']['ss_user'] = ['id' => ['id', 'first', 'last', 'groups', 'userPacks']];
            $request['tables']['user_pack'] = ['pack', 'removed', 'retention', 'downloaded'];
            $request['tables']['ss_group'] = ['id' => ['name', 'id', 'parent', 'deleted']];
            $request['classes'] = ['tiles', 'summary'];
            $request['headers'] = false;
            $request['footers'] = false;
            if ($tab->length == 0) {
                print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
            }
            */
            ?>
        </div>
    </div>
    <?php $view['slots']->stop();

}