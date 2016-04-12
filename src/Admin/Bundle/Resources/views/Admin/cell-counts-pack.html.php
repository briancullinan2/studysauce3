<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
$request = $app->getRequest();

/** @var Pack $pack */
?>
<label class="input">
    <span><?php
        $users = $pack->getUsers();
            if(!empty($group = $request->get('ss_group-id'))) {
                $users = $users->filter(function (User $u) use ($group) {
                    return $u->getGroups()->filter(function (Group $g) use ($group) {return $g->getId() == $group;})->count() > 0;});
            }
            print $users->count(); ?></span>
</label>
<label class="input">
    <span><?php print $pack->getCards()->filter(function (Card $c) { return !$c->getDeleted(); })->count(); ?></span>
</label>
