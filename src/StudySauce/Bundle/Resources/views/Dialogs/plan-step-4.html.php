<?php use StudySauce\Bundle\Entity\Course;

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Step 4 - Type of classes
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<form action="<?php print $view['router']->generate('profile_update'); ?>" method="post">
    <p>What is the primary type of studying you expect to do in this class?<br/><br/></p>
    <header>
        <label>Type of studying</label>
    </header>
    <?php
    foreach($courses as $i => $c)
    {
        /** @var Course $c */
        ?>
        <h4><span class="class<?php print $i; ?>"></span><?php print $c->getName(); ?></h4>
        <label class="input"><span>Type of studying</span>
            <select name="profile-type-<?php print $c->getId(); ?>">
                <option value="" <?php print (empty($c->getStudyType()) ? 'selected="selected"' : ''); ?>>- Select study type -</option>
                <option value="memorization" <?php print ($c->getStudyType() == 'memorization' ? 'selected="selected"' : ''); ?>>Memorization</option>
                <option value="reading" <?php print ($c->getStudyType() == 'reading' ? 'selected="selected"' : ''); ?>>Reading / writing</option>
                <option value="conceptual" <?php print ($c->getStudyType() == 'conceptual' ? 'selected="selected"' : ''); ?>>Conceptual</option>
            </select>
        </label>
        <br />
    <?php } ?>
    <br/><br/><br/><br/>
    <div class="highlighted-link setup-mode invalid">
        <ul class="dialog-tracker"><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li><li>&bullet;</li></ul>
        <button type="submit" class="more">Next</button>
    </div>
    <div class="highlighted-link invalid">
        <ul class="dialog-tracker"><li><a href="#plan-step-1" title="Class difficulty" data-toggle="modal">&bullet;</a></li><li><a href="#plan-step-3" title="Notifications" data-toggle="modal">&bullet;</a></li><li><a href="#plan-step-4" title="Class type" data-toggle="modal">&bullet;</a></li></ul>
        <button type="submit" class="more">Done</button>
    </div>
</form>
<?php $view['slots']->stop();
