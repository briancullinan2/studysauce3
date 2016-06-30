<?php use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Role\SwitchUserRole;

/** @var User $user */
$user = $app->getUser();
$invites = !empty($user) ? $user->getInvites()->toArray() : [];
/** @var TokenInterface $token */
$token = $this->container->get('security.token_storage')->getToken();
if(!empty($token)) {
    foreach ($token->getRoles() as $role) {
        if ($role instanceof SwitchUserRole) {
            $parentToken = $role->getSource();
        }
    }

    if (!empty($parentToken) && !empty($parentToken->getUser())) {
        /** @var User $parentUser */
        $parentUser = $parentToken->getUser();
        foreach ($user->getInvitees()->toArray() as $p) {
            /** @var Invite $p */
            if ($parentUser->getUsername() == $p->getUser()->getUsername()) {
                $invites = $p->getUser()->getInvites()->toArray();
                break;
            }
        }
    }
}

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
                <li><a href="<?php print $view['router']->generate('_welcome'); ?>?_switch_user=_exit"><?php print (empty($p) ? 'Switch back' : implode('', [$p->getUser()->getFirst(), ' ', $p->getUser()->getLast()])); ?></a></li>
            <?php } ?>
            <li><a href="#collapse">Hide</a><h3></h3></li>
            <li><a href="<?php print ($view['router']->generate('account')); ?>"><span>&nbsp;</span>Add Child</a></li>
            <li><a href="<?php print ($view['router']->generate('account')); ?>"><span>&nbsp;</span>Account settings</a></li>
            <li><a href="<?php print ($view['router']->generate('logout')); ?>"><span>&nbsp;</span>Logout</a></li>
        </ul>
    </nav>
</aside>