<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */
$time = method_exists($ss_group, 'getModified') && !empty($ss_group->getModified()) ? $ss_group->getModified() : $ss_group->getCreated();

if (empty($ss_group->getLogo())) { ?>
        <img width="300" height="100" src="<?php print ($view->escape($view['assets']->getUrl('bundles/studysauce/images/upload_image.png'))); ?>" class="default centerized" alt="Upload"/>
    <?php
} else { ?><img height="50" src="<?php print ($ss_group->getLogo()->getUrl()); ?>" class="centerized" /><?php } ?>
