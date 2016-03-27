<?php
use StudySauce\Bundle\Entity\Pack;

/** @var Pack $pack */

?>
<div class="<?php print strtolower($pack->getStatus()); ?> <?php print ($pack->getProperty('schedule') > new \DateTime() ? 'pending' : ''); ?>">
    <label class="input status">
        <span>Pack status</span><br />
        <select name="status" data-publish="<?php print (!empty($pack->getProperty('schedule')) ? $view->escape(json_encode(['schedule' => $pack->getProperty('schedule')->format('r'), 'email' => $pack->getProperty('email'), 'alert' => $pack->getProperty('alert')])) : '' ); ?>">
            <option value="" <?php print (empty($pack->getId()) ? 'selected="selected"' : ''); ?>>Set pack publish settings ></option>
            <option value="UNPUBLISHED" <?php print (!empty($pack->getId()) && $pack->getStatus() == 'UNPUBLISHED' ? 'selected="selected"' : ''); ?>>Unpublished</option>
            <option value="PUBLIC" <?php print (!empty($pack->getId()) && $pack->getStatus() == 'PUBLIC' ? 'selected="selected"' : ''); ?>>Public</option>
            <?php
            if ($pack->getProperty('schedule') > new \DateTime()) { ?>
                <option value="GROUP" <?php print (!empty($pack->getId()) && $pack->getStatus() == 'GROUP' ? 'selected="selected"' : ''); ?>>Pending (<?php print $pack->getProperty('schedule')->format('m/d/Y'); ?>)</option>
            <?php }
            else { ?>
                <option value="GROUP" <?php print (!empty($pack->getId()) && $pack->getStatus() == 'GROUP' ? 'selected="selected"' : ''); ?>>Published</option>
            <?php } ?>
            <option value="UNLISTED" <?php print (!empty($pack->getId()) && $pack->getStatus() == 'UNLISTED' ? 'selected="selected"' : ''); ?>>Unlisted</option>
            <option value="DELETED" <?php print (!empty($pack->getId()) && $pack->getStatus() == 'DELETED' ? 'selected="selected"' : ''); ?>>Deleted</option>
        </select>
    </label>
    <a href="#pack-publish" data-target="#pack-publish" data-toggle="modal"> </a>
</div>