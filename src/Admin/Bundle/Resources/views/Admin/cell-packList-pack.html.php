<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var User|Group $ss_group */
$entityIds = [];
/** @var Pack $pack */

$groups = $pack->getGroups()->toArray();
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
    <?php
    foreach ($groups as $p) {
        /** @var Group $p */
        ?>
        <a href="<?php print $view['router']->generate('packs_edit', ['pack' => $p->getId()]); ?>" class="pack-list"><?php print $p->getName(); ?>
            <span><?php print $p->getUsers()->count(); ?></span></a>
    <?php }
    foreach ($diffUsers as $g) {
        /** @var User $g */
        ?>
        <a href="<?php print $view['router']->generate('groups_edit', ['group' => $g->getId()]); ?>" class="pack-list"><?php print $g->getFirst() . ' ' . $g->getLast(); ?>
            <span>1</span></a>
    <?php } ?>

</div>