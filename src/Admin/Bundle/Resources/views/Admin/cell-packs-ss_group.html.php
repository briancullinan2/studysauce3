<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var User|Group $ss_group */
$entityIds = [];
?>

<div>
    <a href="<?php print $view['router']->generate('packs_new'); ?>" class="big-add">Create
        <span>+</span> new pack</a><br/>
    <label class="input">
        <input type="text" name="packs" value=""
               data-pack="<?php print $view->escape(json_encode(array_map(function (Pack $u) use (&$entityIds) {
                   $entityIds[] = 'pack-' . $u->getId();
                   return [
                       'table' => 'pack',
                       'value' => 'pack-' . $u->getId(),
                       'text' => $u->getTitle() . ' ' . $u->getUserCountStr(),
                       0 => $u->getCardCountStr()
                   ];
               }, $ss_group->getPacks()->toArray()))); ?>"
               data-tables="<?php print $view->escape(json_encode([
                   'pack' => ['title', 'userCountStr', 'cardCountStr', 'id', 'status']])); ?>"
               data-entities="<?php print $view->escape(json_encode($entityIds)); ?>" placeholder="Search for existing pack"/></label>
    <?php
    foreach ($ss_group->getGroupPacks()->toArray() as $p) {
        /** @var Pack $p */
        ?>
        <a href="<?php print $view['router']->generate('packs_edit', ['pack' => $p->getId()]); ?>" class="pack-list"><?php print $p->getTitle(); ?>
            <span><?php print $p->getCards()->filter(function (Card $c) {
                    return !$c->getDeleted();
                })->count(); ?></span></a>
    <?php }
    foreach ($ss_group->getSubgroups()->toArray() as $g) {
        /** @var Group $g */
        ?>
        <a href="<?php print $view['router']->generate('groups_edit', ['group' => $g->getId()]); ?>" class="pack-list"><?php print $g->getName(); ?>
            <span><?php print $g->getPacks()->filter(function (Pack $p) {
                    return !$p->getDeleted();
                })->count(); ?></span></a>
    <?php } ?>

</div>