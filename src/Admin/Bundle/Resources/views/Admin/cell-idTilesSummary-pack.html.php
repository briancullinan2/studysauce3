<a href="<?php print ($view['router']->generate('packs_edit', ['pack' => $pack->getId()])); ?>" class="pack-icon">
    <?php print ($view->render('AdminBundle:Admin:cell-id-pack.html.php', ['pack' => $pack])); ?>
    <?php print ($view->render('AdminBundle:Admin:cell-title.html.php', ['entity' => $pack, 'fields' => ['title']])); ?>
    <?php
    use StudySauce\Bundle\Entity\Card;
    use StudySauce\Bundle\Entity\Group;
    use StudySauce\Bundle\Entity\Pack;
    use StudySauce\Bundle\Entity\User;

    /** @var User|Group $ss_group */
    /** @var Pack $pack */

    $groups = [];
    $groupIds = [];
    $groupUsers = 0;
    foreach($pack->getGroups()->toArray() as $g) {
        /** @var Group $g */
        if(!$g->getDeleted()) {
            $groups[count($groups)] = $g;
            $groupIds[count($groupIds)] = $g->getId();
            $groupUsers += count($g->getUsers()->toArray());
        }
    }
    /** @var User[] $users */
    $users = $pack->getUsers()->toArray();
    // only show the users not included in any groups
    $diffUsers = [];
    foreach($users as $u) {
        $shouldExclude = false;
        foreach($u->getGroups()->toArray() as $g) {
            if(in_array($g->getId(), $groupIds)) {
                $shouldExclude = true;
            }
        }
        if(!$shouldExclude) {
            $diffUsers[count($diffUsers)] = $u;
        }
    }

    $cardCount = 0;
    foreach($pack->getCards()->toArray() as $c) {
        /** @var Card $c */
        if(!$c->getDeleted()) {
            $cardCount += 1;
        }
    }

    ?>
    <label><?php print (count($groups)); ?> groups / <?php print ($groupUsers + count($diffUsers)); ?> users / <?php print ($cardCount); ?> cards</label>
</a>


