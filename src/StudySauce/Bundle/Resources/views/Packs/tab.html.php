<?php
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
/** @var Pack $entity */

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/results.css'], [], ['output' => 'bundles/admin/css/*.css']) as $url):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/packs.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/results.js'], [], ['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/packs.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="packs<?php print ($entity !== null ? ('-pack' . intval($entity->getId())) : ''); ?>">
        <div class="pane-content">
            <?php
            $tables = ['tables' => ['pack', 'card'], 'expandable' => ['card' => ['preview']]];
            if($entity !== null) {
                $isNew = false;
                if(empty($entity->getId())) {
                    $isNew = ['pack'];
                }
                if($entity->getCards()->filter(function (Card $c) {return !$c->getDeleted();})->count() == 0) {
                    $isNew[] = 'card';
                }
                $tables['pack-id'] = $entity->getId();
                $tables['headers'] = ['pack' => 'packPacks'];
                $tables['new'] = $isNew;
                $tables['edit'] = $entity->getStatus() != 'GROUP' && $entity->getStatus() != 'PUBLIC';
                $tables['count-pack'] = 1;
                $tables['count-card'] = $isNew ? 5 : 0;
                $tables['footers'] = ['pack' => 'packPacks', 'card' => true];
            }
            else {
                $tables['count-pack'] = 0;
                $tables['tables'] = ['pack' => ['id' => ['created', 'id'], 'name' => ['title','userCountStr','cardCountStr'], 'packList' => ['groups', 'userPacks.user'], 'actions' => ['status']]];
                $tables['classes'] = ['tiles'];
                $tables['headers'] = ['pack' => 'new'];
                $tables['footers'] = ['pack' => 'new'];
            }
            print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables)); ?>
        </div>
    </div>
<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'upload-file']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'pack-publish']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'add-entity']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'confirm-remove']), ['strategy' => 'sinclude']);
$view['slots']->stop();

