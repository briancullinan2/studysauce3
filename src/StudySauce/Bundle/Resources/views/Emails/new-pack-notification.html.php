<?php
use StudySauce\Bundle\Entity\User;

$view->extend('StudySauceBundle:Emails:layout.html.php');

/** @var User $user */

$view['slots']->start('message'); ?>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
    We just wanted to let you know that <?php print (!empty($group) ? ($group . ' has') : 'we have'); ?> just added a new study pack to <?php print (!empty($child) ? ($child . (substr($child, -1) == 's' ? '\'' : '\'s')) : 'your'); ?> Study Sauce account.<br />
</p>
<?php if (!empty($groupLogo)) { ?>
    <p style="text-align:right; font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
        <img width="150" height="150" src="<?php echo $groupLogo; ?>" alt="LOGO" style="float:left;vertical-align: middle;" />
        <br />
        <br />
        <br />
        <?php print $packName; ?>
        <br />
        <?php print $packCount; ?>
        <br />
        <br />
        <br />
    </p>
<?php } ?>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
We hope you enjoy the new study material!
</p>
<?php $view['slots']->stop(); ?>

