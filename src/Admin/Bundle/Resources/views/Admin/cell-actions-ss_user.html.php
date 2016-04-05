<?php
use StudySauce\Bundle\Entity\User;

/** @var User $ss_user */
?>

<div class="highlighted-link">
    <a href="#cancel-edit">Cancel</a>
    <button type="submit" class="more" value="#save-user">Save</button>
    <a title="Send email"
       href="<?php print $view['router']->generate('emails'); ?>#<?php print $ss_user->getEmail(); ?>"></a>
    <a title="Masquerade"
       href="<?php print $view['router']->generate('_welcome'); ?>?_switch_user=<?php print $ss_user->getEmail(); ?>"></a>
    <a title="Reset password" href="#confirm-password-reset"></a>
    <a title="Edit" href="#edit-user"></a>
    <a title="Remove user" href="#remove-user"></a>
    <a href="#remove-confirm-user" class="more">Remove</a>
</div>