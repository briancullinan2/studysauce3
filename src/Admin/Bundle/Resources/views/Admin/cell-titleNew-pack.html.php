<?php
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Controller\PacksController;
use StudySauce\Bundle\Entity\UserPack;

/** @var Pack $pack */
/** @var User $user */
$retentionCount = 0;
$isNew = true;
$id = 0;
foreach($results['ss_user'][0]->getUserPacks()->toArray() as $i => $up) {
    /** @var UserPack $up */
    if($up->getPack()->getId() == $pack->getId()) {
        foreach($up->getRetention() as $i =>  $r) {
            if($r[2]) {
                if(empty($id)) {
                    $id = $i;
                }
                $retentionCount += 1;
            }
            if(!empty($r[3])) {
                $isNew = false;
            }
        }
    }
}
?>
<a href="<?php print ($view['router']->generate('cards', ['card' => $id])); ?>">
    <label><?php print ($isNew ? '<strong>New </strong>' : ''); ?><span><?php print ($view->escape($pack->getTitle())); ?></span></label><label><?php print ($retentionCount); ?></label>
</a>