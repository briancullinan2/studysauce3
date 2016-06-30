<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use DateTime as Date;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var User $user */
$user = $app->getUser();

/** @var GlobalVariables $app */
$request = $app->getRequest();

/** @var Group $ss_group */

$subGroups = [$ss_group->getId()];
$countGroups = 0;
$countUsers = [];
$countPacks = [];
$groupPacks = [];
foreach($ss_group->getUsers()->toArray() as $u) {
    /** @var User $u */
    if(!in_array($u->getId(), $countUsers)) {
        $countUsers[count($countUsers)] = $u->getId();
    }
}
foreach($ss_group->getGroupPacks()->toArray() as $p) {
    /** @var Pack $p */
    if(!in_array($p->getId(), $countPacks) && $p->getStatus() != 'DELETED') {
        $countPacks[count($countPacks)] = $p->getId();
        $groupPacks[count($groupPacks)] = $p;
    }
}
$added = true;
while($added) {
    $added = false;
    foreach($results['allGroups'] as $g) {
        /** @var Group $g */
        if(!empty($g->getParent())
            && in_array($g->getParent()->getId(), $subGroups)
            && !in_array($g->getId(), $subGroups)) {
            $subGroups[count($subGroups)] = $g->getId();
            $countGroups += 1;
            foreach($g->getUsers()->toArray() as $u) {
                /** @var User $u */
                if(!in_array($u->getId(), $countUsers)) {
                    $countUsers[count($countUsers)] = $u->getId();
                }
            }
            foreach($g->getGroupPacks()->toArray() as $p) {
                /** @var Pack $p */
                if(!in_array($p->getId(), $countPacks) && $p->getStatus() != 'DELETED') {
                    $countPacks[count($countPacks)] = $p->getId();
                    $groupPacks[count($groupPacks)] = $p;
                }
            }
            $added = true;
        }
    }
}


?>

<div>
    <?php if($user->hasRole('ROLE_ADMIN')) { ?>
        <label><?php print ($countGroups); ?> subgroups / <?php print (count($groupPacks)); ?> packs / <?php print (count($countUsers)); ?> users</label>
    <?php }
    else { ?>
        <label><?php print (count($groupPacks)); ?> packs</label>
    <?php }
    foreach ($groupPacks as $g) {
        /** @var Pack $g */
        if($g->getDeleted()) {
            continue;
        }

        // get next summary process card
        $retention = $results['ss_user'][0]->getUserPacks()->toArray();
        $card = 0;
        foreach($retention as $up) {
            /** @var UserPack $up */
            if ($up->getRemoved() || $up->getPack()->getStatus() == 'DELETED' || $up->getPack()->getId() != $g->getId()) {
                continue;
            }

            $summaryDate = $request->cookies->get(implode('', ['retention_', $up->getPack()->getId()]));

            foreach ($up->getRetention() as $id => $r) {
                if (!$r[0] || empty($r[3]) || empty($summaryDate) || $summaryDate === 'false' || new Date($r[3]) < new Date($summaryDate)) {
                    $card = $id;
                    break;
                }
            }
        }

        ?>
        <a href="<?php print ($view['router']->generate('cards', ['card' => $card])); ?>" class="pack-list"><?php print ($view->escape($g->getTitle())); ?>
            <span><?php print (count($g->getCards()->toArray())); ?></span></a>
    <?php } ?>
</div>