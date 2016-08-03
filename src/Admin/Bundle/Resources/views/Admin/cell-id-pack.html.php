<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */
$time = method_exists($pack, 'getModified') && !empty($pack->getModified()) ? $pack->getModified() : $pack->getCreated();
?>
<i class="centerized">
<?php if (empty($pack->getLogo())) { ?>
        <img width="300" height="100" src="<?php print ($view->escape($view['assets']->getUrl('bundles/studysauce/images/upload_image.png'))); ?>" class="default" alt="Upload"/>
    <?php
} else { ?><img height="50" src="<?php print ($pack->getLogo()); ?>" /><?php } ?>
</i>