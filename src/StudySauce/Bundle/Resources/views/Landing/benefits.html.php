<div class="page-top clearfix">
    <div class="scr">
        <div class="swap clearfix">
            <div class="grid_6">
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/iphone.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Situation"/>
                <?php endforeach; ?></div>
            <div class="grid_6">
                <h3><span>Learn</span></h3>
                <ul>
                    <li>Has anyone ever actually taught you how to study? Odds are good you have fallen into some bad habits.</li>
                    <li>Our video-based course will teach you the most effective study methods, so you can get on with your life.</li>
                    <li>Study Sauce uses memory retention science, so you can stop cramming for exams.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="page-top clearfix section-2">
    <div class="scr">
        <div class="grid_6">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/ipad_small.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Complication"/>
            <?php endforeach; ?></div>
        <div class="grid_6">
            <h3><span>Organize</span></h3>
            <ul>
                <li>Once you have learned how to study, our study tools keep you organized and on track.</li>
                <li>Learn to take better notes and keep them all in one place with our study notes that are integrated with Evernote.</li>
                <li>Get organized with a custom study plan tailored to your schedule.</li>
            </ul>
        </div>
    </div>
</div>
<div class="page-top clearfix section-3">
    <div class="scr">
        <div class="swap clearfix">
            <div class="grid_6">
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/iair.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img width="200" height="200" src="<?php echo $view->escape($url) ?>" alt="Resolution"/>
                <?php endforeach; ?></div>
            <div class="grid_6">
                <h3><span>Track</span></h3>
                <ul>
                    <li>Personalized deadline reminders will make sure nothing sneaks up on you.</li>
                    <li>See your hard work in custom charts that track your study progress over time.</li>
                    <li>Take the guesswork out of calculating your class grades. Know what grades you need to reach your goals.</li>
                </ul>
            </div>
        </div>
    </div>
</div>