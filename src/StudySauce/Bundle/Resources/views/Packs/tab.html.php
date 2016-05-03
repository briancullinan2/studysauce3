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
<?php endforeach; ?>
    <script type="text/javascript" src="<?php echo $view['router']->generate('template', ['name' => 'cell-status-pack']) ?>"></script>
<?php
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="packs<?php print ($entity !== null ? ('-pack' . intval($entity->getId())) : ''); ?>">
        <div class="pane-content">
            <?php if ($entity !== null) { ?>
                <form action="<?php print $view['router']->generate('packs_create', ['packId' => $entity->getId()]); ?>" class="pack-edit">
                    <?php
                    $tables = [
                        // view settings
                        'tables' => ['pack'],
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
                    print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables));
                    ?>
                </form>
                <div class="group-list">
                    <?php
                    $newCards = $entity->getCards()->filter(function (Card $c) {
                            return !$c->getDeleted();
                        })->count() == 0;
                    $tables = [
                        // view settings
                        'tables' => [
                            'pack' => ['id', 'title', 'expandMembers' => [], ['status'] /* search field but don't display a template */],
                            'ss_group' => ['id', 'title', 'counts', 'expandMembers' => ['packs', 'groupPacks'], 'actions' => ['deleted'] /* search field but don't display a template */]
                        ],
                        'classes' => ['last-right-expand'],
                        'headers' => ['pack' => 'subGroups'],
                        'footers' => ['ss_group' => 'subGroups'],
                        'edit' => false,
                        'read-only' => false,
                        // search settings
                        'pack-id' => $entity->getId(),
                        'pack-status' => $entity->getDeleted() ? 'DELETED' : '!DELETED',
                        'ss_group-deleted' => false,
                        'count-ss_group' => 0,
                        'count-pack' => 1,
                    ];
                    print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables));
                    ?>
                    <div class="empty-members">
                        <div>Select name on the left to see group members</div>
                    </div>
                </div>
                <div class="card-list">
                    <?php
                    $newCards = $entity->getCards()->filter(function (Card $c) {
                            return !$c->getDeleted();
                        })->count() == 0;
                    $tables = [
                        // view settings
                        'tables' => ['pack', 'card'],
                        'expandable' => ['card' => ['preview']],
                        'headers' => ['card' => 'packCards'],
                        'footers' => ['card' => true],
                        'new' => $newCards,
                        'edit' => $newCards,
                        // search settings
                        'pack-id' => $entity->getId(),
                        'pack-status' => $entity->getDeleted() ? 'DELETED' : '!DELETED',
                        // for new=true the template generates the -count number of empty rows, and no database query is performed
                        'count-pack' => -1,
                        'count-card' => $newCards ? 5 : 0,
                    ];
                    print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables));
                    ?>
                </div>
                <?php
            }
            else {
                $tables['count-pack'] = 0;
                $tables['tables'] = ['pack' => ['id' => ['created', 'id'], 'name' => ['title', 'userCountStr', 'cardCountStr'], 'packList' => ['groups', 'userPacks.user'], 'actions' => ['status']]];
                $tables['classes'] = ['tiles'];
                $tables['headers'] = ['pack' => 'newPack'];
                $tables['footers'] = ['pack' => 'newPack'];
                print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables));
            } ?>
        </div>
    </div>
<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'upload-file']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'pack-publish']), ['strategy' => 'sinclude']);
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'add-entity']), ['strategy' => 'sinclude']);
$view['slots']->stop();

