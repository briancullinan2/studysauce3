<div class="did-you-know">
    <div>
        <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/did_you_know_620x310.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
            <img width="165" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
        <?php endforeach; ?>
        <h2>Even if your student is away at school, you can still help.</h2>
        <div class="one"><h3><span>1</span>Upgrade your student's free account</h3></div>
        <div class="two"><h3><span>2</span>Spread the word</h3></div>
        <div class="one highlighted-link"><a class="more" href="<?php print $view['router']->generate('checkout'); ?>">Sponsor
                student</a></div>
        <div class="two"><a class="more" href="#student-invite" data-toggle="modal">Tell your student</a></div>
    </div>
</div>
<div class="page-top clearfix">
    <div class="scr">
        <h2>Give your student the advantage</h2>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/situation_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Situation"/>
            <?php endforeach; ?></div>
        <div class="grid_6">
            <h3><span>Students learn to become great studiers</span></h3>
            <p>An incredible amount of time and effort is devoted to learning in the classroom. However, up to 75% of time in school is spent outside class. Considering how much time is spent studying, it is stunning that students are never taught effective study methods to employ once they leave the classroom.</p>
        </div>
        <div class="swap clearfix">
            <div class="grid_6">
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/complication_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Situation"/>
                <?php endforeach; ?></div>
            <div class="grid_6">
                <h3><span>Bad study habits are hurting performance</span></h3>
                <p>To make things worse, many of the methods students typically use are either ineffective or oftentimes counterproductive. For example, highlighting or underlining while studying offers no benefit and can even impede learning.</p>
            </div>
        </div>
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/resolution_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Situation"/>
            <?php endforeach; ?></div>
        <div class="grid_6">
            <h3><span>We make becoming a great studier easy</span></h3>
            <p>We have studied the best scientific research and have incorporated the findings into our site. Study Sauce automatically detects good and bad study behaviors and teaches student by simply logging in during study sessions. Your student can become a great studier and improve retention, performance, and grades.</p>
        </div>
        <p class="highlighted-link"><a class="more" href="<?php print $view['router']->generate('register'); ?>">Sponsor student</a></p>
    </div>
</div>