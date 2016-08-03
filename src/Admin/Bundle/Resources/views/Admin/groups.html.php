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

$context = !empty($context) ? $context : jQuery($this);
$tab = $context->filter('.panel-pane');

$isNew = !empty($entity) && empty($entity->getId());

if($tab->length == 0) {

    $view->extend('StudySauceBundle:Shared:dashboard.html.php');

    $view['slots']->start('stylesheets');
    foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/groups.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url) { ?>
        <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('javascripts');
    foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/groups.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
        <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
    <?php }
    $view['slots']->stop();

}

if($tab->length > 0) {
    $tab->attr('id', implode('', ['groups-group' , $entity->getId()]));
}

$view['slots']->start('body'); ?>
    <div class="panel-pane <?php
        print (!empty($entity) && count($entity->getSubgroups()->toArray()) > 0 ? ' has-subgroups' : ''); ?>"
        id="groups<?php print ($entity !== null ? implode('', ['-group' , intval($entity->getId())]) : ''); ?>">
        <div class="pane-content">
            <?php if ($entity !== null) { ?>
                <form action="<?php print ($view['router']->generate('save_group')); ?>" class="group-edit">
                    <?php
                    $tables = [
                        'invite' => ['code'],
                        'file' => AdminController::$defaultMiniTables['file'],
                        'ss_group' => [
                            'idEdit' => ['created', 'id', 'logo'],
                            'name' => ['name', 'description'],
                            'parent' => ['subgroups', 'parent'],
                            'invite' => ['invites'],
                            'actions' => ['deleted']]
                    ];
                    $request = [
                        'count-file' => -1,
                        'count-invite' => -1,
                        'count-ss_group' => 1,
                        'ss_group-deleted' => $entity->getDeleted(),
                        'edit' => !$isNew ? false : ['ss_group'],
                        'read-only' => $isNew ? false : ['ss_group'],
                        'new' => $isNew,
                        // TODO: only search on joins when the prefix is correct?  instead of having to exclude these
                        'parent-ss_group-deleted' => '_empty',
                        'subgroups-ss_group-deleted' => '_empty',
                        'parent-ss_group-id' => '_empty',
                        'subgroups-ss_group-id' => '_empty',
                        'ss_group-id' => $entity->getId(),
                        'tables' => $tables,
                        'headers' => ['ss_group' => 'groupGroups'],
                        'footers' => ['ss_group' => 'groupGroups']
                    ];
                    if($tab->length == 0) {
                        print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                    }
                    else {
                        $tab->find('.group-edit .results')->data('request', $request)->attr('data-request', json_encode($request));
                    }
                    ?>
                </form>
            <?php } ?>
            <div class="membership">
                <div class="group-list">
                    <?php
                    $tiles = [
                        'file' => ['id', 'url'],
                        'ss_user' => ['id'],
                        'pack' => ['id', 'status', 'logo'],
                        'ss_group' => ['idTiles' => ['created', 'id', 'name', 'logo'], 'groupList' => ['groupPacks', 'parent', 'users', 'packs', 'subgroups', 'deleted']]];
                    if (empty($entity)) {
                        $request = [
                            'tables' => $tiles,
                            'parent-ss_group-id' => 'NULL',
                            'count-ss_group' => 0,
                            'count-pack' => -1,
                            'count-ss_user' => -1,
                            'count-file' => -1,
                            'read-only' => false,
                            'classes' => ['tiles'],
                            'headers' => ['ss_group' => 'newGroup'],
                            'footers' => ['ss_group' => 'newGroup']
                        ];
                    } else {
                        // TODO: check view setting
                        $tableViews = (array)(new stdClass());
                        $tableViews['Tiles'] = (array)(new stdClass());
                        $tableViews['Tiles']['tables'] = $tiles;
                        $tableViews['Tiles']['classes'] = ['tiles'];
                        $tableViews['Tiles']['footers'] = ['ss_group' => 'newGroup'];
                        $tableViews['Membership'] = (array)(new stdClass());
                        $tableViews['Membership']['tables'] = (array)(new stdClass());
                        $tableViews['Membership']['tables']['file'] = AdminController::$defaultMiniTables['file'];
                        $tableViews['Membership']['tables']['pack'] = AdminController::$defaultMiniTables['pack'];
                        $tableViews['Membership']['tables']['ss_user'] = AdminController::$defaultMiniTables['ss_user'];
                        $tableViews['Membership']['tables']['ss_group-1'] = ['0' => 'id', 'title' => ['logo', 'name', 'description'], 'expandMembers' => ['users', 'groupPacks', 'deleted'] /* search field but don't display a template */];
                        $tableViews['Membership']['tables']['ss_group'] = ['0' => 'id', 'title' => ['logo', 'name', 'description'], 'expandMembers' => ['users', 'groupPacks', 'parent'], 'actions' => ['deleted'] /* search field but don't display a template */];
                        $tableViews['Membership']['classes'] = ['last-right-expand'];
                        $tableViews['Membership']['footers'] = ['ss_group' => 'groupCount'];
                        $request = $tableViews[empty($app->getRequest()->get('view')) || $app->getRequest()->get('view') != 'Tiles' ? 'Membership' : 'Tiles'];
                        $request['ss_group-1headers'] = ['ss_group' => 'subGroups'];
                        $request['ss_group-1footers'] = false;
                        $request['ss_group-1ss_group-id'] = !empty($entity->getId()) ? $entity->getId() : '0';
                        $request['parent-ss_group-id'] = !empty($entity->getId()) ? $entity->getId() : '0';
                        $request['subgroups-ss_group-id'] = '_empty';
                        $request['subgroups-ss_group-deleted'] = '_empty';
                        $request['count-file'] = -1;
                        $request['count-ss_user'] = -1;
                        $request['count-pack'] = -1;
                        $request['new'] = $isNew;
                        $request['count-ss_group'] = $isNew ? -1 : 0;
                        $request['ss_group-deleted'] = $entity->getDeleted();
                        $request['edit'] = false;
                        $request['read-only'] = false;
                        $request['headers'] = false;
                        $request['footers'] = ['ss_group' => 'groupCount'];
                        $request['views'] = $tableViews;
                    }
                    if($tab->length == 0) {
                        print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                    }
                    else {
                        $tab->find('.group-list .results')->data('request', $request)->attr('data-request', json_encode($request));
                    } ?>
                </div>
                <?php if (!empty($entity)) { ?>
                <div class="list-packs">
                    <?php
                    $tables = (array)(new stdClass());
                    $tables['ss_group'] = ['id', 'deleted'];
                    $tables['ss_user'] = ['first', 'last', 'email', 'id', 'deleted', 'groups'];
                    $tables['user_pack'] = ['user', 'removed', 'downloaded'];
                    $tables['pack'] = ['0' => 'id', 'title' => ['title', 'logo', 'cardCount'], 'expandMembers' => ['groups', 'userPacks'], 'actionsGroup' => ['status'] /* search field but don't display a template */];
                    $request = [
                        'count-pack' => $isNew ? -1 : 0,
                        'count-ss_group' => 1,
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
                    ];
                    if($tab->length == 0) {
                        print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                    }
                    else {
                        $tab->find('.list-packs .results')->data('request', $request)->attr('data-request', json_encode($request));
                    } ?>
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

