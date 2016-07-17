<?php
use Admin\Bundle\Controller\AdminController;
use Doctrine\Common\Collections\ArrayCollection;
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var GlobalVariables $app */
/** @var $view TimedPhpEngine */
/** @var $user User */
$user = $app->getUser();
/** @var Pack $entity */

$context = !empty($context) ? $context : jQuery($this);
$tab = $context->filter('.panel-pane');

$isNew = !empty($entity) && empty($entity->getId());

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
}

if($tab->length > 0) {

}

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="store_cart">
        <div class="pane-content">
            <form action="<?php print ($view['router']->generate('checkout_coupon')); ?>" method="POST">
            <?php
                $request = (array)(new stdClass());
                $request['count-file'] = -1;
                $request['count-pack'] = -1;
                $request['count-coupon'] = 0;
                $request['count-card'] = -1;
                $request['count-ss_group'] = -1;
                $request['count-ss_user'] = 1;
                $request['count-user_pack'] = -1;
                $request['read-only'] = false;
                $request['inCartOnly'] = true;
                $request['tables'] = (array)(new stdClass());
                $request['tables']['file'] = ['id', 'url'];
                $request['tables']['coupon'] = ['idTilesSummary' => ['id', 'name', 'description', 'packs', 'options']];
                $request['tables']['ss_group'] = ['id', 'name', 'users', 'deleted'];
                $request['tables']['ss_user'] = ['id' => ['id', 'first', 'last', 'userPacks']];
                $request['tables']['user_pack'] = ['pack', 'removed', 'downloaded'];
                $request['tables']['card'] = ['id', 'deleted'];
                $request['tables']['pack'] = ['idTilesSummary' => ['created', 'id', 'title', 'logo'], 'actions' => ['cards', 'status']];
                $request['classes'] = ['tiles', 'summary'];
                $request['headers'] = false;
                $request['footers'] = ['coupon' => 'cart'];
                if ($tab->length == 0) {
                    print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                } ?>
            </form>
        </div>
    </div>
<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('sincludes');
$view['slots']->stop();

