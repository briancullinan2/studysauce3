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

$home = \StudySauce\Bundle\Controller\HomeController::getUserRedirect($user, $this->container);
$tables = [
    'ss_user' => ['id', 'first', 'last', 'email', 'roles', 'userPacks'],
    'invite' => ['first', 'last', 'invitee', 'email'],
    'user_pack' => ['pack', 'removed'],
    'pack' => ['id', 'status', 'firstCard', 'cartCount', 'ownerId']
];
$userJson = !empty($user) ? AdminController::toFirewalledEntityArray($user, $tables, 2) : (array)(new stdClass());

// get invites if in parents account
$invitesJson = [];
$invites = !empty($user) ? $user->getInvites()->toArray() : [];
foreach($invites as $i) {
    $invitesJson[count($invitesJson)] = AdminController::toFirewalledEntityArray($i, $tables, 3);
}

// get invitees if in child account
$tables = [
    'ss_user' => ['id', 'first', 'last', 'email', 'roles', 'invites', 'userPacks'],
    'invite' => ['first', 'last', 'user', 'email', 'invitee'],
    'user_pack' => ['pack', 'removed'],
    'pack' => ['id', 'status', 'firstCard', 'cartCount', 'ownerId']
];
$inviteesJson = [];
$invites = !empty($user) ? $user->getInvitees()->toArray() : [];
foreach($invites as $i) {
    $inviteesJson[count($inviteesJson)] = AdminController::toFirewalledEntityArray($i, $tables, 5);
}
?>
<div class="header-wrapper navbar navbar-inverse">
    <div class="header" data-user="<?php print $view->escape(json_encode($userJson + ['invites' => $invitesJson, 'invitees' => $inviteesJson, 'groups' => $allGroups])); ?>">
        <div id="site-name" class="container navbar-header">
            <a href="<?php print $view['router']->generate($home[0], $home[1]); ?>">
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
                    <?php if (!empty($user) && $user->hasRole('ROLE_ADMIN')) { ?>
                    <li><a href="<?php print $view['router']->generate('groups'); ?>"><span>&nbsp;</span>Groups</a></li>
                    <?php }
                    if(count($user->getUserPacks()->toArray()) || (!empty($user) && $user->hasRole('ROLE_ADMIN'))) { ?>
                        <li><a href="<?php print $view['router']->generate('packs'); ?>"><span>&nbsp;</span>Packs</a></li>
                    <?php } ?>
                    <li><a href="<?php print $view['router']->generate('store'); ?>"><span>&nbsp;</span>Store</a></li>
                    <?php if (!empty($user) && $user->hasRole('ROLE_ADMIN')) { ?>
                    <li><a href="<?php print $view['router']->generate('validation'); ?>"><span>&nbsp;</span>Validation</a></li>
                    <li><a href="<?php print $view['router']->generate('activity'); ?>"><span>&nbsp;</span>Activity</a></li>
                    <?php } ?>
                    <?php /*
                    <li><a href="<?php print $view['router']->generate('command'); ?>"><span>&nbsp;</span>Users</a></li>
                    <li><a href="<?php print $view['router']->generate('import'); ?>"><span>&nbsp;</span>Import</a></li>
                    <li><a href="<?php print $view['router']->generate('emails'); ?>"><span>&nbsp;</span>Emails</a></li>
                     */ ?>
                </ul>
            </nav>
        <?php }

        if($app->getRequest()->get('_format') != 'funnel') { ?>
            <div id="welcome-message">
                <?php if (!empty($user) && $user->hasRole('ROLE_ADMIN')) { ?>
                    <ul class="main-menu">
                        <li><a href="https://test.studysauce.com/"><span>&nbsp;</span>Testing</a></li>
                        <li><a href="https://staging.studysauce.com/"><span>&nbsp;</span>Staging</a></li>
                        <li><a href="https://cerebro.studysauce.com/"><span>&nbsp;</span>Cerebro</a></li>
                    </ul>
                <?php } ?>
                <a href="<?php print ($view['router']->generate('store_cart')); ?>"><?php
                    $cart = explode(',', $app->getRequest()->cookies->get('cart'));
                    if($cart[0] == '') {
                        array_splice($cart, 0, 1);
                    }
                    print (!empty($cart) ? count($cart) : '&nbsp;'); ?></a>
                <?php if($user->hasRole('ROLE_ADMIN')) { ?>
                    <label class="input"><input type="text" name="search" data-tables="<?php print $view->escape(json_encode(AdminController::$defaultMiniTables)); ?>" data-confirm="false" placeholder="Search" /></label>
                <?php } ?>

                <?php
                /*
                <a href="<?php print $view['router']->generate('logout'); ?>" title="Log out">logout</a></div>
                 */
                ?>
                <a href="#right-panel"><strong><?php print (!empty($user) ? $user->getFirst() : ''); ?></strong>&nbsp;</a></div>
            <div id="jquery_jplayer" style="width: 0; height: 0;"></div>
        <?php } ?>
    </div>
</div>
<?php
print ($view->render('StudySauceBundle:Shared:menu.html.php'));


