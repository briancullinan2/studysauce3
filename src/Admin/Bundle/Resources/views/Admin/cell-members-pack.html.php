<?php
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Pack $pack */

$entityIds = [];
?>
<label><?php print $pack->getUsers()->count(); ?> users</label>
<?php print $this->render('AdminBundle:Admin:cell-collection.html.php', ['tables' => ['ss_user' => ['first', 'last', 'email', 'id', 'deleted']], 'entities' => $pack->getUsers()->toArray()]); ?>

<a href="#add-entity" class="big-add" data-toggle="modal" data-target="#add-entity">Add
    <span>+</span> individual</a>
