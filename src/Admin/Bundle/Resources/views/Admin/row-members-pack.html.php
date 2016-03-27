<?php
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */

$entityIds = [];
print $this->render('AdminBundle:Admin:row-collection.html.php', ['tables' => ['ss_user' => ['first', 'last', 'email', 'id']], 'entities' => $pack->getUsers()->toArray()]);
?>

<a href="#add-entity" class="big-add" data-toggle="modal" data-target="#add-entity">Add
    <span>+</span> individual</a>
