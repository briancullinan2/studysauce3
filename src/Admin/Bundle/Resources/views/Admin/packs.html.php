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
foreach ($view['assetic']->stylesheets(['@results_css'], [], ['output' => 'bundles/admin/css/*.css']) as $url) { ?>
    <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
<?php }
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/packs.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url) { ?>
    <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
<?php }
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/results.js'], [], ['output' => 'bundles/admin/js/*.js']) as $url) { ?>
    <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
<?php }
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/packs.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
    <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
<?php } ?>
<?php
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="packs<?php print ($entity !== null ? ('-pack' . intval($entity->getId())) : ''); ?>">
        <div class="pane-content">
            <?php if ($entity !== null) { ?>
                <form action="<?php print ($view['router']->generate('packs_create')); ?>" class="pack-edit">
                    <?php
                    $tables = (array)(new stdClass());
                    $tables['pack'] = ['idEdit' => ['modified', 'created', 'id', 'logo'], 'name' => ['title','userCountStr','cardCountStr'], '1' => 'status', '2' => ['group','groups', 'user','userPacks.user'], '3' => 'properties', '4' => 'actions'];
                    $request = [
                        // view settings
                        'tables' => $tables,
                        'headers' => ['pack' => 'packPacks'],
                        'footers' => ['pack' => 'packPacks'],
                        'new' => empty($entity->getId()),
                        'edit' => empty($entity->getId()),
                        // search settings
                        'pack-id' => $entity->getId(),
                        'pack-status' => $entity->getDeleted() ? 'DELETED' : '!DELETED',
                        // for new=true the template generates the -count number of empty rows, and no database query is performed
                        'count-pack' => 1,
                    ];
                    print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                    ?>
                </form>
                <div class="group-list">
                    <?php
                    $tables = (array)(new stdClass());
                    $tables['pack'] = ['0' => 'id', '1' => 'title', 'expandMembers' => [], '2' => ['status'] /* search field but don't display a template */];
                    $tables['ss_group'] = ['0' => 'id', '1' => 'title', 'expandMembers' => ['packs', 'groupPacks'], 'actions' => ['deleted'] /* search field but don't display a template */];
                    $request = [
                        // view settings
                        'tables' => $tables,
                        'classes' => ['last-right-expand'],
                        'headers' => ['pack' => 'createSubGroups'],
                        'footers' => false,
                        'edit' => false,
                        'read-only' => false,
                        // search settings
                        'pack-id' => empty($entity->getId()) ? '0' : $entity->getId(),
                        'pack-status' => $entity->getDeleted() ? 'DELETED' : '!DELETED',
                        'ss_group-deleted' => false,
                        'count-ss_group' => 0,
                        'count-pack' => 1,
                    ];
                    print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                    ?>
                    <div class="empty-members">
                        <div>Select name on the left to see group members</div>
                    </div>
                </div>
                <form action="<?php print ($view['router']->generate('packs_create', ['pack' => ['id' => $entity->getId()]])); ?>" class="card-list">
                    <?php
                    $newCards = true;
                    foreach($entity->getCards()->toArray() as $c) {
                        /** @var Card $c */
                        if(!$c->getDeleted()) {
                            $newCards = false;
                            break;
                        }
                    }
                    $tables = [
                        // view settings
                        'tables' => ['pack', 'card'],
                        'expandable' => ['card' => ['preview']],
                        'headers' => ['card' => 'packCards'],
                        'footers' => ['card' => 'packCards'],
                        'new' => $newCards,
                        'edit' => empty($entity->getId()),
                        // search settings
                        'pack-id' => $entity->getId(),
                        // for new=true the template generates the -count number of empty rows, and no database query is performed
                        'count-pack' => -1,
                        'count-card' => $newCards ? 5 : 0,
                    ];
                    print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables)));
                    ?>
                </form>
                <?php
            }
            else {
                $tables['count-pack'] = 0;
                $tables['tables'] = ['pack' => ['idTiles' => ['created', 'id', 'title', 'userCountStr', 'cardCountStr'], 'packList' => ['groups', 'userPacks.user'], 'actions' => ['status']]];
                $tables['classes'] = ['tiles'];
                $tables['headers'] = ['pack' => 'newPack'];
                $tables['footers'] = ['pack' => 'newPack'];
                print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables)));
            } ?>
        </div>
    </div>
<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('sincludes');
$view['slots']->stop();

