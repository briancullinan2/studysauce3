<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Coupon;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use \DateTime as Date;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var User $user */
$user = $app->getUser();
$invites = !empty($user) ? $user->getInvites()->toArray() : [];

/** @var Invite $invite */

/** @var GlobalVariables $app */
$httpRequest = $app->getRequest();
$cart = explode(',', $httpRequest->cookies->get('cart'));
if(!is_array($cart)) {
    $cart = [];
}

?>
<div class="pack-icon">
    <div>
        <input type="hidden" name="childId" value="<?php print $invite->getInvitee()->getId(); ?>" />
    <label class="input childFirst">
        <span>Child&rsquo;s first name</span>
        <input type="text" name="childFirst" value="<?php print ($invite->getInvitee()->getFirst()); ?>" />
    </label>
    </div>
    <div>
    <label class="input childLast">
        <span>Child&rsquo;s last name</span>
        <input type="text" name="childLast" value="<?php print ($invite->getInvitee()->getLast()); ?>" />
    </label>
    </div>
    <?php
    $atLeastOne = false;
    foreach($invite->getInvitee()->getGroups()->toArray() as $g) {
        /** @var Group $g */
        foreach($results['invite-1'] as $publicInvite) {
            /** @var Invite $publicInvite */
            if($publicInvite->getGroup()->getId() == $g->getId()) {
                $atLeastOne = true; ?>
            <div>
                <?php print ($view->render('AdminBundle:Admin:register-child-group.html.php', ['context' => $context, 'invite' => $publicInvite, 'invites' => $results['invite-1']])); ?>
            </div>
            <?php
            }
        }
    }
    if(!$atLeastOne) { ?>
        <div>
            <?php print ($view->render('AdminBundle:Admin:register-child-group.html.php', ['context' => $context, 'invites' => $results['invite-1']])); ?>
        </div>
    <?php } ?>
    <label class="highlighted-link">
        <a href="#edit-invite" class="edit-icon">&nbsp;</a>
        <a href="#cancel-edit">Cancel</a>
        <button type="submit" value="#save-invite" class="more">Save</button>
    </label>
</div>


