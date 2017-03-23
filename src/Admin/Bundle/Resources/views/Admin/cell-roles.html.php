<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;

/** @var User|Group $entity */
?>

<div>
    <label class="checkbox"><input type="checkbox" name="roles"
                                   value="ROLE_PAID" <?php print ($entity->hasRole(
            'ROLE_PAID'
        ) ? 'checked="checked"' : ''); ?> /><i></i><span>PAID</span></label>
    <label class="checkbox"><input type="checkbox" name="roles"
                                   value="ROLE_ADMIN" <?php print ($entity->hasRole(
            'ROLE_ADMIN'
        ) ? 'checked="checked"' : ''); ?> /><i></i><span>ADMIN</span></label>
    <label class="checkbox"><input type="checkbox" name="roles"
                                   value="ROLE_PARENT" <?php print ($entity->hasRole(
            'ROLE_PARENT'
        ) ? 'checked="checked"' : ''); ?> /><i></i><span>PARENT</span></label>
    <label class="checkbox"><input type="checkbox" name="roles"
                                   value="ROLE_PARTNER" <?php print ($entity->hasRole(
            'ROLE_PARTNER'
        ) ? 'checked="checked"' : ''); ?> /><i></i><span>PARTNER</span></label>
    <label class="checkbox"><input type="checkbox" name="roles"
                                   value="ROLE_ADVISER" <?php print ($entity->hasRole(
            'ROLE_ADVISER'
        ) ? 'checked="checked"' : ''); ?> /><i></i><span>ADVISER</span></label>
    <label class="checkbox"><input type="checkbox" name="roles"
                                   value="ROLE_MASTER_ADVISER" <?php print ($entity->hasRole(
            'ROLE_MASTER_ADVISER'
        ) ? 'checked="checked"' : ''); ?> /><i></i><span>MASTER_ADVISER</span></label>
    <label class="checkbox"><input type="checkbox" name="roles"
                                   value="ROLE_DEMO" <?php print ($entity->hasRole(
            'ROLE_DEMO'
        ) ? 'checked="checked"' : ''); ?> /><i></i><span>DEMO</span></label>
    <label class="checkbox"><input type="checkbox" name="roles"
                                   value="ROLE_GUEST" <?php print ($entity->hasRole(
            'ROLE_GUEST'
        ) ? 'checked="checked"' : ''); ?> /><i></i><span>GUEST</span></label>
</div>