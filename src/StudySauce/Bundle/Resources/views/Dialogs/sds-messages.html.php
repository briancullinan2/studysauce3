<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

 $view['slots']->start('modal-header') ?>
<span class="full-only">Your study tip for this session</span><span class="mobile-only">Study tip</span>
<?php $view['slots']->stop();

 $view['slots']->start('modal-body') ?>
<?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/Study_Sauce_Logo_Gray.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
    <img width="48" height="48" src="<?php echo $view->escape($url) ?>" alt="LOGO" />
<?php endforeach; ?>
<div class="multiple-locations <?php print ($count == 0 ? 'show' : 'hide'); ?>">
    <h3>Use different study locations</h3>
    <p>Changing location has shown a significant improvement in memory retention in studies done by cognitive scientists.  According to the researchers, the brain associates the material being learned with the environment that the studier is in.  Varied environments allow the brain more opportunities to associate the material with something unique - which in turn increases the likelihood of retention.</p>
</div>
<div class="switching-topics <?php print ($count == 1 ? 'show' : 'hide'); ?>">
    <h3>Alternate your study topics</h3>
    <p>Think of this as athletics for your brain.  Elite athletes have been cross-training in multiple disciplines for years because of the proven benefits of varied activity.  The brain is also a muscle and studies have shown that retention of study material improves dramatically when different parts of the brain are activated.   When the brain must use different approaches to solving problems, it is better able to solve varied problems that a student is likely to encounter on a test.  Results of scientific studies have shown double the performance of test takers focusing on a single subject and has been corroborated using both adults and children.  This is most effective if you study very different types of courses as opposed to similar ones.  For example, alternating Spanish with Calculus will be more effective than alternating Economics with Statistics.</p>
</div>
<div class="taking-breaks <?php print ($count == 2 ? 'show' : 'hide'); ?>">
    <h3>Taking breaks are key</h3>
    <p>This will ultimately be a personal preference.  A good rule of thumb is to study hard for 50 minutes, then take a 10 minute break.  We use a slight variation of 60 minute session followed by a 10-15 minute break for our software.  You can then repeat the process for days that you need to study longer.  Taking too short of a break will not leave you refreshed.  Taking too long of a break can be a waste of precious time.  Experiment to find what works best for you.</p>
</div>
<div class="no-cramming <?php print ($count == 3 ? 'show' : 'hide'); ?>">
    <h3>Say no to cramming</h3>
    <p>The tragedy of cramming is that it works...sort of.  Cramming does tend to produce short term results, however, the brain has a tendency to immediately “dump” the information shortly after the test.  You will end up having to restudy the material to commit it to long term memory…which can be problematic for your Final Exams…  Not to mention the fact that the material you are studying will most likely need to be used again in a more advanced course.</p>
</div>
<?php /*<div class="switching-location <?php print ($count == -4 ? 'show' : 'hide'); ?>">
    <h3>Switching your study location</h3>
    <p>Well done!  We see that you have changed your study locations.  You are helping your brain retain the information that you are studying.  Keep it up!</p>
</div> */ ?>
<div class="repeat-location <?php print ($count == 4 ? 'show' : 'hide'); ?>">
    <h3>Studying from the same location</h3>
    <p>Here is a study tip for you.  We see that you have been studying from the same location when you check in.  We highly recommend you alternate your study locations.  Changing location has shown a significant improvement in memory retention in studies done by cognitive scientists.  According to researchers, the brain associates the material being learned with the environment that the studier is in.  Varied environments allow the brain more opportunities to associate the material with something unique - which in turn increases the likelihood of retention.</p>
</div>
<?php /* <div class="multiple-topics <?php print ($count == -5 ? 'show' : 'hide'); ?>">
    <h3>Multiple topics in a study session</h3>
    <p>Good job!  We see that you have changed your study topics.  You are helping your brain retain the information that you are studying by activating different parts of your brain to solve different types of problems related to different subjects.</p>
</div>*/ ?>
<div class="same-topics <?php print ($count == 5 ? 'show' : 'hide'); ?>">
    <h3>Study different subjects to maximize effectiveness</h3>
    <p>We have a study tip for you.  We see that you have checked in for the same class two times in a row.  We highly recommend switching up your study materials.  By doing so, you are helping your brain retain the information that you are studying.  This will activate different parts of your brain to solve different types of problems related to different subjects which will be very helpful to keep you fresh for longer study sessions.</p>
</div>
<?php /*<div class="right-length <?php print ($count == -6 ? 'show' : 'hide'); ?>">
    <h3>Ideal study break length</h3>
    <p>Nicely done.  The ideal study break length can vary from person to person, but we encourage a 10-15 minute break.  Try that length break for a while to see if it improves your study performance.</p>
</div> */ ?>
<div class="too-short <?php print ($count == 6 ? 'show' : 'hide'); ?>">
    <h3>Ideal study break length</h3>
    <p>The ideal study break length can vary from person to person, but we encourage a 10-15 minute break.  Try that length break for a while to see if it improves your study performance.</p>
</div>
<div class="no-highlighting <?php print ($count == 7 ? 'show' : 'hide'); ?>">
    <h3>Highlighting/underlining overuse</h3>
    <p>Highlighting can actually be counter-productive.  We really wish we had known this years ago before we used a bathtub worth of highlighting fluid during our time as a student.  Highlighting, underlining, and rereading are all very passive approaches that have been used as a crutch for years.  Instead, try to create flash cards – they actually work quite well.</p>
</div>
<div class="no-music <?php print ($count == 8 ? 'show' : 'hide'); ?>">
    <h3>Think hard about music</h3>
    <p>This is a hotly debated topic.  From the many studies we have sifted through, there doesn’t appear to be much evidence to support the benefits of music during studying.  There are, however, indications that music can be a distraction and we therefor suggest skipping it all together.  If you do decide to experiment with listening to music, we suggest avoiding popular music with fast beats.  Instead, listen to musical scores with no lyrics.  Basically, you should not consciously realize that music is playing in the background, otherwise it can be a distraction for you.   At the end of the day, this is gray area, find what works for you.</p>
</div>
<div class="no-cell <?php print ($count == 9 ? 'show' : 'hide'); ?>">
    <h3>Ditch the cell phone</h3>
    <p>Your cell phone is your enemy when it comes to studying.  He have heard early scientific findings that it takes our brains 30-45 minutes to stop thinking about a phone call/text completely.  That is a huge distraction.  If you are like us, severing your electronic umbilical cord is extraordinarily tough to do.  If you have a smartphone, put it on airplane mode in order to avoid any distractions.  The texts and calls can wait…promise.  This obviously goes for tablets, ipods, kindles, and anything else that will draw your attention away from the task at hand.  And yes, we realize that this would cut off your check in.  Try checking in on your computer and putting all your mobile devices on airplane mode.</p>
</div>
<div class="no-comfort <?php print ($count == 10 ? 'show' : 'hide'); ?>">
    <h3>Don't get too comfortable</h3>
    <p>When studying, you want to put your body into a receptive mode that is conducive to learning and retention.  Getting too comfortable can be counterproductive and can make you drowsy while good posture can help you maintain alertness and enable you to study longer and more effectively.  Your bed is for sleeping, not studying...</p>
</div>
<div class="no-multitasking <?php print ($count == 11 ? 'show' : 'hide'); ?>">
    <h3>So you think you can multitask?</h3>
    <p>In our opinion, the ability to multitask is one of the big jokes of our time.  Doing several things inefficiently at once is a poor substitute for doing things efficiently one at a time.  Being able to multitask has somehow become a badge of honor for people when almost no one can do it well.  Studies show that multitasking can decrease performance by up to 40%!  One such study to read to understand the cognitive implications of  multitasking is a paper entitled “Executive Control of Cognitive Processes in Task Switching “ by Rubenstein, Meyer, and Evans.</p>
</div>
<div class="positive-mindset <?php print ($count == 12 ? 'show' : 'hide'); ?>">
    <h3>Get your mind right</h3>
    <p>Ever wonder why self-help gurus always hammer this point?  Well, the ever-expanding research on this topic shows that this may be a panacea to many of life’s ills.  Positive thinking is correlated with improved performance in almost all facets of life - school, career, and even life expectancy.  What does it actually mean to be positive?  One of the most interesting ways we have seen this described is through the concept of a person’s default explanatory style.  Optimists tend to explain unfortunate events as setbacks that can be remedied while pessimists view them as an inherent failure that will follow them through life.   There is a ton of fascinating research on this topic – if you are interested we recommend looking into the work of Dr. Seligman on Positive Psychology or Dr. Valliant’s work with Harvard’s Grant Study.</p>
</div>
<div class="be-prepared <?php print ($count == 13 ? 'show' : 'hide'); ?>">
    <h3>Be prepared</h3>
    <p>The Boy Scouts' motto rings true for studying too.  Try to gather all the materials needed for your study session before you start to focus.  Interrupting your session to search for things will drag down your productivity.  Another helpful tool is to be clear about what you need to accomplish in the study session.  That will keep you focused on the task at hand during your session.</p>
</div>
<?php $view['slots']->stop();

 $view['slots']->start('modal-footer') ?>
<a href="#study" class="btn btn-primary">Continue to session</a>
<?php $view['slots']->stop() ?>

