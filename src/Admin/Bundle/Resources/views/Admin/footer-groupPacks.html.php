<?php

use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;

/** @var Group[] $ss_group */
global $subPacks;
if(empty($subPacks)) { ?>
    <div class="empty-packs">No packs in this group or all subgroups</div>
<?php
}
?>
<div class="highlighted-link form-actions <?php print $table; ?>">
    <a href="<?php print $view['router']->generate('packs_new'); ?>" class="big-add">Create
        <span>+</span> new pack</a><br/>
    <?php
    /** @var Group[] $ss_group */
    print $this->render('AdminBundle:Admin:cell-collection.html.php', [
        'tables' => ['pack' => ['title','userCountStr','cardCountStr', 'id', 'status']],
        'entityIds' => isset($subPacks) ? $subPacks : [],
        'dataConfirm' => false]);
    ?>
    <a href="#add-entity" title="Manage packs" data-target="#add-entity" data-toggle="modal" class="big-add"><span>+</span>&nbsp;</a>
</div>

