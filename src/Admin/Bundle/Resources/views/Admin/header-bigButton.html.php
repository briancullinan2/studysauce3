<?php

/** @var GlobalVariables $app */
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var User $user */
$httpRequest = $app->getRequest();
$user = $app->getUser();
$remaining = [];
$total = 0;
$retentionObj = [];
foreach($results['ss_user'][0]->getUserPacks()->toArray() as $i => $up) {
    /** @var UserPack $up */
    if($up->getRemoved() || $up->getPack()->getStatus() == 'DELETED' || $up->getPack()->getStatus() == 'UNPUBLISHED') {
        continue;
    }
    $retentionObj[count($retentionObj)] = AdminController::toFirewalledEntityArray($up, $request['tables'], 1);
    foreach($up->getRetention() as $i =>  $r) {
        if($r[2]) {
            $remaining[count($remaining)] = $i;
            $total += 1;
        }
        if(!empty($r[3])) {
            $isNew = false;
        }
    }
    if(!empty($up->getDownloaded()) && !$up->getRemoved()) {
        $isNew = false;
    }
}

$firstCard = array_shift($remaining);

?>
<header data-remaining="<?php print ($view->escape(json_encode($remaining))); ?>" data-retention="<?php print ($view->escape(json_encode($retentionObj))); ?>" class="<?php print ($table); ?> <?php print ($total == 0 ? 'disabled' : ''); ?>">
    <a href="<?php print ($view['router']->generate('cards', ['card' => $firstCard])); ?>"></a>
    <label>Today&rsquo;s cards</label>
    <label><?php print ($total); ?> card<?php print ($total != 1 ? 's' : ''); ?></label>
    <?php if($total == 0) { ?><h3>No packs due today.  Check out the store.</h3><?php } ?>
</header>



