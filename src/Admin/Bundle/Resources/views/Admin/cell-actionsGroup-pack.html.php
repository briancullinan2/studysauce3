<?php
use StudySauce\Bundle\Entity\Pack;

/** @var Pack $pack */

?>
<div class="highlighted-link">
    <a title="Remove pack/group membership"
       data-dialog="Are you sure you would like to remove the pack &ldquo;<?php print $pack->getTitle(); ?>&rdquo; from this group?"
       class="remove-icon" href="#general-dialog" data-type="text"
       data-action="<?php print $view['router']->generate('save_group', ['groupId' => $searchRequest['ss_group-id'], 'packId' => $pack->getId(), 'ss_group' => [['remove' => 'true', 'id' => $searchRequest['ss_group-id']]]]); ?>"
       data-target="#general-dialog" data-toggle="modal">&nbsp;</a>
    <a title="Edit pack" href="<?php print $view['router']->generate('packs_edit', ['pack' => $pack->getId()]); ?>"
       class="edit-icon">&nbsp;</a>
</div>