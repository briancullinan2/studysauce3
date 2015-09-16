
<div style="margin: 0 auto; display:block; height: 40px; background-color:#555; color:#FF9900; padding: 5px 15px; width:100%; max-width:600px;"><a href="https://studysauce.com/" style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif;font-size: 32px; color:#FFFFFF; white-space: nowrap; text-decoration: none; display:inline-block;" title="Home"><img alt="" height="40" src="https://studysauce.com/bundles/studysauce/images/Study_Sauce_Logo.png" style="margin: 0 5px 0 5px; float: left;" width="40" /><strong style="color:#FF9900;">Study</strong> Sauce</a></div>

<div style="margin: 0 auto; padding:15px; background: url(https://studysauce.com/bundles/studysauce/images/noise_gray.png) #EEEEEE; width:100%; max-width:600px;">
    <p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; "><strong>Hello <?php print $user->getFirst(); ?>,</strong></p>

    <p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">
        Below is your reminder.<br /><br /><br />

        <strong>Subject:</strong><br />
        <span style="height:24px;width:24px;background-image:url(https://studysauce.com/bundles/studysauce/images/course_icon.png);display:inline-block;vertical-align: middle;">&nbsp;</span> <?php print $course->getName(); ?><br /><br />
        <?php
        if(empty($course)) {
            $course = $deadline->getCourse();
        } ?>
        <strong>Assignment:</strong><br />
        <?php print $deadline->getAssignment(); ?><br /><br />

        <strong>Days until due date:</strong><br />
        <?php print $deadline->getDueDate(); ?><br /><br />
    </p>

    <p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">To access your account <a href="https://studysauce.com/login" style="color:#FF9900;" target="_blank">click here.</a>
        <br />&nbsp;</p>

    <p style="font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; ">Keep studying!
        <br />The Study Sauce Team</p>

    <p style="text-align: center; font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 16px; color: #555555; "><a href="https://www.facebook.com/pages/Study-Sauce/519825501425670?ref=stream" style="background:url(https://studysauce.com/bundles/studysauce/images/social_sprites_v2.png) no-repeat 0 0 transparent; height: 45px; width: 45px; display: inline-block; color:transparent;">&nbsp;</a> <a href="https://plus.google.com/115129369224575413617/about" style="background:url(https://studysauce.com/bundles/studysauce/images/social_sprites_v2.png) no-repeat 0 -95px transparent; height: 45px; width: 45px; display: inline-block; color:transparent;"> &nbsp;</a> <a href="https://twitter.com/StudySauce" style="background:url(https://studysauce.com/bundles/studysauce/images/social_sprites_v2.png) no-repeat 0 -190px transparent; height: 45px; width: 45px; display: inline-block; color:transparent;"> &nbsp;</a></p>
</div>

<div style="text-align: center; margin: 0 auto; font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 9px; color: #555555; width:100%; max-width:600px;">Copyright 2015. &nbsp;<a href="https://studysauce.com/privacy" style="text-decoration: underline; color: #555555; font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 9px;" target="_blank">Privacy Policy</a>&nbsp;|&nbsp;<a href="%unsubscribe%" style="text-decoration: underline; color: #555555; font-family: 'Ubuntu',Helvetica Neue,Arial,sans-serif; font-size: 9px;" target="_blank">Unsubscribe</a></div>
