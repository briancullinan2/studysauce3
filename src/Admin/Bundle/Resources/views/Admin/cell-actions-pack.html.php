<div class="highlighted-link">
    <a title="Edit pack" href="<?php print $view['router']->generate('packs_edit', ['pack' => $pack->getId()]); ?>" class="edit-icon">&nbsp;</a>
    <a title="Remove pack" href="#confirm-remove" class="remove-icon" data-action="<?php print $view['router']->generate('packs_remove', ['id' => $pack->getId()]); ?>" data-target="#confirm-remove" data-toggle="modal">&nbsp;</a>
</div>