<?php
use StudySauce\Bundle\Entity\Pack;
use \DateTime as Date;

/** @var Pack $pack */

?>

<div class="<?php
    print (strtolower($pack->getStatus()));
    print ($pack->getProperty('schedule') > new Date() ? ' pending' : ''); ?>">
    <label class="input status">
        <select name="status" data-publish="<?php print (!empty($pack->getProperty('schedule')) ? $view->escape(json_encode(['schedule' => $pack->getProperty('schedule')->format('r'), 'email' => $pack->getProperty('email'), 'alert' => $pack->getProperty('alert')])) : '' ); ?>">
            <option value="UNPUBLISHED" <?php print (empty($pack->getId()) || empty($pack->getStatus()) || $pack->getStatus() == 'UNPUBLISHED' ? 'selected="selected"' : ''); ?>>Unpublished</option>
            <?php
            if ($pack->getProperty('schedule') > new Date()) { ?>
                <option value="GROUP" <?php print (!empty($pack->getId()) && $pack->getStatus() == 'GROUP' ? 'selected="selected"' : ''); ?>>Pending (<?php print ($pack->getProperty('schedule')->format('m/d/Y')); ?>)</option>
            <?php }
            else if (!empty($pack->getProperty('schedule'))) { ?>
                <option value="GROUP" <?php print (!empty($pack->getId()) && $pack->getStatus() == 'GROUP' ? 'selected="selected"' : ''); ?>>Published</option>
            <?php }
            else { ?>
                <option value="GROUP" <?php print (!empty($pack->getId()) && $pack->getStatus() == 'GROUP' ? 'selected="selected"' : ''); ?>>Publish</option>
            <?php }

            if ($app->getUser()->hasRole('ROLE_ADMIN') && $app->getUser()->getEmail() == 'brian@studysauce.com') {
                ?>
                <option
                    value="PUBLIC" <?php print (!empty($pack->getId()) && $pack->getStatus() == 'PUBLIC' ? 'selected="selected"' : ''); ?>>
                    Public
                </option>
                <option
                    value="UNLISTED" <?php print (!empty($pack->getId()) && $pack->getStatus() == 'UNLISTED' ? 'selected="selected"' : ''); ?>>
                    Unlisted
                </option>
                <option
                    value="DELETED" <?php print (!empty($pack->getId()) && $pack->getStatus() == 'DELETED' ? 'selected="selected"' : ''); ?>>
                    Deleted
                </option>
                <?php
            }
            ?>
        </select>
    </label>
</div>
