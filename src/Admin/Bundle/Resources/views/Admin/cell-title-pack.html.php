<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */

if (isset($searchRequest['pack-id']) && $pack->getId() == $searchRequest['pack-id']) {
    print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => ['All users in this pack', 0, 0]]));
} else { ?>
    <a href="<?php print ($view['router']->generate('packs_edit', ['pack' => $pack->getId()])); ?>">
    <?php
    $users = $pack->getUsers()->filter(function (User $u) use ($pack) {
        if (!empty($up = $u->getUserPack($pack))) {
            return !empty($up->getDownloaded());
        }
        return false;
    });
    if (isset($searchRequest['ss_group-id']) && !empty($group = $searchRequest['ss_group-id'])) {
        $users = $users->filter(function (User $u) use ($group) {
            return $u->getGroups()->filter(function (Group $g) use ($group) {
                return $g->getId() == $group;
            })->count() > 0;
        });
    }

    print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => [
        $pack->getTitle(), $users->count(), $pack->getCards()->filter(function (Card $c) {
            return !$c->getDeleted();
        })->count()]]));
    ?>
    </a>
<?php }