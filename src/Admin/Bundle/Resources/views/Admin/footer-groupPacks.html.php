<?php

use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;

/** @var Group[] $ss_group */
global $ss_group;
if (isset($searchRequest['ss_group-id']) && isset($ss_group[0])) {
    $packs = $ss_group[0]->getGroupPacks()->filter(function (Pack $p) {
        return $p->getStatus() != 'DELETED';})->map(function (Pack $p) {
        return 'pack-' . $p->getId();});
}

?>
<div class="add-packs">
    <a href="<?php print $view['router']->generate('packs_new'); ?>" class="big-add">Create
        <span>+</span> new pack</a><br/><br/>
    <?php
    /** @var Group[] $ss_group */
    if(isset($packs)) {
        print $this->render('AdminBundle:Admin:cell-collection.html.php', [
            'tables' => ['pack' => ['title','userCountStr','cardCountStr', 'id', 'status']],
            'entityIds' => $packs->toArray(),
            'dataConfirm' => false]);
        ?>
        <a href="#add-entity" title="Manage packs" data-target="#add-entity" data-toggle="modal" class="big-add"><span>+</span>&nbsp;</a>
    <?php } ?>
</div>
<header class="pack">
    <label>Study pack</label>
    <label>Members</label>
    <label>Cards</label>
</header>
<?php
if(isset($packs) && $packs->count() == 0) { ?>
    <div>No packs associated with this group</div>
<?php }