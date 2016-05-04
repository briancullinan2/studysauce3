<a href="<?php print ($view['router']->generate('groups_edit', ['group' => $ss_group->getId()])); ?>" class="pack-icon">
    <?php print ($view->render('AdminBundle:Admin:cell-id-ss_group.html.php', ['ss_group' => $ss_group])); ?>
</a>
<?php print ($view->render('AdminBundle:Admin:cell-title-ss_group.html.php', ['ss_group' => $ss_group])); ?>
