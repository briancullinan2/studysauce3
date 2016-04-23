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
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/results.css'], [], ['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/home.css',],[],['output' => 'bundles/studysauce/css/*.css']) as $url):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts'); ?>
<?php foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/results.js'], [], ['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="home<?php print (!empty($id) && $user->getId() != $id ? ('-user' . $id) : ''); ?>">
        <div class="pane-content">
            <div class="study-top">
            <div class="user-shuffle">
                <h2>Today's goal <?php print ($user->getId() != $id ? ('(' . $first . ' ' . $last . ')') : ''); ?></h2>
                <a href="#shuffle-card" class="centerized">&nbsp;</a>
            <?php
            $tables = [
                'tables' => ['pack' => ['titleNew' => ['title'], 'retention', ['user', 'userPacks.user']], 'ss_user' => ['id']],
                //'user-ss_user-id' => 'NULL',
                'ss_user-id' => $id,
                'headers' => false,
                'count-pack' => 0,
                'count-ss_user' => -1,
                'footers' => false
            ];
            print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables));
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
                print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables));
                ?>
            </div>
            </div>
            <div class="study-mastery">
                <h2>Study mastery report</h2>
                <?php
                $tables = [
                    'tables' => ['pack' => ['id', 'title', 'packMastery', ['user', 'userPacks.user'], 'cardMastery'], 'ss_user' => ['id']],
                    //'user-ss_user-id' => 'NULL',
                    'ss_user-id' => $id,
                    'headers' => false,
                    'count-pack' => 0,
                    'count-ss_user' => -1,
                    'footers' => false
                ];
                print $view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $tables));
                ?>
            </div>
        </div>
    </div>
<?php $view['slots']->stop();


$view['slots']->start('sincludes'); ?>

<?php $view['slots']->stop();