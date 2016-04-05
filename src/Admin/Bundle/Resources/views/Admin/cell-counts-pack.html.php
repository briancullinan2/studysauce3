<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Pack;

/** @var Pack $pack */
?>
<label class="input">
    <span><?php print $pack->getUsers()->count(); ?></span>
</label>
<label class="input">
    <span><?php print $pack->getCards()->filter(function (Card $c) { return !$c->getDeleted(); })->count(); ?></span>
</label>
