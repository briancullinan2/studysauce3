

<div class="highlighted-link">
    <?php
    if(isset($request['pack-id']) && !empty($packId = $request['pack-id'])) { ?>
        <a href="#general-dialog" data-confirm="Are you sure you would like to remove the group &ldquo;<?php print ($ss_group->getName()); ?>&rdquo; from the pack?" class="remove-icon" data-action="<?php print ($view['router']->generate('save_group', ['ss_group' => ['id' => $ss_group->getId(), 'groupPacks' => ['id' => $packId, 'remove' => 'true']]])); ?>" data-target="#general-dialog" data-toggle="modal">&nbsp;</a>
    <?php }
    else {
        $removeParams = ['id' => $ss_group->getId(), 'deleted' => '1'];

        ?>
        <a href="#general-dialog" data-confirm="Are you sure you would like to delete the group &ldquo;<?php print ($ss_group->getName()); ?>&rdquo; permanently?" class="remove-icon" data-action="<?php print ($view['router']->generate('save_group', ['ss_group' => $removeParams])); ?>" data-target="#general-dialog" data-toggle="modal">&nbsp;</a>
    <?php } ?>
</div>
