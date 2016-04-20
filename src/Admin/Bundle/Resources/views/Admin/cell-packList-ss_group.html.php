<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */
$entityIds = [];

list($users, $packs) = $ss_group->getUserPacksRecursively();
?>

<div>
    <label><?php print count($packs); ?> packs / <?php print count($users); ?> users
        <span><?php if(count($ss_group->getSubgroups()->toArray()) == 0) {
            print 'No subgroups';
        }
        else {
            print $ss_group->getSubgroups()->map(function (Group $g) {
                return $g->getDeleted() ? 0 : $g->getSubgroups()->count() + 1;
            })->count() . ' subgroups';
        } ?>
        </span></label>
    <?php
    foreach ($ss_group->getSubgroups()->toArray() as $g) {
        /** @var Group $g */
        if($g->getDeleted()) {
            continue;
        }
        ?>
        <a href="<?php print $view['router']->generate('groups_edit', ['group' => $g->getId()]); ?>" class="pack-list"><?php print $g->getName(); ?>
            <span><?php print $g->getSubgroups()->filter(function (Group $p) {
                    return !$p->getDeleted();
                })->count(); // for self ?></span></a>
    <?php } ?>

</div>