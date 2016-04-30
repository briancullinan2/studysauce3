

<div class="highlighted-link">
    <a title="Edit group" href="<?php print $view['router']->generate('groups_edit', ['group' => $ss_group->getId()]); ?>" class="edit-icon">&nbsp;</a>
    <a href="#general-dialog" data-dialog="Are you sure you would like to delete the group &ldquo;<?php print $ss_group->getName(); ?>&rdquo; permanently?" class="remove-icon" data-action="<?php print $view['router']->generate('save_group', ['remove' => 'true', 'groupId' => $ss_group->getId()]); ?>" data-target="#general-dialog" data-toggle="modal">&nbsp;</a>
</div>
