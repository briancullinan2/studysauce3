<?php
$user = $app->getUser();
?>
<div class="footer">
    <?php
    if($view['slots']->get('classes') == 'landing-home') { ?>
        <ul class="menu secondary-menu">
            <li><a href="#contact-support" data-toggle="modal">Contact us</a></li>
        </ul>
        <span><?php print 'Copyright ' . date('Y'); ?></span>
    <?php } else if($app->getRequest()->get('_format') == 'funnel') { ?>
        <ul class="menu secondary-menu">
            <li class="menu-334"><a href="<?php print $view['router']->generate('privacy'); ?>">Privacy policy</a></li>
        </ul>
        <span><?php print 'Copyright ' . date('Y'); ?></span>
    <?php } else { ?>
        <div style="display:inline-block;">
            <a target="_blank" href="https://www.facebook.com/pages/Study-Sauce/519825501425670?ref=stream">&nbsp;</a>
            <a href="https://plus.google.com/115129369224575413617/about">&nbsp;</a>
            <a href="https://twitter.com/StudySauce">&nbsp;</a>
        </div>
        <ul class="menu secondary-menu">
            <li class="first"><a href="<?php print $view['router']->generate('terms'); ?>">Terms of service</a></li>
            <li><a href="<?php print $view['router']->generate('privacy'); ?>">Privacy policy</a></li>
            <li><a href="<?php print $view['router']->generate('about'); ?>">About us</a></li>
            <li><a href="#contact-support" data-toggle="modal">Contact us</a></li>
            <?php /* if (!empty($user) && is_object($user) && !$user->hasRole('ROLE_GUEST') && !$user->hasRole('ROLE_DEMO')) { ?>
                <li><a href="<?php print $view['router']->generate('logout'); ?>">Logout</a></li>
            <?php }
            if ($view['security']->isGranted('ROLE_PREVIOUS_ADMIN')) { ?>
                <li><a href="<?php print $view['router']->generate('_welcome'); ?>?_switch_user=_exit">Exit</a></li>
            <?php } */ ?>
        </ul>
        <span><?php print 'Copyright ' . date('Y'); ?></span>

        <?php
        $request = $view['request'];
        if ($app->getRequest()->get('_route') == '/home') {
            ?>
            <script type="text/javascript">
                var fb_param = {};
                fb_param.pixel_id = '6008770260529'; // Leads
                fb_param.value = '0.00';
                fb_param.currency = 'USD';
                (function () {
                    var fpw = document.createElement('script');
                    fpw.async = true;
                    fpw.src = '//connect.facebook.net/en_US/fp.js';
                    var ref = document.getElementsByTagName('script')[0];
                    ref.parentNode.insertBefore(fpw, ref);
                })();
            </script>
            <noscript><img height="1" width="1" alt="" style="display:none"
                           src="https://www.facebook.com/offsite_event.php?id=6008770262329&amp;value=0&amp;currency=USD"/>
            </noscript>
        <?php
        }
    } ?>
</div>