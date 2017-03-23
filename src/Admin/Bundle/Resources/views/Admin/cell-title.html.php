<?php
$labels = [];
foreach($fields as $f => $field) {
    if(method_exists($entity, $method = implode('', ['get' , ucfirst($field)]))) {
        $labels[count($labels)] = call_user_func_array([$entity, $method], []);
    }
}
print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => $labels]));
