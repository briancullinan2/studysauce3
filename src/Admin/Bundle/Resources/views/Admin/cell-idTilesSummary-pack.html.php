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

    ?><label><span><?php print ($pack->getCardCount()); ?> cards</span></label>
</a>


