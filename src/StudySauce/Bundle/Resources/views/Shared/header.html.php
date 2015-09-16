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
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/logo_4_trans_2.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="48" height="48" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
                <?php endforeach; ?><span><strong>Study</strong> Sauce</span></a>
        </div>
        <?php if($app->getRequest()->get('_format') == 'index' || ($app->getRequest()->get('_format') != 'funnel' &&
                !empty($user) && $user->hasRole('ROLE_PARTNER'))) { ?>
            <div id="partner-message">
                <?php if($user->hasRole('ROLE_PARTNER')) {
                    if($user->getInvitedPartners()->exists(function ($k, PartnerInvite $p) {return !$p->getUser()->hasRole('ROLE_PAID');})) { ?>
                    <div class="highlighted-link">
                        <a href="<?php print $view['router']->generate('checkout'); ?>" class="more">Upgrade</a>
                    </div>
                <?php }
                } elseif(!empty($user) && !empty($partner = $user->getPartnerOrAdviser())) { ?>
                    <div class="partner-icon">
                        <?php if (empty($partner->getPhoto())) {
                            foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/empty-photo.png'],[],['output' => 'bundles/studysauce/images/*']) as $url): ?>
                                <img width="48" height="48" alt="Partner" src="<?php echo $view->escape($url) ?>"/>
                            <?php endforeach;
                        } else {
                            ?><img width="48" height="48" src="<?php echo $view->escape($partner->getPhoto()->getUrl()) ?>"
                                   alt="LOGO" />
                        <?php } ?>
                    </div>
                    <div>I am accountable to: <br><span><?php print $partner->getFirst(); ?> <?php print $partner->getLast(); ?></span></div>
                <?php } else { ?>
                    <div class="partner-icon">
                    <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/empty-photo.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                        <img width="48" height="48" alt="Partner" src="<?php echo $view->escape($url) ?>" />
                    <?php endforeach; ?>
                    </div>
                    <div>I am accountable to: <br><a href="<?php print $view['router']->generate('partner'); ?>">Click to set up</a></div>
                <?php } ?>
            </div>
        <?php } ?>
        <?php if($app->getRequest()->get('_format') != 'funnel') { ?>
            <div id="welcome-message"><strong><?php print (!empty($user) ? $user->getFirst() : ''); ?></strong>
                <a href="<?php print $view['router']->generate('plan_download'); ?>"><?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/plan-download-blue.gif'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                        <img width="40" height="40" src="<?php echo $view->escape($url) ?>" title="Click to download plan" />
                    <?php endforeach; ?></a>
                <a href="<?php print $view['router']->generate('logout'); ?>" title="Log out">logout</a></div>
            <div id="jquery_jplayer" style="width: 0; height: 0;"></div>
        <?php } ?>
    </div>
</div>
<?php


