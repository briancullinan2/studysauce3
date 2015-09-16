<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var @var User $user */
$user = $app->getUser();

 echo $view['actions']->render(new ControllerReference('StudySauceBundle:Course:menu')); ?>

<aside id="right-panel" class="collapsed">
    <nav>
        <a href="#expand"><span class="navbar-toggle">Study Tools</span></a>
        <ul class="main-menu">
            <li><a href="#collapse">Hide</a><h3>Study Tools</h3></li>
            <li><a href="<?php print $view['router']->generate('home'); ?>"><span>&nbsp;</span>Home</a></li>
            <li><a href="<?php print $view['router']->generate('goals'); ?>"><span>&nbsp;</span>Goals</a></li>
            <li><a href="<?php print $view['router']->generate('schedule'); ?>"><span>&nbsp;</span>Class schedule</a></li>
            <li><a href="<?php print $view['router']->generate('deadlines'); ?>"><span>&nbsp;</span>Deadlines</a></li>
            <li><a href="<?php print $view['router']->generate('checkin'); ?>"><span>&nbsp;</span>Check in</a></li>
            <li><a href="<?php print $view['router']->generate('metrics'); ?>"><span>&nbsp;</span>Study metrics</a></li>
            <li><a href="<?php print $view['router']->generate('partner'); ?>"><span>&nbsp;</span>Accountability partner</a></li>
            <li><a href="<?php print $view['router']->generate('calculator'); ?>"><span>&nbsp;</span>Grade calculator</a></li>
            <li><a href="<?php print $view['router']->generate('notes'); ?>"><span>&nbsp;</span>Notes</a></li>
            <?php if(!$user->hasRole('ROLE_PAID')) { ?>
                <li><a href="<?php print $view['router']->generate('premium'); ?>"><span>&nbsp;</span>Premium</a></li>
            <?php } ?>
            <li><a href="<?php print $view['router']->generate('plan'); ?>"><span>&nbsp;</span>Study plan<?php if(!$user->hasRole('ROLE_PAID')) { ?> <sup class="premium">Beta</sup><?php } ?></a></li>
            <li><a href="<?php print $view['router']->generate('account'); ?>"><span>&nbsp;</span>Account settings</a></li>
            <?php /*
            <li><h3>Coming soon</h3></li>
            <li><a href="#midterm"><span>&nbsp;</span>Midterm/final planner</a></li>
            <li><a href="#quizlet"><span>&nbsp;</span>Quizlet</a></li>
            <li><a href="#drive"><span>&nbsp;</span>Google Drive</a></li>
            <li><a href="#blackboard"><span>&nbsp;</span>Blackboard</a></li>
            */ ?>
        </ul>
    </nav>
</aside>