<div class="page-top clearfix">
    <div class="scr">
        <h2>Learn to be a great studier</h2>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/situation_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Situation"/>
            <?php endforeach; ?></div>
        <div class="grid_6">
            <h3><span>Why aren't we taught how to study?</span></h3>
            <p>An incredible amount of time and effort is devoted to learning in the classroom.  However, up to 75% of our time in school is spent outside class.  Considering how much time we spend studying, it is stunning that we are never taught effective study methods to employ once we leave the classroom.</p>
        </div>
        <div class="swap clearfix">
            <div class="grid_6">
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/complication_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Complication"/>
                <?php endforeach; ?></div>
            <div class="grid_6">
                <h3><span>Your study habits are hurting you</span></h3>
                <p>To make things worse, many of the methods we use are either ineffective or oftentimes counterproductive.  For example, highlighting or underlining while studying offers no benefit and can even impede learning.</p>
            </div>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/resolution_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Resolution"/>
            <?php endforeach; ?></div>
        <div class="grid_6">
            <h3><span>We make becoming a great studier easy</span></h3>
            <p>We have studied the best scientific research and have incorporated the findings into our site.  Study Sauce automatically detects good and bad study behaviors and teaches you by simply logging in when you study.  You are already putting in the time, use it effectively!  Become a great studier and improve your retention, performance, and your grades.</p>
        </div>
        <p class="highlighted-link"><a class="more" href="<?php print $view['router']->generate('register'); ?>">Join for free</a></p>
    </div>
</div>