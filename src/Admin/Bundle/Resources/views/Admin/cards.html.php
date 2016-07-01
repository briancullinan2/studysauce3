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
    <div class="panel-pane" id="cards<?php print (!empty($card->getId()) ? implode('', ['-card' , $card->getId()]) : ''); ?>" data-card="<?php print ($view->escape(json_encode(['pack' => ['id' => $card->getPack()->getId()]]))); ?>">
        <div class="pane-content">
            <h2><?php print($view->escape($card->getPack()->getTitle())); ?></h2>
            <?php
            $tables = (array)(new stdClass());
            $tables['card'] = ['card' => ['id', 'answers', 'correct', 'content', 'responseType']];
            $tables['answer'] = ['id', 'value', 'card', 'deleted', 'correct', 'content'];
            $tables['pack'] = ['id', 'status', 'cards'];
            $tables['user_pack'] = ['id' => ['user', 'pack', 'retention', 'removed']];
            $tables['ss_user'] = ['id', 'userPacks'];
            $request = [
                // view settings
                'tables' => $tables,
                'headers' => false,
                'footers' => false,
                'read-only' => false,
                // search settings
                'card-id' => $card->getId(),
                'pack-id' => $card->getPack()->getId(),
                'ss_user-id' => $user->getId(),
                // for new=true the template generates the -count number of empty rows, and no database query is performed
                'count-card' => 1,
                'count-pack' => -1,
                'count-answer' => -1,
                'count-user_pack' => 1,
                'count-ss_user' => -1,
            ];
            if($tab->length == 0) {
                print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
            }
            ?>
        </div>
    </div>
    <div class="panel-pane" id="cards<?php print (!empty($card->getId()) ? implode('', ['-answer' , $card->getId()]) : ''); ?>" data-card="<?php print ($view->escape(json_encode(['pack' => ['id' => $card->getPack()->getId()]]))); ?>">
        <div class="pane-content">
            <h2><?php print($view->escape($card->getPack()->getTitle())); ?></h2>
            <?php
            $tables = (array)(new stdClass());
            $tables['card'] = ['cardAnswer' => ['id', 'answers', 'correct', 'content', 'responseType']];
            $tables['answer'] = ['id', 'value', 'card', 'deleted', 'correct', 'content'];
            $tables['pack'] = ['id', 'status'];
            $tables['user_pack'] = ['id' => ['user', 'pack', 'retention', 'removed']];
            $tables['ss_user'] = ['id', 'userPacks'];
            $request = [
                // view settings
                'tables' => $tables,
                'headers' => false,
                'footers' => false,
                'read-only' => false,
                // search settings
                'card-id' => $card->getId(),
                'pack-id' => $card->getPack()->getId(),
                'ss_user-id' => $user->getId(),
                // for new=true the template generates the -count number of empty rows, and no database query is performed
                'count-card' => 1,
                'count-pack' => -1,
                'count-answer' => -1,
                'count-user_pack' => 1,
                'count-ss_user' => -1,
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

