<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var User|Group $ss_group */
$entityIds = [];
/** @var Pack $pack */

$groups = $pack->getGroups()->filter(function (Group $g) {return !$g->getDeleted();})->toArray();
$users = $pack->getUsers()->toArray();
$entityIds = [];
$groupIds = [];
$diffIds = [];
$diffUsers = array_values(array_filter($users, function (User $u) use (&$entityIds, $groups) {
    $entityIds[] = 'ss_user-' . $u->getId();
    return count(array_intersect($u->getGroups()->map(function (Group $g) {
        return $g->getId();
    })->toArray(), array_map(function (Group $g) {
        return $g->getId();
    }, $groups))) == 0;
}));
?>

<div>
    <label><?php print $pack->getCards()->filter(function (Card $c) {return !$c->getDeleted();})->count(); ?> cards</label>
    <?php
    foreach ($groups as $p) {
        /** @var Group $p */
        if ($p->getUsers()->count() == 0) {
            continue;
        }
        ?>
        <a href="<?php print $view['router']->generate('groups_edit', ['group' => $p->getId()]); ?>" class="pack-list"><?php print $p->getName(); ?>
            <span><?php print $p->getUsers()->count(); ?></span></a>
    <?php }
    foreach ($diffUsers as $g) {
        /** @var User $g */
        ?>
        <a href="#<?php //print $view['router']->generate('groups_edit', ['group' => $g->getId()]); ?>" class="pack-list"><?php print $g->getFirst() . ' ' . $g->getLast(); ?>
            <span>1</span></a>
    <?php } ?>

</div>