<?php
use StudySauce\Bundle\Entity\Pack;

/** @var \Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables $app */
$user = $app->getUser();

/** @var Pack $pack */

?>
<div class="highlighted-link">
    <?php
    if($user->hasRole('ROLE_ADMIN')) { ?>
        <a title="Edit pack" href="<?php print ($view['router']->generate('packs_edit', ['pack' => $pack->getId()])); ?>" class="edit-icon">&nbsp;</a>
    <?php } ?>
</div>
