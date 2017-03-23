<div class="video clearfix">
    <video autoplay="true" loop="true" id="bgvid" height="240" width="320">
        <source src="https://s3-us-west-2.amazonaws.com/studysauce/Study_Difficulties.webm" type="video/webm">
        <source src="https://s3-us-west-2.amazonaws.com/studysauce/Study_Difficulties_compressed.mp4" type="video/mp4">
        <source src="https://s3-us-west-2.amazonaws.com/studysauce/Study_Difficulties.flv" type="video/flv">
    </video>
    <div id="site-name" class="navbar-header">
        <a href="<?php print $view['router']->generate('login'); ?>">Sign in</a>
    </div>
    <div class="flexslider">
        <div class="player-divider">
            <h1>STOP THE STRESS!</h1>
            <div class="player-wrapper">
                <iframe id="landing-intro-player" src="https://www.youtube.com/embed/xY-LuIsFpio?rel=0&amp;controls=0&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1&amp;origin=<?php print $view->escape($app->getRequest()->getScheme() . '://' . $app->getRequest()->getHttpHost()); ?>"></iframe>
                <a href="#yt-pause">&times;</a>
            </div>
            <div class="highlighted-link">
                <a class="more" href="<?php print $view['router']->generate('checkout'); ?>">Sign up</a>
            </div>
        </div>
    </div>
</div>
<div class="price-wrapper">
    <div class="price-section highlighted-link">
        <div class="student">
            <h2><strong>Study</strong> Sauce</h2>
            <h3>Single Student</h3>
            <h4>$10 / month</h4>
            <a class="more" href="<?php print $view['router']->generate('checkout'); ?>">Buy now</a>
        </div>
        <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/Study_Sauce_Logo_Sketch.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <div class="business">
            <h2><strong>Study</strong> Sauce</h2>
            <h3>Organization</h3>
            <h4>Contact for pricing</h4>
            <a href="#schedule-demo" class="more" data-toggle="modal">Contact us</a>
        </div>
    </div>
</div>
