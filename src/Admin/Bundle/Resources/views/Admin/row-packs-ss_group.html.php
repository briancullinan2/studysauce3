<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var User|Group $ss_group */
?>

<div>
    <a href="<?php print $view['router']->generate('packs'); ?>" class="big-add">Create
        <span>+</span> new pack</a><br/>
    <label class="input">
        <input type="text" name="packs" value=""
               data-tables="<?php print $view->escape(json_encode([
                   'pack' => ['title', 'userCountStr', 'cardCountStr', 'id']])); ?>"
               data-entities="<?php print $view->escape(json_encode($ss_group->getPacks()->map(function (Pack $p) {
                   return 'pack-' . $p->getId();
               })->toArray())); ?>" placeholder="Search for existing pack"/></label>
    <?php
    foreach ($ss_group->getPacks()->toArray() as $p) {
        /** @var Pack $p */
        ?>
        <div class="pack-list"><?php print $p->getTitle(); ?>
            <span><?php print $p->getCards()->filter(function (Card $c) {
                    return !$c->getDeleted();
                })->count(); ?></span></div>
    <?php }
    foreach ($ss_group->getSubgroups()->toArray() as $g) {
        /** @var Group $g */
        ?>
        <div class="pack-list"><?php print $g->getName(); ?>
            <span><?php print $g->getPacks()->filter(function (Pack $p) {
                    return !$p->getDeleted();
                })->count(); ?></span></div>
    <?php } ?>

</div>