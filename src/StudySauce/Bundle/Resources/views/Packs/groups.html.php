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
/** @var Group $entity */

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/results.css'], [], ['output' => 'bundles/admin/css/*.css']) as $url):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/groups.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/results.js'], [], ['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/groups.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="groups<?php print ($entity !== null ? ('-group' . $entity->getId()) : ''); ?>">
        <div class="pane-content">
            <div class="group-edit">
            <?php

            if (!empty($entity)) {
                $tables = ['ss_group' => ['id' => ['created', 'id'], 'name' => ['name', 'description'], 'parent' => [], 'invites', 'packs' => ['packs', 'groupPacks'], 'actions' => ['deleted']]];
                if ($entity->getPacks()->count() > 0) {
                    $tables['pack'] = ['name' => ['title'], 'counts', 'actions', ['group', 'groups'] /* search field but don't display a template */];
                }
                print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', [
                    'count-pack' => 0,
                    'edit' => ['ss_group'],
                    'ss_group-id' => $entity->getId(),
                    'tables' => $tables,
                    'headers' => false,
                    'expandable' => ['pack' => ['members']]]));
                if ($entity->getPacks()->count() > 0) {
                    ?>
                    <div class="empty-members">
                        <div>Click pack name to see group members</div>
                    </div><?php
                }
            }
            ?>
            </div>
            <div class="group-list <?php print ($entity->getPacks()->count() > 0 ? 'left-pad' : ''); ?>">
                <?php
                if (empty($entity)) {
                    print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', [
                        'count-pack' => -1,
                        'parent-ss_group-id' => 'NULL',
                        'tables' => ['ss_group', 'pack'],
                        'headers' => false,
                        'expandable' => ['pack' => ['members']]]));
                } else if ($entity->getSubgroups()->count() > 0) {
                    print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', [
                        'count-pack' => -1,
                        'parent-ss_group-id' => $entity->getId(),
                        'tables' => ['ss_group'],
                        'headers' => false,
                        'expandable' => ['pack' => ['members']]]));
                }
                ?>
            </div>
        </div>
    </div>
<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'upload-file']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'pack-publish']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'add-entity']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'confirm-remove']), ['strategy' => 'sinclude']);
$view['slots']->stop();

