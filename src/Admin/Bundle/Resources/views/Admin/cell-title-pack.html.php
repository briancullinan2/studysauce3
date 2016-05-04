<?php if (isset($searchRequest['pack-id']) && $pack->getId() == $searchRequest['pack-id']) { ?>
<label><span><?php print ('All users in this pack'); ?></span></label>
<?php } else { ?>
<label><a href="<?php print ($view['router']->generate('packs_edit', ['pack' => $pack->getId()])); ?>"><?php print ($view->escape($pack->getTitle())); ?></a></label>
<?php }