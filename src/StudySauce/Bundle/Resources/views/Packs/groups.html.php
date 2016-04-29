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
        print (!empty($entity) && $entity->getSubgroups()->count() > 0 ? ' has-subgroups' : ''); ?>"
        id="groups<?php print ($entity !== null ? ('-group' . intval($entity->getId())) : ''); ?>">
        <div class="pane-content">
            <?php if ($entity !== null) { ?>
                <div class="group-edit">
                    <?php
                    $tables = ['ss_group' => ['id' => ['created', 'id'], 'name' => ['name', 'description'], 'parent' => [''], 'invite' => ['invites'], 'actions' => ['deleted']]];
                    $isNew = empty($entity->getId());
                    print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', [
                        'count-ss_group' => 1,
                        'ss_group-deleted' => $entity->getDeleted(),
                        'edit' => !$isNew ? false : ['ss_group'],
                        'read-only' => $isNew ? false : ['ss_group'],
                        'new' => $isNew,
                        'ss_group-id' => $entity->getId(),
                        'tables' => $tables,
                        'headers' => ['ss_group' => 'groupGroups'],
                        'footers' => ['ss_group' => 'groupGroups']
                    ]));
                    ?>
                </div>
            <?php } ?>
            <div class="membership">
                <div class="group-list">
                    <?php
                    $tiles = ['ss_group' => ['id' => ['created', 'id'], 'name' => ['name', 'userCountStr', 'descriptionStr'], 'packList' => ['groupPacks', 'parent'], 'actions' => ['deleted']]];
                    if (empty($entity)) {
                        print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', [
                            'tables' => $tiles,
                            'parent-ss_group-id' => 'NULL',
                            'count-ss_group' => 0,
                            'classes' => ['tiles'],
                            'headers' => ['ss_group' => 'newGroup'],
                            'footers' => ['ss_group' => 'newGroup']
                        ]));
                    } else {
                        global $subGroupParent;
                        $subGroupParent = [$entity];
                        // TODO: check view setting
                        $tableViews = [
                            'Tiles' => [
                                'tables' => $tiles,
                                'classes' => ['tiles'],
                                 'headers' => false,
                           ],
                            'Membership' => [
                                'tables' => ['ss_group' => ['id', 'title', 'counts', 'expandMembers' => ['parent'], 'actions' => ['deleted'] /* search field but don't display a template */]],
                                'classes' => ['last-right-expand'],
                                 'headers' => ['ss_group' => 'subGroups'],
                           ]
                        ];
                        $tableView = $tableViews[empty($app->getRequest()->get('view')) || $app->getRequest()->get('view') != 'Tiles' ? 'Membership' : 'Tiles'];
                        print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tableView + [
                                'parent-ss_group-id' => !empty($entity->getId()) ? $entity->getId() : '0',
                                'count-ss_group' => 0,
                                'parent-ss_group-deleted' => $entity->getDeleted(),
                                'edit' => false,
                                'read-only' => false,
                                'headers' => ['ss_group' => 'subGroups'],
                                'footers' => ['ss_group' => 'subGroups'],
                                'views' => $app->getUser()->getEmailCanonical() == 'brian@studysauce.com' ? $tableViews : null
                            ]));
                    }
                    ?>
                </div>
                <?php if ($entity !== null) {
                    global $subPacks;
                    list($users, $subPacks, $groups) = $entity->getUsersPacksGroupsRecursively();
                    ?>
                    <div class="list-packs">
                        <?php
                        $tables = ['ss_group' => ['id', 'deleted']];
                        $tables['pack'] = ['id', 'title', 'counts', 'expandMembers' => ['group', 'groups'], 'actionsGroup' => ['status'] /* search field but don't display a template */];
                        $isNew = empty($entity->getId());
                        print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', [
                            'count-pack' => $isNew || empty($subPacks) ? -1 : 0,
                            'count-ss_group' => -1,
                            'ss_group-deleted' => $entity->getDeleted(),
                            'edit' => false,
                            'new' => empty($subPacks),
                            'classes' => ['last-right-expand'],
                            'read-only' => false,
                            'ss_group-id' => $entity->getId(),
                            'tables' => $tables,
                            'headers' => ['pack' => 'groupPacks'],
                            'footers' => ['pack' => 'groupPacks']
                        ]));
                        ?>
                    </div>
                    <div class="empty-members">
                        <div>Select name on the left to see group members</div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'upload-file']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'pack-publish']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'add-entity']), ['strategy' => 'sinclude']);
$view['slots']->stop();

