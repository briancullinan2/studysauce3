<?php
$email_base = $view['router']->generate('_welcome', [], true) . 'bundles/studysauce';
?>
<html>
<body style="padding:0; margin:0; background: url(<?php print $email_base; ?>/images/noise_white.png) #FFFFFF;">
<div style="margin: 0 auto; padding: 0 5px; box-sizing:border-box; max-width:600px; display:block;">
<div style="margin: 0; display:block; height: 40px; background-color:#555; color:#FF9900; padding: 5px;">
    <a title="Home" href="<?php print $view['router']->generate('_welcome', [], true); ?>" style="line-height: 40px; vertical-align: middle; font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif;font-size: 32px; color:#FFFFFF; white-space: nowrap; text-decoration: none; display:inline-block;">
        <img width="40" height="40" alt="" style="margin: 0 5px 0 5px; float: left; line-height:40px; vertical-align: middle;" src="<?php print $email_base; ?>/images/Study_Sauce_Logo.png"><strong style="color:#FF9900;">Study</strong> Sauce</a>
</div>
<div style="margin: 0; padding:5px; background: url(<?php print $email_base; ?>/images/noise_gray.png) #EEEEEE; display:block;">
    <?php if(!empty($greeting)): ?>
    <p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
        <strong><?php print $greeting; ?></strong>
    </p>
    <?php elseif(!empty($view['slots']->get('greeting'))): ?>
        <p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
            <strong><?php $view['slots']->output('greeting'); ?></strong>
        </p>
    <?php endif; ?>

    <p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
        <?php $view['slots']->output('message') ?>
    </p>

    <?php if(!isset($link) || $link !== false): ?>
        <p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
            <?php if (isset($link)): ?>
                <?php print $link; ?>
            <?php else: ?>
                To access your account <a style="color:#FF9900;" href="<?php print $view['router']->generate('login', [], true); ?>" target="_blank">click here.</a>
            <?php endif; ?>
            <br/><br/>
        </p>
    <?php endif; ?>

    <p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
        <?php if(empty($view['slots']->get('salutation'))) { ?>
        Keep studying!<br/>
        The Study Sauce Team
        <?php } else {
            $view['slots']->output('salutation');
        }?>
    </p>
    <p style="text-align: center; font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
        <a href="https://www.facebook.com/pages/Study-Sauce/519825501425670?ref=stream"
           style="background:url(<?php print $email_base; ?>/images/social_sprites_v2.png) no-repeat 0 0 transparent; height: 45px; width: 45px; display: inline-block; color:transparent;">
            &nbsp;</a>
        <a href="https://plus.google.com/115129369224575413617/about"
           style="background:url(<?php print $email_base; ?>/images/social_sprites_v2.png) no-repeat 0 -95px transparent; height: 45px; width: 45px; display: inline-block; color:transparent;">
            &nbsp;</a>
        <a href="https://twitter.com/StudySauce"
           style="background:url(<?php print $email_base; ?>/images/social_sprites_v2.png) no-repeat 0 -190px transparent; height: 45px; width: 45px; display: inline-block; color:transparent;">
            &nbsp;</a>
    </p>
    <?php if (isset($footer)): ?>
        <p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
            <?php print $footer; ?>
        </p>
    <?php endif; ?>
</div>
<div
    style="text-align: center; margin: 0 auto; font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 9px; color: #555555; width:100%; max-width:600px;">
    Copyright <?php print date('Y'); ?>. &nbsp;<a target="_blank"
                                                  href="<?php print $view['router']->generate('privacy', [], true); ?>"
                                                  style="text-decoration: underline; color: #555555; font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 9px;">Privacy
        Policy</a>&nbsp;|&nbsp;<a target="_blank" href="%unsubscribe%"
                                  style="text-decoration: underline; color: #555555; font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 9px;">Unsubscribe</a>
</div>
</div>
</body>
</html>
<?php
$view['slots']->start('greeting');
$view['slots']->stop();
$view['slots']->start('message');
$view['slots']->stop();
$view['slots']->start('salutation');
$view['slots']->stop();