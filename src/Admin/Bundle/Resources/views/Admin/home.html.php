<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\Response;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/home.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url) { ?>
    <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
<?php }
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/packs.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
    <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
<?php }
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="home<?php print (!empty($id) && $user->getId() != $id ? implode('', ['-user' , $id]) : ''); ?>">
        <div class="pane-content">
            <div class="user-shuffle">
            <?php
            $tables = (array)(new stdClass());
            $tables['ss_user'] = ['id' => ['id', 'userPacks']];
            $tables['pack'] = ['titleNew' => ['id', 'title', 'status', 'cardCount']];
            $tables['user_pack'] = ['pack', 'removed', 'retention'];
            $request = [
                'tables' => $tables,
                //'user-ss_user-id' => 'NULL',
                'user_pack-removed' => '!1',
                'userPacks-user_pack-removed' => '_empty',
                'ss_user-id' => $id,
                'headers' => ['ss_user' => 'bigButton'],
                'count-pack' => 0,
                'read-only' => false,
                'classes' => ['centerized'],
                'count-ss_user' => 1,
                'count-user_pack' => -1,
                'footers' => false
            ];
            print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
            /*
            ?>
            </div>
            <div class="study-log">
                <h2>Study log</h2>
                <?php
                $request = [
                    'tables' => ['ss_user' => ['mastery' => 'id']],
                    //'user-ss_user-id' => 'NULL',
                    'ss_user-id' => $id,
                    'headers' => false,
                    'read-only' => false,
                    'count-pack' => 0,
                    'count-ss_user' => -1,
                    'footers' => false
                ];
                print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                ?>
            </div>
            <div class="study-mastery">
                <h2>Study mastery report</h2>
                <?php
                $tables = (array)(new stdClass());
                $tables['ss_user'] = ['id', 'userPacks'];
                $tables['pack'] = ['id', 'title', 'packMastery', ['status', 'logo', 'cards'], 'expandMastery'];
                $tables['card'] = ['id'];
                $tables['user_pack'] = ['pack', 'removed', 'retention'];
                $request = [
                    'tables' => $tables,
                    'user_pack-removed' => false,
                    //'user-ss_user-id' => 'NULL',
                    'classes' => ['last-right-expand'],
                    'ss_user-id' => $id,
                    'headers' => false,
                    'read-only' => false,
                    'count-pack' => 0,
                    'count-card' => -1,
                    'count-user_pack' => -1,
                    'count-ss_user' => 1,
                    'footers' => false
                ];
                print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));

            */
            ?>
            </div>
        </div>
    </div>
<?php $view['slots']->stop();


$view['slots']->start('sincludes'); ?>

<?php $view['slots']->stop();


