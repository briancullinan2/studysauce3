<div class="video clearfix">
    <video autoplay="" loop="" id="bgvid">
        <source src="https://s3-us-west-2.amazonaws.com/studysauce/Study_Difficulties.webm" type="video/webm">
        <source src="https://s3-us-west-2.amazonaws.com/studysauce/Study_Difficulties_compressed.mp4" type="video/mp4">
    </video>
    <div id="site-name" class="navbar-header">
        <a href="<?php print $view['router']->generate('login'); ?>">Sign in</a>
    </div>
    <div class="flexslider">
        <div class="player-divider">
            <h1>Help your student succeed</h1>
            <div class="player-wrapper">
                <iframe id="landing-intro-player" src="https://www.youtube.com/embed/xY-LuIsFpio?rel=0&amp;controls=0&amp;modestbranding=1&amp;showinfo=0&amp;enablejsapi=1&amp;origin=<?php print $app->getRequest()->getScheme() . '://' . $app->getRequest()->getHttpHost(); ?>"></iframe>
                <a href="#yt-pause">&times;</a>
            </div>
            <div class="highlighted-link">
                <a class="more" href="<?php print $view['router']->generate('checkout'); ?>">Sponsor student</a>
            </div>
        </div>
    </div>
</div>