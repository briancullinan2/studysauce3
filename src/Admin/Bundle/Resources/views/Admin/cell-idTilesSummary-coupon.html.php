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

/** @var Coupon $coupon */

/** @var GlobalVariables $app */
$httpRequest = $app->getRequest();
$cart = explode(',', $httpRequest->cookies->get('cart'));
if(!is_array($cart)) {
    $cart = [];
}

?>
<div class="pack-icon"><?php
    if(!empty($request['inCartOnly'])) { ?>
        <label class="input child">
            <select name="child">
                <option value="">- Select student -</option>
                <?php foreach ($invites as $invite) {
                    /** @var Invite $invite */
                    if (empty($invite->getInvitee())) {
                        continue;
                    }
                    ?>
                    <option value="<?php print ($invite->getInvitee()->getId()); ?>"><?php print (implode('', [$invite->getInvitee()->getFirst(), ' ', $invite->getInvitee()->getLast()])); ?></option>
                <?php } ?>
                <option value="<?php print ($user->getId()); ?>"><?php print (implode('', [$user->getFirst(), ' ', $user->getLast()])); ?></option>
            </select>
        </label>
    <?php }
    print ($view->render('AdminBundle:Admin:cell-id-coupon.html.php', ['coupon' => $coupon]));
    print ($view->render('AdminBundle:Admin:cell-title.html.php', ['entity' => $coupon, 'fields' => ['description']]));

    /** @var User|Group $ss_group */

    $cardCount = 0;
    foreach($coupon->getPacks()->toArray() as $p) {
        /** @var Pack $p */
        foreach($p->getCards()->toArray() as $c) {
            /** @var Card $c */
            if(!$c->getDeleted()) {
                $cardCount += 1;
            }
        }
    }

    ?>
    <label class="highlighted-link"><?php
        if(!empty($coupon->getOptions())) {
            foreach ($coupon->getOptions() as $o) {
                if (empty($o['description'])) {
                    if(!empty($request['inCartOnly'])) { ?>
                        <a href="#remove-coupon" data-value="<?php print ($view->escape($coupon->getName())); ?>">Remove</a>&nbsp;&nbsp;&nbsp;<h3><strong><?php print (implode('', ['&#36;' , $o['price']])); ?></strong></h3>
                    <?php }
                    else {
                        ?>
                    <button type="submit" class="btn" <?php print (in_array($coupon->getName(), $cart) ? 'disabled="disabled"' : ''); ?> value="<?php print ($coupon->getName()); ?>">
                        <?php print (in_array($coupon->getName(), $cart) ? 'In cart' : implode('', ['&#36;', $o['price'], ' Buy'])); ?></button><?php
                    }
                }
            }
        } ?>
    </label>
    <label><span><?php print ($cardCount); ?> cards</span></label>
</div>


