<?php
if(isset($request['cells'][$tableId])) {
    $cells = $request['cells'][$tableId];
}
else {
    $cells = array_keys($tables[$table]);
}
foreach ($cells as $i => $f) {
    $fields = $tables[$table][$f];
    $field = is_array($fields) ? $f : $fields;
    // skip search only fields
    if(is_numeric($field)) {
        continue;
    }
    ?>
<div class="<?php print ($field); ?>">
    <?php
    if ($view->exists(implode('', ['AdminBundle:Admin:cell-' , $field , '-' , $table , '.html.php']))) {
        $specificCell = [
            'table' => $table,
            'request' => $request,
            'results' => $results];
        $specificCell[$table] = $entity;
        print ($view->render(implode('', ['AdminBundle:Admin:cell-' , $field , '-' , $table , '.html.php']), $specificCell));
    } else if ($view->exists(implode('', ['AdminBundle:Admin:cell-' , $field , '.html.php']))) {
        print ($view->render(implode('', ['AdminBundle:Admin:cell-' , $field , '.html.php']), [
            'entity' => $entity,
            'table' => $table,
            'request' => $request,
            'results' => $results]));
    } else {
        print ($view->render('AdminBundle:Admin:cell-generic.html.php', [
            'tables' => $tables,
            'fields' => is_array($fields) ? $fields : [$fields],
            'field' => $field,
            'entity' => $entity,
            'table' => $table,
            'request' => $request,
            'results' => $results]));
    }
    ?></div>

    <?php
}
