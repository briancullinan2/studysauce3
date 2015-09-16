<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
Let's get started building your personal study plan
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>We will guide you through a few steps to:<br /></p>
<ol>
    <li <?php print (!empty($course2 = $app->getUser()->getCourse2s()->first()) && $course2->getLesson2() == 4 ? 'style="text-decoration:line-through;"' : ''); ?>><a href="<?php print $view['router']->generate('course2_study_plan', ['_step' => 0]); ?>" class="cloak">Watch the <span class="reveal">study plan video</span></a></li>
    <li>Create your ideal study plan</li>
    <li>Download the plan to a calendar</li>
</ol>
<br />
<?php $view['slots']->stop();

$view['slots']->start('modal-footer'); ?>
<div class="highlighted-link <?php print (!empty($course2 = $app->getUser()->getCourse2s()->first()) && $course2->getLesson2() == 4 ? '' : 'invalid'); ?>">
    <a href="#plan-step-1" class="btn btn-primary">Get started</a>
</div>
<?php $view['slots']->stop();
