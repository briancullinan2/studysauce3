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
/** @var Group $entity */

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@results_css'], [], ['output' => 'bundles/admin/css/*.css']) as $url) { ?>
    <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
<?php }
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/groups.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url) { ?>
    <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
<?php }
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/results.js'], [], ['output' => 'bundles/admin/js/*.js']) as $url) { ?>
    <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
<?php }
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/groups.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
    <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
<?php }
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane <?php
        print (!empty($entity) && $entity->getSubgroups()->count() > 0 ? ' has-subgroups' : ''); ?>"
        id="groups<?php print ($entity !== null ? implode('', ['-group' , intval($entity->getId())]) : ''); ?>">
        <div class="pane-content">
            <?php if ($entity !== null) { ?>
                <form action="<?php print ($view['router']->generate('save_group')); ?>" class="group-edit">
                    <?php
                    $tables = [
                        'ss_group' => ['idEdit' => ['created', 'id', 'logo'], 'name' => ['name', 'description'], 'parent' => ['subgroups'], 'invite' => ['invites'], 'actions' => ['deleted']]
                    ];
                    $isNew = empty($entity->getId());
                    print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', [
                        'count-ss_group' => 1,
                        'ss_group-deleted' => $entity->getDeleted(),
                        'edit' => !$isNew ? false : ['ss_group'],
                        'read-only' => $isNew ? false : ['ss_group'],
                        'new' => $isNew,
                        'parent-ss_group-id' => '!0',
                        'ss_group-id' => $entity->getId(),
                        'tables' => $tables,
                        'headers' => ['ss_group' => 'groupGroups'],
                        'footers' => ['ss_group' => 'groupGroups']
                    ])));
                    ?>
                </form>
            <?php } ?>
            <div class="membership">
                <div class="group-list">
                    <?php
                    $tiles = ['ss_group' => ['idTiles' => ['created', 'id', 'name', 'userCountStr', 'descriptionStr'], 'packList' => ['groupPacks', 'parent', 'users'], 'actions' => ['deleted']]];
                    if (empty($entity)) {
                        print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', [
                            'tables' => $tiles,
                            'parent-ss_group-id' => 'NULL',
                            'count-ss_group' => 0,
                            'classes' => ['tiles'],
                            'headers' => ['ss_group' => 'newGroup'],
                            'footers' => ['ss_group' => 'newGroup']
                        ])));
                    } else {
                        // TODO: check view setting
                        $tableViews = (array)(new stdClass());
                        $tableViews['Tiles'] = (array)(new stdClass());
                        $tableViews['Tiles'] = [
                            'tables' => $tiles,
                            'classes' => ['tiles'],
                        ];
                        $tableViews['Membership'] = (array)(new stdClass());
                        $tableViews['Membership']['tables'] = (array)(new stdClass());
                        $tableViews['Membership']['tables']['file'] = AdminController::$defaultMiniTables['file'];
                        $tableViews['Membership']['tables']['pack'] = AdminController::$defaultMiniTables['pack'];
                        $tableViews['Membership']['tables']['ss_user'] = AdminController::$defaultMiniTables['ss_user'];
                        $tableViews['Membership']['tables']['ss_group-1'] = ['0' => 'id', 'title' => ['logo', 'name', 'description'], 'expandMembers' => ['users', 'groupPacks', 'deleted'] /* search field but don't display a template */];
                        $tableViews['Membership']['tables']['ss_group'] = ['0' => 'id', 'title' => ['logo', 'name', 'description'], 'expandMembers' => ['users', 'groupPacks', 'parent'], 'actions' => ['deleted'] /* search field but don't display a template */];
                        $tableViews['Membership']['classes'] = ['last-right-expand'];
                        $tableView = $tableViews[empty($app->getRequest()->get('view')) || $app->getRequest()->get('view') != 'Tiles' ? 'Membership' : 'Tiles'];
                        print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', array_merge($tableView, [
                            'ss_group-1headers' => ['ss_group' => 'subGroups'],
                            'ss_group-1footers' => false,
                            'ss_group-1ss_group-id' => !empty($entity->getId()) ? $entity->getId() : '0',
                            'parent-ss_group-id' => !empty($entity->getId()) ? $entity->getId() : '0',
                            'count-file' => -1,
                            'count-ss_user' => -1,
                            'count-pack' => -1,
                            'count-ss_group' => 0,
                            'ss_group-deleted' => $entity->getDeleted(),
                            'edit' => false,
                            'read-only' => false,
                            'headers' => false,
                            'footers' => ['ss_group' => 'groupCount'],
                            'views' => $tableViews
                        ]))));
                    } ?>
                </div>
                <?php if (!empty($entity)) { ?>
                <div class="list-packs">
                    <?php
                    $tables = ['ss_group' => ['id', 'deleted'], 'ss_user' => ['first', 'last', 'email', 'id', 'deleted', 'userPacks', 'groups'], 'user_pack' => ['user', 'pack', 'removed', 'downloaded'], 'card' => ['id', 'deleted']];
                    $tables['pack'] = ['0' => 'id', 'title' => ['title', 'logo', 'cards'], 'expandMembers' => ['group', 'groups', 'users', 'userPacks'], 'actionsGroup' => ['status'] /* search field but don't display a template */];
                    $isNew = empty($entity->getId());
                    print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', [
                        'count-pack' => $isNew ? -1 : 0,
                        'count-ss_group' => 1,
                        'count-card' => -1,
                        'count-ss_user' => -1,
                        'count-user_pack' => -1,
                        'ss_group-deleted' => $entity->getDeleted(),
                        'edit' => false,
                        'classes' => ['last-right-expand'],
                        'read-only' => false,
                        'ss_group-id' => $entity->getId(),
                        'tables' => $tables,
                        'headers' => ['ss_group' => 'groupPacks'],
                        'footers' => ['pack' => 'groupPacks']
                    ])));
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
$view['slots']->stop();

