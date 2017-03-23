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

<div>
    <label><?php print (count($groups)); ?> groups / <?php print ($groupUsers + count($diffUsers)); ?> users / <?php print ($cardCount); ?> cards</label>
    <?php
    foreach ($groups as $p) {
        /** @var Group $p */
        if (count($p->getUsers()->toArray()) == 0) {
            continue;
        }
        ?>
        <a href="<?php print ($view['router']->generate('groups_edit', ['group' => $p->getId()])); ?>" class="pack-list"><?php print ($p->getName()); ?>
            <span><?php print (count($p->getUsers()->toArray())); ?></span></a>
    <?php }
    foreach ($diffUsers as $g) {
        /** @var User $g */
        ?>
        <a href="<?php print ($view['router']->generate('home_user', ['user' => $g->getId()])); ?>" class="pack-list"><?php print (implode('', [$g->getFirst() , ' ' , $g->getLast()])); ?>
            <span>1</span></a>
    <?php } ?>

</div>