<div class="highlighted-link">
    <a title="Edit pack" href="<?php print $view['router']->generate('packs_edit', ['pack' => $pack->getId()]); ?>" class="edit-icon">&nbsp;</a>
    <a title="Remove pack" class="remove-icon" href="#confirm-remove" data-action="<?php print $view['router']->generate('save_group', ['groupId' => $searchRequest['ss_group-id'], 'packId' => $pack->getId(), 'groups' => [['remove' => 'true', 'id' => $searchRequest['ss_group-id']]]]); ?>" data-target="#confirm-remove" data-toggle="modal">&nbsp;</a>
</div>