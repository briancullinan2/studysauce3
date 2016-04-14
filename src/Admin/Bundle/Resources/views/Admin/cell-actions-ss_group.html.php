<div class="highlighted-link">
    <a title="Edit group" href="<?php print $view['router']->generate('groups_edit', ['group' => $ss_group->getId()]); ?>" class="edit-icon">&nbsp;</a>
    <a href="#confirm-remove" class="remove-icon" data-url="<?php print $view['router']->generate('save_group', ['remove' => true, 'groupId' => $ss_group->getId()]); ?>" data-target="#confirm-remove" data-toggle="modal">&nbsp;</a>
</div>
