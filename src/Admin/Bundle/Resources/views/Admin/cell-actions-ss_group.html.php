

<div class="highlighted-link">
    <?php
    if(isset($searchRequest['pack-id']) && !empty($packId = $searchRequest['pack-id'])) { ?>
        <a href="#general-dialog" data-confirm="Are you sure you would like to remove the group &ldquo;<?php print $ss_group->getName(); ?>&rdquo; from the pack?" class="remove-icon" data-action="<?php print $view['router']->generate('save_group', ['ss_group' => ['id' => $ss_group->getId(), 'packs' => ['id' => $packId, 'remove' => 'true']]]); ?>" data-target="#general-dialog" data-toggle="modal">&nbsp;</a>
    <?php }
    else { ?>
        <a href="#general-dialog" data-confirm="Are you sure you would like to delete the group &ldquo;<?php print $ss_group->getName(); ?>&rdquo; permanently?" class="remove-icon" data-action="<?php print $view['router']->generate('save_group', ['ss_group' => ['id' => $ss_group->getId(), 'remove' => 'true']]); ?>" data-target="#general-dialog" data-toggle="modal">&nbsp;</a>
    <?php } ?>
</div>
