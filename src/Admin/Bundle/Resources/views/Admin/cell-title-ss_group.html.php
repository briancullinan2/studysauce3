<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */
$usersGroupsPacks = $ss_group->getUsersPacksGroupsRecursively();
$users = $usersGroupsPacks[0];
$packs = $usersGroupsPacks[1];

if (isset($request['parent-ss_group-id']) && $ss_group->getId() == $request['parent-ss_group-id']) {
    print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => ['All users (not in subgroups below)', 0, 0]]));
} else { ?>
    <a href="<?php print ($view['router']->generate('groups_edit', ['group' => $ss_group->getId()])); ?>">
    <?php print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => [$ss_group->getName(), count($users), count($packs)]])); ?>
    </a>
<?php }