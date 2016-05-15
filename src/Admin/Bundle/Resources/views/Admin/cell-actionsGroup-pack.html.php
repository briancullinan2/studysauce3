<?php
use StudySauce\Bundle\Entity\Pack;

/** @var Pack $pack */

?>
<div class="highlighted-link">
    <a title="Remove pack/group membership"
       data-confirm="Are you sure you would like to remove the pack &ldquo;<?php print ($pack->getTitle()); ?>&rdquo; from this group?"
       class="remove-icon" href="#general-dialog"
       data-action="<?php print ($view['router']->generate('save_group', ['ss_group' => ['id' => $request['ss_group-id'], 'groupPacks' => ['id' => $pack->getId(), 'remove' => 'true']], 'tables' => ['ss_group' => ['groupPacks']]])); ?>"
       data-target="#general-dialog" data-toggle="modal">&nbsp;</a>
</div>