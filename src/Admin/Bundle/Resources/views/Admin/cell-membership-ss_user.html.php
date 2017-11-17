<div>
<?php
foreach($ss_user->getRoles() as $r) {
    ?><label class="checkbox">
    <input type="checkbox" name="roles" value="<?php print($r); ?>" <?php
        print($ss_user->hasRole($r) ? 'checked="checked"' : '');
        ?> /><i></i><span><?php print(str_replace($r, 'ROLE_', '')); ?></span></label><?php
}
?>
</div>
<div>
    <?php
    foreach($ss_user->getGroups()->toArray() as $r) {
        ?><label class="checkbox">
        <input type="checkbox" name="groups" value="<?php print($r->getName()); ?>"
               checked="checked" /><i></i><span><?php print($r->getName()); ?></span></label><?php
    }
    ?>
</div>
