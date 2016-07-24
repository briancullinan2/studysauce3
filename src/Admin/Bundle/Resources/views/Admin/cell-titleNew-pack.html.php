<?php
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Controller\PacksController;
use StudySauce\Bundle\Entity\UserPack;

/** @var Pack $pack */
/** @var User $user */
$retention = [];
$isNew = true;
$retentionCount = $pack->getCardCount();
foreach($results['ss_user'][0]->getUserPacks()->toArray() as $i => $up) {
    /** @var UserPack $up */
    if($up->getPack()->getId() == $pack->getId()) {
        $retentionCount = 0;
        $refresh = false;
        $retentionCache = $up->getRetention($refresh);
        if($refresh) {
            $orm = $this->container->get('doctrine')->getManager();
            $orm->merge($up);
            $orm->flush();
        }
        foreach($retentionCache as $i =>  $r) {
            if($r[2]) {
                $retention[count($retention)] = $i;
                $retentionCount++;
            }
            if(!empty($r[3])) {
                $isNew = false;
            }
        }
    }
}

?>
<a href="<?php print ($view['router']->generate('cards', ['card' => count($retention) > 0 ? $retention[0] : 0])); ?>">
    <label><?php print ($isNew ? '<strong>New </strong>' : ''); ?>
        <span><?php print ($view->escape($pack->getTitle())); ?></span>
    </label>
    <label><?php print ($retentionCount); ?></label>
</a>
