<?php
use StudySauce\Bundle\Entity\User;

/** @var User $ss_user */
?>

<div class="highlighted-link">
    <a title="Send email"
       href="<?php print $view['router']->generate('emails'); ?>#<?php print $ss_user->getEmail(); ?>"></a>
    <a title="Masquerade"
       href="<?php print $view['router']->generate('_welcome'); ?>?_switch_user=<?php print $ss_user->getEmail(); ?>"></a>
    <a title="Reset password" href="#confirm-password-reset"
       data-toggle="modal"></a>
    <a title="Cancel payment" href="#confirm-cancel-user" data-toggle="modal"></a>
    <a title="Edit" href="#edit-user"></a>
    <a title="Remove user" href="#confirm-remove-user" data-toggle="modal"></a>
    <a href="#cancel-edit">Cancel</a>
    <button type="submit" class="more" value="#save-user">Save</button>
</div>