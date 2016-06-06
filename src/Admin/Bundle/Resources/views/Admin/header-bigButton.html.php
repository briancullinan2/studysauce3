<?php

/** @var GlobalVariables $app */
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var User $user */

$user = $app->getUser();
$retention = [];
foreach($results['ss_user'][0]->getUserPacks()->toArray() as $i => $up) {
    /** @var UserPack $up */
    foreach($up->getRetention() as $i =>  $r) {
        if($r[2]) {
            $retention[count($retention)] = $i;
        }
        if(!empty($r[3])) {
            $isNew = false;
        }
    }
    if(!empty($up->getDownloaded()) && !$up->getRemoved()) {
        $isNew = false;
    }
}
?>
<header class="<?php print ($table); ?>">
    <a href="<?php print ($view['router']->generate('cards', ['card' => $retention[0]])); ?>" class="centerized"></a>
</header>