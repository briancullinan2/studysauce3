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
    foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/packs.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url) { ?>
        <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('javascripts');
    foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/packs.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
        <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
    <?php }
    $view['slots']->stop();
}

if($tab->length > 0) {
    $tab->attr('id', implode('', ['packs-pack' , $entity->getId()]));
    $tab->find('.pack-edit')
        ->attr('action', $view['router']->generate('packs_create'));
    $tab->find('.card-list')
        ->attr('action', $view['router']->generate('packs_create', ['pack' => ['id' => $entity->getId()]]))
        ->removeAttr('name');
}

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="packs<?php print ($entity !== null ? implode('', [method_exists($entity, 'getTitle') ? '-pack' : '-group' , intval($entity->getId())]) : ''); ?>">
        <div class="pane-content">
            <?php if ($entity !== null && method_exists($entity, 'getTitle')) { ?>
                <form action="<?php print ($view['router']->generate('packs_create')); ?>" class="pack-edit">
                    <?php
                    $tables = (array)(new stdClass());
                    $tables['pack'] = [
                        'idEdit' => ['modified', 'created', 'id', 'logo'],
                        'name' => ['title','userCountStr','cardCountStr'],
                        '1' => 'status',
                        '2' => ['group','groups', 'user','userPacks.user'],
                        '3' => 'properties',
                        'actions' => []];
                    $request = [
                        // view settings
                        'tables' => $tables,
                        'headers' => ['pack' => 'packPacks'],
                        'footers' => ['pack' => 'packPacks'],
                        'new' => $isNew,
                        'edit' => $isNew,
                        // search settings
                        'pack-id' => $entity->getId(),
                        'pack-status' => $entity->getDeleted() ? 'DELETED' : '!DELETED',
                        // for new=true the template generates the -count number of empty rows, and no database query is performed
                        'count-pack' => 1,
                        'count-card' => -1
                    ];
                    if($tab->length == 0) {
                        print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                    }
                    ?>
                </form>
                <div class="group-list">
                    <?php
                    $tables = (array)(new stdClass());
                    $tables['file'] = AdminController::$defaultMiniTables['file'];
                    $tables['ss_user'] = ['first', 'last', 'email', 'id', 'deleted', 'groups'];
                    $tables['user_pack'] = ['user', 'removed', 'downloaded'];
                    $tables['pack'] = ['0' => 'id', 'title' => ['title', 'logo'], 'expandMembers' => ['userPacks'], '2' => ['status'] /* search field but don't display a template */];
                    $tables['ss_group'] = ['0' => 'id', 'title' => ['logo', 'name', 'description'], 'expandMembers' => ['users', 'groupPacks', 'parent'], 'actions' => ['deleted'] /* search field but don't display a template */];
                    $request = [
                        // view settings
                        'tables' => $tables,
                        'classes' => ['last-right-expand'],
                        'headers' => ['pack' => 'createSubGroups'],
                        'footers' => false,
                        'edit' => false,
                        'new' => $isNew,
                        'read-only' => false,
                        // search settings
                        'pack-id' => $isNew ? '0' : $entity->getId(),
                        'pack-status' => $entity->getDeleted() ? 'DELETED' : '!DELETED',
                        'parent-ss_group-deleted' => null,
                        'ss_group-deleted' => false,
                        'count-ss_group' => $isNew ? -1 : 0,
                        'count-pack' => $isNew ? -1 : 1,
                        'count-ss_user' => -1,
                        'count-file' => -1,
                        'count-user_pack' => -1,
                    ];
                    if($tab->length == 0) {
                        print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                    }
                    else {
                        $tab->find('.group-list .results')->data('request', $request)->attr('data-request', json_encode($request));
                    }
                    ?>
                    <div class="empty-members">
                        <div>Select name on the left to see group members</div>
                    </div>
                </div>
                <form action="<?php print ($view['router']->generate('packs_create', ['pack' => ['id' => $entity->getId()]])); ?>" <?php print (empty($entity->getId()) ? 'name="pack[cards]"' : ''); ?> class="card-list">
                    <?php
                    $newCards = true;
                    foreach($entity->getCards()->toArray() as $c) {
                        /** @var Card $c */
                        if(!$c->getDeleted()) {
                            $newCards = false;
                            break;
                        }
                    }
                    $tables = (array)(new stdClass());
                    $tables['pack'] = ['id' => ['id']];
                    $tables['card'] = ['id' => ['id'], 'name' => ['type', 'upload', 'content'], 'correct' => ['correct', 'answers', 'responseContent', 'responseType'], '0' => ['pack'], 'actions' => ['deleted']];
                    $tables['answer'] = ['id' => ['value', 'deleted', 'correct', 'content', 'id']];
                    $request = [
                        // view settings
                        'tables' => $tables,
                        'expandable' => ['card' => ['preview']],
                        'headers' => ['card' => 'packCards'],
                        'footers' => ['card' => 'packCards'],
                        'new' => $newCards,
                        'edit' => $isNew,
                        // search settings
                        'pack-id' => $entity->getId(),
                        // for new=true the template generates the -count number of empty rows, and no database query is performed
                        'count-pack' => -1,
                        'count-answer' => -1,
                        'count-card' => $newCards ? 5 : 0,
                    ];
                    if($tab->length == 0) {
                        print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                    }
                    ?>
                </form>
                <?php
            }
            else {
                if (empty($entity) && count($user->getGroups()->toArray()) > 1) {
                    $tiles = [
                        'file' => ['id', 'url'],
                        'ss_user' => ['id'],
                        'pack' => ['id', 'status', 'logo', 'title'],
                        'ss_group' => ['idTilesPack' => ['created', 'id', 'name', 'userCountStr', 'descriptionStr', 'logo'], 'packList' => ['groupPacks', 'parent', 'users', 'subgroups'], 'actions' => ['deleted']]];
                    $request = [
                        'tables' => $tiles,
                        'parent-ss_group-id' => 'NULL',
                        'count-ss_group' => 0,
                        'count-pack' => -1,
                        'read-only' => false,
                        'count-ss_user' => -1,
                        'count-file' => -1,
                        'classes' => ['tiles'],
                        'headers' => ['ss_group' => 'newPack'],
                        'footers' => ['ss_group' => 'newPack']
                    ];
                }
                else {
                    if(!empty($entity)) {
                        $request['ss_group-deleted'] = $entity->getDeleted();
                        $request['ss_group-id'] = $entity->getId();
                        $request['ss_group-1ss_group-id'] = null;
                        $request['subgroups-ss_group-deleted'] = null;
                        $request['parent-ss_group-deleted'] = null;
                        $request['ss_group-1parent-ss_group-id'] = $entity->getId();
                    }
                    $request['count-file'] = -1;
                    $request['count-pack'] = 0;
                    $request['count-card'] = -1;
                    $request['count-ss_group'] = -1;
                    $request['ss_group-1count-ss_group'] = 0;
                    $request['read-only'] = false;
                    $request['count-ss_user'] = -1;
                    $request['count-user_pack'] = -1;
                    $request['tables'] = (array)(new stdClass());
                    $request['tables']['file'] = ['id', 'url'];
                    $request['tables']['ss_group'] = ['id', 'name', 'users', 'deleted'];
                    $request['tables']['ss_group-1'] = ['idTilesPack' => ['created', 'id', 'name', 'userCountStr', 'descriptionStr', 'logo'], 'packList' => ['groupPacks', 'parent', 'users', 'subgroups'], 'actions' => ['deleted']];
                    $request['tables']['ss_user'] = ['id', 'first', 'last', 'groups'];
                    $request['tables']['user_pack'] = ['user', 'pack', 'removed', 'downloaded'];
                    $request['tables']['card'] = ['id', 'deleted'];
                    $request['tables']['pack'] = ['idTiles' => ['created', 'id', 'title', 'logo', 'userCountStr', 'cardCountStr'], 'packList' => ['groups', 'userPacks', 'cards'], 'actions' => ['status']];
                    $request['classes'] = ['tiles'];
                    $request['headers'] = ['pack' => 'newPack'];
                    $request['footers'] = ['pack' => 'newPack'];
                }
                if ($tab->length == 0) {
                    print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                }
            } ?>
        </div>
    </div>
<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('sincludes');
$view['slots']->stop();

