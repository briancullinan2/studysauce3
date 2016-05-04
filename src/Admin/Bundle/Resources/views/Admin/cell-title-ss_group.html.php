<?php if(isset($searchRequest['parent-ss_group-id']) && $ss_group->getId() == $searchRequest['parent-ss_group-id']) { ?>
    <label><span><?php print ('All users not in subgroups below'); ?></span></label>
<?php } else { ?>
    <label><a href="<?php print ($view['router']->generate('groups_edit', ['group' => $ss_group->getId()])); ?>"><?php print ($view->escape($ss_group->getName())); ?></a></label>
<?php }