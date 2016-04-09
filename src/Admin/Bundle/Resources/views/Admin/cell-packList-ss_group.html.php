<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var User|Group $ss_group */
$entityIds = [];
?>

<div>
    <?php
    foreach ($ss_group->getGroupPacks()->toArray() as $p) {
        /** @var Pack $p */
        if($p->getStatus() == 'DELETED') {
            continue;
        }
        ?>
        <a href="<?php print $view['router']->generate('packs_edit', ['pack' => $p->getId()]); ?>" class="pack-list"><?php print $p->getTitle(); ?>
            <span><?php print $p->getCards()->filter(function (Card $c) {
                    return !$c->getDeleted();
                })->count(); ?></span></a>
    <?php }
    foreach ($ss_group->getSubgroups()->toArray() as $g) {
        /** @var Group $g */
        if($g->getDeleted() || $g->getUsers()->count() == 0) {
            continue;
        }
        ?>
        <a href="<?php print $view['router']->generate('groups_edit', ['group' => $g->getId()]); ?>" class="pack-list"><?php print $g->getName(); ?>
            <span><?php print $g->getPacks()->filter(function (Pack $p) {
                    return !$p->getDeleted();
                })->count(); ?></span></a>
    <?php } ?>

</div>