

<div class="highlighted-link">
    <?php
    if(isset($searchRequest['pack-id']) && !empty($group = $searchRequest['pack-id'])) { ?>
        <a href="#general-dialog" data-dialog="Are you sure you would like to remove the group &ldquo;<?php print $ss_group->getName(); ?>&rdquo; from the pack?" class="remove-icon" data-action="<?php print $view['router']->generate('save_group', ['groupId' => $searchRequest['pack-id'], 'packId' => $ss_group->getId(), 'ss_group' => [['remove' => 'true', 'id' => $searchRequest['pack-id']]]]); ?>" data-target="#general-dialog" data-toggle="modal">&nbsp;</a>
    <?php }
    else { ?>
        <a href="#general-dialog" data-dialog="Are you sure you would like to delete the group &ldquo;<?php print $ss_group->getName(); ?>&rdquo; permanently?" class="remove-icon" data-action="<?php print $view['router']->generate('save_group', ['remove' => 'true', 'groupId' => $ss_group->getId()]); ?>" data-target="#general-dialog" data-toggle="modal">&nbsp;</a>
    <?php } ?>
    <a title="Edit group" href="<?php print $view['router']->generate('groups_edit', ['group' => $ss_group->getId()]); ?>" class="edit-icon">&nbsp;</a>
</div>
