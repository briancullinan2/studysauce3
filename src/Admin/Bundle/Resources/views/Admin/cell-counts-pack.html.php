<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */
?>
<label class="input">
    <span><?php
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
        print $users->count(); ?></span>
</label>
<label class="input">
    <span><?php print $pack->getCards()->filter(function (Card $c) {
            return !$c->getDeleted();
        })->count(); ?></span>
</label>
