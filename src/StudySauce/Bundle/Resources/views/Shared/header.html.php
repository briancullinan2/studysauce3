<?php
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\HttpFoundation\Session\Session;
/** @var GlobalVariables $app */

/** @var User $user */
$user = $app->getUser();

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
        <?php } ?>
        <nav>
            <ul class="main-menu">
                <li><a href="<?php print $view['router']->generate('command'); ?>"><span>&nbsp;</span>Users</a></li>
                <li><a href="<?php print $view['router']->generate('groups'); ?>"><span>&nbsp;</span>Groups</a></li>
                <li><a href="<?php print $view['router']->generate('packs'); ?>"><span>&nbsp;</span>Packs</a></li>
                <li><a href="<?php print $view['router']->generate('import'); ?>"><span>&nbsp;</span>Import</a></li>
                <li><a href="<?php print $view['router']->generate('emails'); ?>"><span>&nbsp;</span>Emails</a></li>
                <li><a href="<?php print $view['router']->generate('validation'); ?>"><span>&nbsp;</span>Validation</a></li>
                <li><a href="<?php print $view['router']->generate('activity'); ?>"><span>&nbsp;</span>Activity</a></li>
                <li><a href="<?php print $view['router']->generate('results'); ?>"><span>&nbsp;</span>Results</a></li>
                <li><a href="<?php print $view['router']->generate('account'); ?>"><span>&nbsp;</span>Account</a></li>
            </ul>
        </nav>
        <?php if($app->getRequest()->get('_format') != 'funnel') { ?>
            <div id="welcome-message">
                <?php if (!empty($user) && $user->hasRole('ROLE_ADMIN') && $user->getEmail() == 'brian@studysauce.com') { ?>
                    <ul class="main-menu">
                        <li><a href="https://staging.studysauce.com/"><span>&nbsp;</span>Staging</a></li>
                        <li><a href="https://cerebro.studysauce.com/"><span>&nbsp;</span>Cerebro</a></li>
                    </ul>
                <?php } ?>
                <label class="input"><input type="text" name="search" data-tables="<?php print $view->escape(json_encode([
                        'pack' => ['title', 'userCountStr', 'cardCountStr', 'id', 'status'],
                        'ss_user' => ['first', 'last', 'email', 'id', 'deleted'],
                        'ss_group' => ['name', 'userCountStr', 'description', 'id', 'deleted']])); ?>" placeholder="Search" /></label>
                <strong><?php print (!empty($user) ? $user->getFirst() : ''); ?></strong>
                <a href="<?php print $view['router']->generate('logout'); ?>" title="Log out">logout</a></div>
            <div id="jquery_jplayer" style="width: 0; height: 0;"></div>
        <?php } ?>
    </div>
</div>
<?php


