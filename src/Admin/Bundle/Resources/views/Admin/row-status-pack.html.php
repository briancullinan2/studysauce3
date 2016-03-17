<?php
use StudySauce\Bundle\Entity\Pack;

/** @var Pack $pack */

?>
<div class="<?php print strtolower($pack->getStatus()); ?> <?php print ($pack->getProperty('schedule') > new \DateTime() ? 'pending' : ''); ?>">
    <label class="input status">
        <span>Pack status</span><br />
        <select name="status">
            <option value="">Set pack publish settings ></option>
            <option value="UNPUBLISHED" <?php print ($pack->getStatus() == 'UNPUBLISHED' ? 'selected="selected"' : ''); ?>>Unpublished</option>
            <option value="PUBLIC" <?php print ($pack->getStatus() == 'PUBLIC' ? 'selected="selected"' : ''); ?>>Public</option>
            <?php
            if ($pack->getProperty('schedule') > new \DateTime()) { ?>
                <option value="GROUP" <?php print ($pack->getStatus() == 'GROUP' ? 'selected="selected"' : ''); ?>>Pending (<?php print $pack->getProperty('schedule')->format('m/d/Y'); ?>)</option>
            <?php }
            else { ?>
                <option value="GROUP" <?php print ($pack->getStatus() == 'GROUP' ? 'selected="selected"' : ''); ?>>Published</option>
            <?php } ?>
            <option value="UNLISTED" <?php print ($pack->getStatus() == 'UNLISTED' ? 'selected="selected"' : ''); ?>>Unlisted</option>
            <option value="DELETED" <?php print ($pack->getStatus() == 'DELETED' ? 'selected="selected"' : ''); ?>>Deleted</option>
        </select>
    </label>
    <a href="#pack-publish" data-target="#pack-publish" data-toggle="modal"> </a>
</div>