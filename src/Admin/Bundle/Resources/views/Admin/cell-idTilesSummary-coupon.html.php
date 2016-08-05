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

/** @var Coupon $coupon */

/** @var GlobalVariables $app */
$httpRequest = $app->getRequest();
$cart = explode(',', $httpRequest->cookies->get('cart'));
if(!is_array($cart)) {
    $cart = [];
}

$alreadyHas = true;
foreach($coupon->getPacks()->toArray() as $p) {
    if(empty($user->getUserPack($p))) {
        $alreadyHas = false;
    }
}
?>
<div class="pack-icon"><?php
    if(!empty($request['inCartOnly'])) { ?>
        <label class="input child">
            <select name="child">
                <option value="">- Select student -</option>
                <option value="<?php print ($user->getId()); ?>" <?php print ($alreadyHas ? 'disabled="disabled"' : ''); ?>>
                    <?php print (implode('', [$user->getFirst(), ' ', $user->getLast()])); ?><?php print ($alreadyHas ? ' (Already assigned)' : ''); ?></option>
                <?php
                if(!empty($user)) {
                    foreach($user->getInvites()->toArray() as $childInvite) {
                        /** @var Invite $childInvite */
                        if (empty($childInvite->getInvitee())) {
                            continue;
                        }
                        $alreadyHas = true;
                        foreach($coupon->getPacks()->toArray() as $p) {
                            if(empty($childInvite->getInvitee()->getUserPack($p))) {
                                $alreadyHas = false;
                            }
                        }
                        ?><option value="<?php print ($childInvite->getInvitee()->getId()); ?>" <?php print ($alreadyHas ? 'disabled="disabled"' : ''); ?>>
                        <?php print (implode('', [$childInvite->getInvitee()->getFirst(), ' ', $childInvite->getInvitee()->getLast()])); ?><?php print ($alreadyHas ? ' (Already assigned)' : ''); ?></option><?php
                    }
                    foreach($user->getInvitees()->toArray() as $parentInvite) {
                        /** @var Invite $parentInvite */
                        if (empty($parentInvite->getUser())) {
                            continue;
                        }
                        foreach($parentInvite->getUser()->getInvites()->toArray() as $in) {
                            /** @var Invite $in */
                            if(empty($in->getInvitee()) || $in->getInvitee()->getId() == $user->getId()) {
                                continue;
                            }
                            $alreadyHas = true;
                            foreach($coupon->getPacks()->toArray() as $p) {
                                if(empty($in->getInvitee()->getUserPack($p))) {
                                    $alreadyHas = false;
                                }
                            }
                            ?><option value="<?php print ($in->getInvitee()->getId()); ?>" <?php print ($alreadyHas ? 'disabled="disabled"' : ''); ?>>
                            <?php print (implode('', [$in->getInvitee()->getFirst(), ' ', $in->getInvitee()->getLast()])); ?><?php print ($alreadyHas ? ' (Already assigned)' : ''); ?></option><?php
                        }
                        $alreadyHas = true;
                        foreach($coupon->getPacks()->toArray() as $p) {
                            if(empty($parentInvite->getUser()->getUserPack($p))) {
                                $alreadyHas = false;
                            }
                        }
                        ?><option value="<?php print ($parentInvite->getUser()->getId()); ?>" <?php print ($alreadyHas ? 'disabled="disabled"' : ''); ?>>
                        <?php print (implode('', [$parentInvite->getUser()->getFirst(), ' ', $parentInvite->getUser()->getLast()])); ?><?php print ($alreadyHas ? ' (Already assigned)' : ''); ?></option><?php
                    }
                }
                ?>
            </select>
        </label>
        <input type="hidden" name="coupon" value="<?php print ($view->escape($coupon->getName())); ?>" />
    <?php }
    print ($view->render('AdminBundle:Admin:cell-id-coupon.html.php', ['coupon' => $coupon]));
    print ($view->render('AdminBundle:Admin:cell-title.html.php', ['entity' => $coupon, 'fields' => ['description']]));

    /** @var User|Group $ss_group */

    $cardCount = $coupon->getCardCount();
    ?>
    <label class="highlighted-link"><?php
        if(!empty($coupon->getOptions())) {
            foreach ($coupon->getOptions() as $o) {
                if (empty($o['description'])) {
                    $price = $o['price'] > 0 ? implode('', ['&#36;' , number_format($o['price'], 2)]) : 'Free';
                    if(!empty($request['inCartOnly'])) { ?>
                        <a href="#remove-coupon">Remove</a>&nbsp;&nbsp;&nbsp;<h3><strong><?php print ($price); ?></strong></h3>
                    <?php }
                    else {
                        ?><button type="submit" class="btn" <?php print (in_array($coupon->getName(), $cart) ? 'disabled="disabled"' : ''); ?> value="<?php print ($coupon->getName()); ?>">
                        <?php print (in_array($coupon->getName(), $cart) ? 'In cart' : ($o['price'] > 0 ? implode('', [$price, ' Buy']) : $price)); ?></button><?php
                    }
                }
            }
        } ?>
    </label>
    <label><span><?php print ($cardCount); ?> cards</span></label>
</div>


