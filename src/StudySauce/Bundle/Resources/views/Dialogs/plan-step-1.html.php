<?php use StudySauce\Bundle\Entity\Course;

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 1 - Tell us about your classes
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<form action="<?php print $view['router']->generate('profile_update'); ?>" method="post">
    <p>How difficult do you expect each class to be?  This will help us determine how much time you should spend studying for each.<br/><br/></p>
    <header>
        <label title="Class should require less homework and/or be less difficult than most classes.  Will require less than 1 hours of studying for every hour of class">Easy</label>
        <label title="Class will be a pretty standard amount of homework/difficulty.  Will require ~2 hours studying for every hour of class">Average</label>
        <label title="Class will require much more homework and focus than others.  ~3 hours studying for every hour of class">Tough</label>
        <label title="Class will requires no studying or all of the studying is done during the class like in a lab">No studying</label>
    </header>
    <?php
    foreach($courses as $i => $c)
    {
        /** @var Course $c */
        ?>
        <h4 data-reoccurs="<?php print count($c->getDotw()); ?>"><span class="class<?php print $i; ?> course-id-<?php print $c->getId(); ?>"></span><?php print $c->getName(); ?></h4>
        <label title="Class should require less homework and/or be less difficult than most classes.  Will require less than 1 hours of studying for every hour of class"
               class="radio">
            <span>Easy</span>
            <input name="profile-difficulty-<?php print $c->getId(); ?>" type="radio" value="easy" <?php print ($c->getStudyDifficulty() == 'easy' ? 'checked="checked"' : ''); ?>><i></i></label>
        <label title="Class will be a pretty standard amount of homework/difficulty.  Will require ~2 hours studying for every hour of class"
               class="radio">
            <span>Average</span>
            <input name="profile-difficulty-<?php print $c->getId(); ?>" type="radio" value="average" <?php print ($c->getStudyDifficulty() == 'average' ? 'checked="checked"' : ''); ?>><i></i></label>
        <label title="Class will require much more homework and focus than others.  ~3 hours studying for every hour of class"
               class="radio">
            <span>Tough</span>
            <input name="profile-difficulty-<?php print $c->getId(); ?>" type="radio" value="tough" <?php print ($c->getStudyDifficulty() == 'tough' ? 'checked="checked"' : ''); ?>><i></i></label>
        <label title="Class will requires no studying or all of the studying is done during the class like in a lab"
               class="radio">
            <span>No studying</span>
            <input name="profile-difficulty-<?php print $c->getId(); ?>" type="radio" value="none" <?php print ($c->getStudyDifficulty() == 'none' ? 'checked="checked"' : ''); ?>><i></i></label>
        <br />
    <?php } ?>
    <br/><br/><br/><br/>
    <div class="highlighted-link setup-mode invalid">
        <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
        <button type="submit" class="more">Next</button>
    </div>
    <div class="highlighted-link invalid">
        <ul class="dialog-tracker"><li><a href="#plan-step-1" title="Class difficulty" data-toggle="modal">&bullet;</a></li><li><a href="#plan-step-3" title="Notifications" data-toggle="modal">&bullet;</a></li><li><a href="#plan-step-4" title="Class type" data-toggle="modal">&bullet;</a></li></ul>
        <button type="submit" class="more">Next</button>
    </div>
</form>
<?php $view['slots']->stop();
