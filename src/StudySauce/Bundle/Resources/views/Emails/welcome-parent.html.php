<?php
$view->extend('StudySauceBundle:Emails:layout.html.php');
$email_base = $view['router']->generate('_welcome', [], true) . 'bundles/studysauce';

$view['slots']->start('message'); ?>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
Thank you for signing <?php print (!empty($child) ? $child : ''); ?> up for Study Sauce.<br />
<br />
</p>

<?php if (!empty($group)) { ?>
<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
    We are working closely with <?php print (!empty($group) ? $group : ''); ?> to help <?php print (!empty($child) ? $child : 'your child'); ?> remember more of what is being learned.  <?php print (!empty($group) ? $group : ''); ?> will be adding new study material over time as <?php print (!empty($child) ? $child : 'your child'); ?> covers more material.<br />
    <br />
</p>
<?php } ?>

<?php if (!empty($groupLogo)) { ?>
    <p style="text-align:center; font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
        <img width="150" height="150" src="<?php echo $groupLogo; ?>" alt="LOGO" style="vertical-align: middle;" />
        <br />
    </p>
<?php } ?>

<p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
Without periodically looking over what you learn, you forget almost everything within a few days.  Study Sauce calculates exactly what needs to be reviewed each day in order to remember more of the material.  You are already investing the time, why not spend a few minutes to maintain what you learn?  You will be amazed by how much more you remember.<br />
</p>


<?php $view['slots']->stop(); ?>
