<div class="highlighted-link">
    <a title="Remove pack" href="#general-dialog" data-confirm="Are you sure you would like to delete the pack &ldquo;<?php print $pack->getTitle(); ?>&rdquo; permanently?" class="remove-icon" data-action="<?php print $view['router']->generate('packs_remove', ['pack' => $pack->getId()]); ?>" data-target="#general-dialog" data-toggle="modal">&nbsp;</a>
</div>