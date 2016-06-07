<?php use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var User $user */
$user = $app->getUser();
$invites = !empty($user) ? $user->getInvites()->toArray() : [];
?>

<aside id="right-panel" class="collapsed">
    <nav>
        <ul class="main-menu">
            <?php
            foreach ($invites as $invite) {
                /** @var Invite $invite */
                if (empty($invite->getInvitee())) {
                    continue;
                }
                ?>
                <li>
                    <a href="<?php print ($view['router']->generate('_welcome', ['_switch_user' => $invite->getInvitee()->getEmail()])); ?>"><?php print (implode('', [$invite->getInvitee()->getFirst(), ' ', $invite->getInvitee()->getLast()])); ?></a>
                </li>
            <?php }
            if (!empty($user) && $view['security']->isGranted('ROLE_PREVIOUS_ADMIN')) { ?>
                <li><a href="<?php print $view['router']->generate('_welcome'); ?>?_switch_user=_exit">Parent account</a></li>
            <?php } ?>
            <li><a href="#collapse">Hide</a><h3></h3></li>
            <li><a href="<?php print ($view['router']->generate('account')); ?>"><span>&nbsp;</span>Add Child</a></li>
            <li><a href="<?php print ($view['router']->generate('logout')); ?>"><span>&nbsp;</span>Logout</a></li>
        </ul>
    </nav>
</aside>