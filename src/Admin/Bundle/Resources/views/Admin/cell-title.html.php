<?php
foreach($fields as $f => $field) {
    if(method_exists($entity, $method = implode('', ['get' , ucfirst($field)]))) {
        $fields[$f] = $entity->$method();
    }
}
print ($view->render('AdminBundle:Admin:cell-label.html.php', ['fields' => $fields]));
