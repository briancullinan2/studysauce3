<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */
$time = method_exists($ss_group, 'getModified') && !empty($ss_group->getModified()) ? $ss_group->getModified() : $ss_group->getCreated();
?>
<span>&nbsp;</span>
<a href="#upload-image" data-target="#upload-file" data-toggle="modal" class="pack-icon cloak">
    <?php print ($view->render('AdminBundle:Admin:cell-id-ss_group.html.php', ['ss_group' => $ss_group])); ?>
    <input name="upload" value="<?php print (!empty($ss_group->getLogo()) ? $ss_group->getLogo()->getUrl() : ''); ?>" type="hidden" />
    <br />
    <span class="reveal"> Image</span>
</a>