<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;

/** @var Group $ss_group */

$entityIds = [];
$users = $ss_group->getUsers()->toArray();
$ids = array_map(function (User $u) {return 'ss_user-' . $u->getId();}, $users);

?>
<form action="<?php print (isset($searchRequest['pack-id']) && !empty($group = $searchRequest['pack-id'])
    ? $view['router']->generate('save_group', ['packId' => $searchRequest['pack-id'], 'groupId' => $ss_group->getId()])
    : $view['router']->generate('save_group', ['groupId' => $ss_group->getId()])); ?>">
    <label><?php print count($users); ?> users</label>
    <?php print $this->render('AdminBundle:Admin:cell-collection.html.php', [
        'tables' => ['ss_user' => ['first', 'last', 'email', 'id', 'deleted']],
        'entities' => $users,
        'entityIds' => $ids]); ?>
    <a href="#add-entity" class="big-add" data-toggle="modal" data-target="#add-entity">Add
        <span>+</span> individual</a>
</form>
