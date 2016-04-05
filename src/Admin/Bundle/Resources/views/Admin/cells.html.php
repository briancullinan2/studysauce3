<?php
foreach ($tables[$table] as $f => $fields) {
    $field = is_array($fields) ? $f : $fields;
    // skip search only fields
    if(is_numeric($field)) {
        continue;
    }
    ?>
<div class="<?php print $field; ?>">
    <?php
    if ($view->exists('AdminBundle:Admin:cell-' . $field . '-' . $table . '.html.php')) {
        print $view->render('AdminBundle:Admin:cell-' . $field . '-' . $table . '.html.php', [$table => $entity, 'groups' => $allGroups, 'table' => $table]);
    } else if ($view->exists('AdminBundle:Admin:cell-' . $field . '.html.php')) {
        print $view->render('AdminBundle:Admin:cell-' . $field . '.html.php', ['entity' => $entity, 'groups' => $allGroups, 'table' => $table]);
    } else {
        print $view->render('AdminBundle:Admin:cell-generic.html.php', ['tables' => $tables, 'fields' => is_array($fields) ? $fields : [$fields], 'field' => $field, 'entity' => $entity, 'groups' => $allGroups, 'table' => $table]);
    }
    ?></div>

    <?php
}
