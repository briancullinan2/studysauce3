<?php

/** @var GlobalVariables $app */
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

$httpRequest = $app->getRequest();
$user = $app->getUser();
$total = 0;
$firstCard = 0;
$retention = $results['ss_user'][0]->getUserPacks()->toArray();
$allUserPacks = $app->getUser()->getUserPacks()->toArray();
foreach($retention as $up) {
    /** @var UserPack $up */
    if ($up->getRemoved() || $up->getPack()->getStatus() == 'DELETED' || ($up->getPack()->getStatus() == 'UNPUBLISHED' && $up->getPack()->getOwnerId() != $user->getId())) {
        continue;
    }
    $hasUp = false;
    foreach($allUserPacks as $ur => $upr) {
        /** @var UserPack $upr */
        if($up->getPack()->getId() == $upr->getPack()->getId()) {
            $upr->setRetention($up->getRetention());
            $hasUp = true;
        }
    }
    if(!$hasUp) {
        $allUserPacks = array_merge($allUserPacks, [$up]);
    }
}
$user->userPacks = $allUserPacks;
jQuery('.header')->data('user', $user);

foreach($results['pack'] as $pack) {
    /** @var Pack $pack */
    /** @var UserPack $up */
    if($pack->getStatus() == 'DELETED' || ($pack->getStatus() == 'UNPUBLISHED' && $pack->getOwnerId() != $user->getId())) {
        continue;
    }
    $up = $user->getUserPack($pack);
    if($pack->getStatus() != 'PUBLIC' && (empty($up) || $up->getRemoved())) {
        continue;
    }
    if(!empty($up)) {
        foreach ($up->getRetention() as $i => $r) {
            if ($r[2]) {
                if (empty($firstCard)) {
                    $firstCard = $i;
                }
                $total += 1;
            }
        }
    }
    else {
        $total += intval($pack->getCardCount());
        if (empty($firstCard)) {
            $firstCard = $pack->getFirstCard()->getId();
        }
    }
}

?>
<header class="<?php print ($table); ?> <?php print ($total == 0 ? 'disabled' : ''); ?>">
    <a href="<?php print ($view['router']->generate('cards', ['card' => $firstCard])); ?>"></a>
    <label>Today&rsquo;s cards</label>
    <label><?php print ($total); ?> card<?php print ($total != 1 ? 's' : ''); ?></label>
    <?php if($total == 0) { ?><h3>No packs due today.  Check out the store.</h3><?php } ?>
</header>



