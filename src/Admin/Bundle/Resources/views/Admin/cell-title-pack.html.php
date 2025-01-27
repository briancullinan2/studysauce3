<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */
$userCount = 0;
foreach($pack->getUsers()->toArray() as $u) {
    /** @var User $u */
    if (!empty($up = $pack->getUserPack($u))) {
        $userCount += !empty($up->getDownloaded()) ? 1 : 0;
    }
}

if (isset($request['pack-id']) && $pack->getId() == $request['pack-id']) {
    print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => ['All users (not in subgroups below)', $userCount, 0]]));
} else { ?>
    <a href="<?php print ($view['router']->generate('packs_edit', ['pack' => $pack->getId()])); ?>">
    <?php

    if (isset($request['ss_group-id']) && !empty($group = $request['ss_group-id'])) {
        $userGroupCount = 0;
        foreach($pack->getUsers()->toArray() as $u) {
            if(!empty($up = $pack->getUserPack($u)) && !empty($up->getDownloaded())) {
                /** @var User $u */
                foreach($u->getGroups()->toArray() as $g) {
                    /** @var Group $g */
                    if($g->getId() == $request['ss_group-id']) {
                        $userGroupCount += 1;
                        break;
                    }
                }
            }
        }
        $userCount = $userGroupCount;
    }

    print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => [$pack->getTitle(), $userCount, $pack->getCardCount()]]));
    ?>
    </a>
<?php }