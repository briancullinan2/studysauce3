<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */
?>
<span>&nbsp;</span>
<a href="#upload-image" data-target="#upload-file" data-toggle="modal" class="pack-icon cloak">
    <?php print ($view->render('AdminBundle:Admin:cell-id-pack.html.php', ['pack' => $pack])); ?>
    <input name="upload" value="<?php print (!empty($pack->getLogo()) ? $pack->getLogo() : ''); ?>" type="hidden" />
    <br />
    <span class="reveal"> Image</span>
</a>
