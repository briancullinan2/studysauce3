<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use \DateTime as Date;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
/** @var User $user */
$user = $app->getUser();

/** @var Pack $pack */

/** @var GlobalVariables $app */
$request = $app->getRequest();

// get next summary process card
$retention = $results['ss_user'][0]->getUserPacks()->toArray();
$card = 0;
foreach($retention as $up) {
    /** @var UserPack $up */
    if ($up->getRemoved() || $up->getPack()->getStatus() == 'DELETED' || $up->getPack()->getId() != $pack->getId()) {
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
<a href="<?php print ($view['router']->generate('cards', ['card' => $card])); ?>" class="pack-icon"><?php
    print ($view->render('AdminBundle:Admin:cell-id-pack.html.php', ['pack' => $pack]));
    print ($view->render('AdminBundle:Admin:cell-title.html.php', ['entity' => $pack, 'fields' => ['title']]));

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

    if($user->hasRole('ROLE_ADMIN')) { ?>
        <label><span><?php print (count($groups)); ?> groups / <?php print ($groupUsers + count($diffUsers)); ?> users / <?php print ($cardCount); ?> cards</span></label>
    <?php }
    else { ?>
        <label><span><?php print ($cardCount); ?> cards</span></label>
    <?php } ?>
</a>


