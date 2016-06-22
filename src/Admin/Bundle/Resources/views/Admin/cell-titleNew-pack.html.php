<?php
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Controller\PacksController;
use StudySauce\Bundle\Entity\UserPack;

/** @var Pack $pack */
/** @var User $user */
$retention = [];
$isNew = true;
foreach($results['ss_user'][0]->getUserPacks()->toArray() as $i => $up) {
    /** @var UserPack $up */
    if($up->getPack()->getId() == $pack->getId()) {
        foreach($up->getRetention() as $i =>  $r) {
            if($r[2]) {
                $retention[count($retention)] = $i;
            }
            if(!empty($r[3])) {
                $isNew = false;
            }
        }
    }
}

$firstCard = array_shift($retention);
?>
<a data-retention="<?php print ($view->escape(json_encode($retention))); ?>" href="<?php print ($view['router']->generate('cards', ['card' => $firstCard])); ?>">
    <label><?php print ($isNew ? '<strong>New </strong>' : ''); ?>
        <span><?php print ($view->escape($pack->getTitle())); ?></span>
    </label>
    <label><?php print (count($retention)); ?></label>
</a>
