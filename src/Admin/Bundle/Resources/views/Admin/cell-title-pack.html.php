<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */

if (isset($request['pack-id']) && $pack->getId() == $request['pack-id']) {
    print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => ['All users (not in subgroups below)', 0, 0]]));
} else { ?>
    <a href="<?php print ($view['router']->generate('packs_edit', ['pack' => $pack->getId()])); ?>">
    <?php
    $userCount = 0;
    foreach($pack->getUsers()->toArray() as $u) {
        /** @var User $u */
        if (!empty($up = $u->getUserPack($pack))) {
            $userCount += !empty($up->getDownloaded()) ? 1 : 0;
        }
    }
    if (isset($request['ss_group-id']) && !empty($group = $request['ss_group-id'])) {
        $userGroupCount = 0;
        foreach($pack->getUsers()->toArray() as $u) {
            if(!empty($up = $u->getUserPack($pack)) && !empty($up->getDownloaded())) {
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

    $cardCount = 0;
    foreach($pack->getCards()->toArray() as $c) {
        /** @var Card $c */
        $cardCount += !$c->getDeleted() ? 1 : 0;
    }

    print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => [$pack->getTitle(), $userCount, $cardCount]]));
    ?>
    </a>
<?php }