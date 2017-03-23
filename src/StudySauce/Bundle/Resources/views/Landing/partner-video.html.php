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
                <a class="more" href="<?php print $view['router']->generate('register'); ?>">Sign up to help!</a>
            </div>
        </div>
    </div>
</div>
<div class="price-wrapper">
    <h1>How to be a great accountability partner</h1>
    <div class="features-content">
        <div class="feature-block first-block">
            <h2>Support and Guide</h2>
            <div class="feature-image-container">
                <div class="feature-image"></div>
            </div>
            <ul>
                <li>Help establish goals</li>
                <li>Celebrate achievements</li>
                <li>Soothe disappointments</li>
            </ul>
        </div>
        <div class="middle-block-wrapper">
            <div class="feature-block second-block">
                <h2>Communicate</h2>
                <div class="feature-image-container">
                    <div class="feature-image"></div>
                </div>
                <ul>
                    <li>Check in frequently</li>
                    <li>Create a safe forum for discussion</li>
                    <li>Challenge your student</li>
                </ul>
                <a class="features-cta" href="<?php print $view['router']->generate('register'); ?>">Sign up to help - it's free</a>
            </div>
        </div>
        <div class="feature-block third-block">
            <h2>Sponsor</h2>
            <div class="feature-image-container">
                <div class="feature-image"></div>
            </div>
            <ul>
                <li>Provide extra incentives for achievements</li>
                <li>Buy a premium account for your student</li>
            </ul>
        </div>
    </div>
</div>
