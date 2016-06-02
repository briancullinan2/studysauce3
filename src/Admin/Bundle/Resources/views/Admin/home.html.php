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

$view['slots']->start('javascripts'); ?>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="home<?php print (!empty($id) && $user->getId() != $id ? implode('', ['-user' , $id]) : ''); ?>">
        <div class="pane-content">
            <div class="study-top">
            <div class="user-shuffle">
                <h2>Today&rsquo;s goal <?php print ($user->getId() != $id ? implode('', ['(' , $first , ' ' , $last , ')']) : ''); ?></h2>
                <a href="#shuffle-card" class="centerized"></a>
            <?php
            $tables = [
                'tables' => [
                    'ss_user' => ['id' => ['id', 'packs', 'userPacks']],
                    'pack' => ['titleNew' => ['id', 'title']],
                    'user_pack' => ['user', 'pack', 'removed', 'retention', 'downloaded']],
                //'user-ss_user-id' => 'NULL',
                'user_pack-removed' => false,
                'ss_user-id' => $id,
                'headers' => false,
                'count-pack' => 0,
                'read-only' => false,
                'count-ss_user' => 1,
                'count-user_pack' => -1,
                'footers' => false
            ];
            print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables)));
            ?>
            </div>
            <div class="study-log">
                <h2>Study log</h2>
                <?php
                $tables = [
                    'tables' => ['ss_user' => ['mastery' => 'id']],
                    //'user-ss_user-id' => 'NULL',
                    'ss_user-id' => $id,
                    'headers' => false,
                    'count-pack' => 0,
                    'count-ss_user' => -1,
                    'footers' => false
                ];
                print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables)));
                ?>
            </div>
            </div>
            <div class="study-mastery">
                <h2>Study mastery report</h2>
                <?php
                $tables = [
                    'tables' => ['pack' => ['id', 'title', 'packMastery', ['userPacks.user'], 'expandMastery'], 'ss_user' => ['id']],
                    //'user-ss_user-id' => 'NULL',
                    'classes' => ['last-right-expand'],
                    'ss_user-id' => $id,
                    'headers' => false,
                    'count-pack' => 0,
                    'count-ss_user' => -1,
                    'footers' => false
                ];
                print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables)));
                ?>
            </div>
        </div>
    </div>
<?php $view['slots']->stop();


$view['slots']->start('sincludes'); ?>

<?php $view['slots']->stop();


