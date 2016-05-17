<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */
$subGroups = [];
$countUsers = count($ss_group->getUsers()->toArray());
$countPacks = count($ss_group->getPacks()->toArray());
$added = true;
while($added) {
    $added = false;
    foreach($results['allGroups'] as $g) {
        /** @var Group $g */
        if(!empty($g->getParent())
            && ($g->getParent()->getId() == $ss_group->getId() || in_array($g->getParent()->getId(), $subGroups))
            && !in_array($g->getId(), $subGroups)) {
            $subGroups[count($subGroups)] = $g->getId();
            $countUsers += count($g->getUsers()->toArray());
            $countPacks += count($g->getPacks()->toArray());
            $added = true;
        }
    }
}

if (isset($request['parent-ss_group-id']) && $ss_group->getId() == $request['parent-ss_group-id']) {
    print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => ['All users (not in subgroups below)', 0, 0]]));
} else { ?>
    <a href="<?php print ($view['router']->generate('groups_edit', ['group' => $ss_group->getId()])); ?>">
    <?php print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => [$ss_group->getName(), $countUsers, $countPacks]])); ?>
    </a>
<?php }