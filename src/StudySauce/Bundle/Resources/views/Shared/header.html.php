<?php
use Admin\Bundle\Controller\AdminController;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\HttpFoundation\Session\Session;
/** @var GlobalVariables $app */
/** @var \Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine $view */

/** @var User $user */
$user = $app->getUser();

$allGroups = [];
AdminController::setUpClasses($this->container->get('doctrine')->getManager());
if(!empty($user)) {
    foreach ($user->getGroups()->toArray() as $g) {
        /** @var Group $g */
        $allGroups[count($allGroups)] = AdminController::toFirewalledEntityArray($g, [], 2);
    }
}

/** @var Session $session */
$session = $app->getSession();

// TODO: generalize this for other groups
if(!empty($user) && $user->hasGroup('Torch And Laurel') ||
    (!empty($session) && $session->has('organization') && $session->get('organization') == 'Torch And Laurel'))
{
    print $view->render('TorchAndLaurelBundle:Shared:header.html.php');
    return;
}

?>
<div class="header-wrapper navbar navbar-inverse">
    <div class="header">
        <div id="site-name" class="container navbar-header">
            <a title="Home" href="<?php print $view['router']->generate('_welcome'); ?>">
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/Study_Sauce_Logo.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="48" height="48" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                <?php endforeach; ?><span>Study Sauce</span></a>
        </div>
        <?php if($app->getRequest()->get('_format') == 'index' || ($app->getRequest()->get('_format') != 'funnel' &&
                !empty($user) && $user->hasRole('ROLE_PARTNER'))) { ?>
        <?php }

        if($app->getRequest()->get('_format') != 'funnel') { ?>
            <nav>
                <ul class="main-menu">
                    <li><a href="<?php print $view['router']->generate('home'); ?>"><span>&nbsp;</span>Home</a></li>
                    <li><a href="<?php print $view['router']->generate('groups'); ?>"><span>&nbsp;</span>Groups</a></li>
                    <li><a href="<?php print $view['router']->generate('packs'); ?>"><span>&nbsp;</span>Packs</a></li>
                    <?php /*
                    <li><a href="<?php print $view['router']->generate('command'); ?>"><span>&nbsp;</span>Users</a></li>
                    <li><a href="<?php print $view['router']->generate('import'); ?>"><span>&nbsp;</span>Import</a></li>
                    <li><a href="<?php print $view['router']->generate('emails'); ?>"><span>&nbsp;</span>Emails</a></li>
                    <li><a href="<?php print $view['router']->generate('validation'); ?>"><span>&nbsp;</span>Validation</a></li>
                    <li><a href="<?php print $view['router']->generate('activity'); ?>"><span>&nbsp;</span>Activity</a></li>
                    <li><a href="<?php print $view['router']->generate('account'); ?>"><span>&nbsp;</span>Account</a></li> */ ?>
                </ul>
            </nav>
        <?php }

        if($app->getRequest()->get('_format') != 'funnel') { ?>
            <div id="welcome-message" data-user="<?php print $view->escape(json_encode([
                    'id' => $user->getId(),
                    'email' => !empty($user) ? $user->getEmail() : '',
                    'groups' => $allGroups,
                    'roles' => $user->getRoles()])); ?>">
                <?php if (!empty($user) && $user->hasRole('ROLE_ADMIN') && $user->getEmail() == 'brian@studysauce.com') { ?>
                    <ul class="main-menu">
                        <li><a href="https://staging.studysauce.com/"><span>&nbsp;</span>Staging</a></li>
                        <li><a href="https://cerebro.studysauce.com/"><span>&nbsp;</span>Cerebro</a></li>
                    </ul>
                <?php } ?>
                <label class="input"><input type="text" name="search" data-tables="<?php print $view->escape(json_encode(AdminController::$defaultMiniTables)); ?>" data-confirm="false" placeholder="Search" /></label>
                <strong><?php print (!empty($user) ? $user->getFirst() : ''); ?></strong>
                <?php
                /*
                <a href="<?php print $view['router']->generate('logout'); ?>" title="Log out">logout</a></div>
                 */
                ?>
                <a href="#right-panel" title="Show/Hide menu">&nbsp;</a></div>
            <div id="jquery_jplayer" style="width: 0; height: 0;"></div>
        <?php } ?>
    </div>
</div>
<?php
print ($view->render('StudySauceBundle:Shared:menu.html.php'));


