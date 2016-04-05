<header class="<?php print $table; ?>">
    <?php
    $templates = []; // template name => classes
    // TODO: build backwards so its right aligned when there are different field counts
    for ($i = 0; $i < count($tables[$table]); $i++) {
        $field = is_array(array_values($tables[$table])[$i]) ? array_keys($tables[$table])[$i] : array_values($tables[$table])[$i];
        // skip search only fields
        if(is_numeric($field)) {
            continue;
        }
        ?>
        <label class="<?php print $field; ?>">
            <?php print $view->render('AdminBundle:Admin:heading.html.php', ['groups' => $allGroups, 'field' => $field]); ?>
        </label>
        <?php
    } ?>
</header>