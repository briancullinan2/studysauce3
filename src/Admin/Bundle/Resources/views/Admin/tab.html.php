<?php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/admin.css'], [], ['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
?>
    <style>
        <?php foreach($tables as $table => $t) { ?>
        #command.<?php print $table; ?>-headings header > .<?php print $table; ?> {
            display: inline-block;
            opacity: 1;
            visibility: visible;
        }
        #command.<?php print $table; ?>-headings header > h2.<?php print $table; ?> {
            display: block;
            opacity: 1;
            visibility: visible;
        }
        <?php } ?>
    </style>
<?php
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/admin.js'], [], ['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="command">
        <div class="pane-content">
            <?php
                print $view->render('AdminBundle:Admin:results.html.php', compact('tables', array_keys($tables), array_map(function ($t) { return $t . '_total'; }, array_keys($tables))));
            ?>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
print $this->render('AdminBundle:Dialogs:confirm-remove-user.html.php', ['id' => 'confirm-remove-user']);
print $this->render('AdminBundle:Dialogs:confirm-password-reset.html.php', ['id' => 'confirm-password-reset']);
print $this->render('AdminBundle:Dialogs:confirm-cancel-user.html.php', ['id' => 'confirm-cancel-user']);
print $this->render('AdminBundle:Dialogs:add-user.html.php', ['id' => 'add-user']);
$view['slots']->stop();
