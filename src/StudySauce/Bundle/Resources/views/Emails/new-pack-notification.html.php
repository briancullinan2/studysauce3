<?php
use StudySauce\Bundle\Entity\User;

$view->extend('StudySauceBundle:Emails:layout.html.php');

/** @var User $user */

$view['slots']->start('message'); ?>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
    We just wanted to let you know that <?php print (!empty($group) ? ($group . ' has') : 'we have'); ?> just added a new study pack to <?php print (!empty($child) ? ($child . (substr($child, -1) == 's' ? '\'' : '\'s')) : 'your'); ?> Study Sauce account.<br />
</p>
<?php if (!empty($groupLogo)) { ?>
    <br />
    <p style="text-align:center; font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; padding:20px; margin: 0 auto; max-width:400px; border-top:1px solid #BBB; border-bottom:1px solid #BBB;">
        <br />
        <br />
        <br />
        <span style="text-align: left; display:inline-block;">
            <strong><?php print $packName; ?></strong><br />(<?php print $packCount; ?> cards)
        </span>
        <br />
        <br />
        <img width="150" height="150" src="<?php echo $groupLogo; ?>" alt="LOGO" />
        <br />
        <br />
        <br />
    </p>
    <br />
<?php } ?>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555;">
We hope you enjoy the new study material!
</p>
<?php $view['slots']->stop(); ?>

