<?php
use StudySauce\Bundle\Entity\Coupon;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Coupon $coupon */
$time = method_exists($coupon, 'getModified') && !empty($coupon->getModified()) ? $coupon->getModified() : $coupon->getCreated();

if (empty($coupon->getLogo())) { ?>
        <img width="300" height="100" src="<?php print ($view->escape($view['assets']->getUrl('bundles/studysauce/images/upload_image.png'))); ?>" class="default centerized" alt="Upload"/>
    <?php
} else { ?><img height="50" src="<?php print ($coupon->getLogo()); ?>" class="centerized" /><?php } ?>
