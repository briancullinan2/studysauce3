<?php
use StudySauce\Bundle\Entity\User;

/** @var User $ss_user */
?>

<div class="user-name">
    <label class="input first"><input type="text" name="first"
                                value="<?php print $ss_user->getFirst(); ?>"
                                placeholder="First name"/></label>
    <label class="input last"><input type="text" name="last"
                                value="<?php print $ss_user->getLast(); ?>"
                                placeholder="Last name"/></label>
    <label class="input email"><input type="text" name="email"
                                value="<?php print $ss_user->getEmail(); ?>"
                                placeholder="Email"/></label>
</div>
