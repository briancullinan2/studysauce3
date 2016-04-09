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
    <div
        class="panel-pane <?php
        print (!empty($entity) && $entity->getGroupPacks()->count() > 0 ? ' right-pad' : '');
        print (!empty($entity) && $entity->getSubgroups()->count() > 0 ? ' has-subgroups' : ''); ?>"
        id="groups<?php print ($entity !== null ? ('-group' . intval($entity->getId())) : ''); ?>">
        <div class="pane-content">
            <?php if ($entity !== null) { ?>
                <div class="group-edit">
                    <?php
                    $tables = ['ss_group' => ['id' => ['created', 'id'], 'name' => ['name', 'description'], 'parent' => [], 'invites', 'actions' => ['deleted']]];
                    $tables['pack'] = ['title', 'counts', 'members' => ['groups'], 'actions' => ['status'] /* search field but don't display a template */];
                    print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', [
                        'count-pack' => empty($entity->getId()) ? -1 : 0,
                        'count-ss_group' => 1,
                        'ss_group-deleted' => $entity->getDeleted(),
                        'edit' => !empty($entity->getId()) ? false : ['ss_group'],
                        'read-only' => empty($entity->getId()) ? false : ['ss_group'],
                        'new' => empty($entity->getId()),
                        'ss_group-id' => $entity->getId(),
                        'tables' => $tables,
                        'headers' => ['ss_group' => 'groupGroups', 'pack' => 'groupPacks'],
                        'footers' => ['ss_group' => 'groupGroups']]));
                    if ($entity->getGroupPacks()->count() > 0) {
                        ?>
                        <div class="empty-members">
                            <div>Click pack name to see group members</div>
                        </div><?php
                    } ?>
                </div>
            <?php } ?>
            <div class="group-list">
                <?php
                $tables = ['ss_group' => ['id' => ['created', 'id'], 'name' => ['name','description','userCountStr'], 'packList' => ['groupPacks', 'parent'], 'actions' => ['deleted']]];
                if (empty($entity)) {
                    print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', [
                        'parent-ss_group-id' => 'NULL',
                        'classes' => ['tiles'],
                        'tables' => $tables,
                        'headers' => ['ss_group' => 'new'],
                        'footers' => ['ss_group' => 'new']]));
                } else if ($entity->getSubgroups()->count() > 0) {
                    print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', [
                        'parent-ss_group-id' => $entity->getId(),
                        'classes' => ['tiles'],
                        'tables' => $tables,
                        'headers' => ['ss_group' => 'new'],
                        'footers' => ['ss_group' => 'new']]));
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

