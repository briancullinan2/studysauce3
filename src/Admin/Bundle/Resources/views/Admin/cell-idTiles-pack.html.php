<a href="<?php print ($view['router']->generate('packs_edit', ['pack' => $pack->getId()])); ?>" class="pack-icon">
    <?php print ($view->render('AdminBundle:Admin:cell-id-pack.html.php', ['pack' => $pack])); ?>
</a>
<?php print ($view->render('AdminBundle:Admin:cell-title-pack.html.php', ['pack' => $pack])); ?>
