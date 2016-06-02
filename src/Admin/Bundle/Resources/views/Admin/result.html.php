<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\Response;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var GlobalVariables $app */
/** @var User $user */
/** @var Card $card */
$user = $app->getUser();

$context = !empty($context) ? $context : jQuery($this);
$tab = $context->filter('.panel-pane');


if($tab->length == 0) {

    $view->extend('StudySauceBundle:Shared:dashboard.html.php');

    $view['slots']->start('stylesheets');
    foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/packs.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url) { ?>
        <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('javascripts');
    foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/packs.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
        <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
    <?php }
    foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/progressbar.min.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
        <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
    <?php } ?>
    <?php $view['slots']->stop();

}

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="cards<?php print (!empty($pack->getId()) ? implode('', ['-pack' , $pack->getId()]) : ''); ?>">
        <div class="pane-content">
            <?php
            $request = [
                // view settings
                'tables' => [
                    'user_pack' => ['cardResult' => ['user', 'pack', 'retention']],
                    'ss_user' => ['id'],
                    'pack' => ['id']
                ],
                'headers' => false,
                'footers' => false,
                'read-only' => false,
                // search settings
                'ss_user-id' => $user->getId(),
                'pack-id' => $pack->getId(),
                // for new=true the template generates the -count number of empty rows, and no database query is performed
                'count-user_pack' => 1,
                'count-ss_user' => -1,
                'count-pack' => -1,
            ];
            if($tab->length == 0) {
                print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
            }
            ?>
        </div>
    </div>

<?php $view['slots']->stop();


$view['slots']->start('sincludes'); ?>

<?php $view['slots']->stop();

