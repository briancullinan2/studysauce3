<a href="<?php print ($view['router']->generate('packs_group', ['group' => $ss_group->getId()])); ?>" class="pack-icon">
    <?php print ($view->render('AdminBundle:Admin:cell-id-ss_group.html.php', ['ss_group' => $ss_group])); ?>
    <?php print ($view->render('AdminBundle:Admin:cell-title.html.php', ['entity' => $ss_group, 'fields' => ['name']])); ?>
</a>
