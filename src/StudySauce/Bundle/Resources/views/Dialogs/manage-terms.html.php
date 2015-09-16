<?php use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Schedule;

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');
/** @var $schedule Schedule */

$view['slots']->start('modal-header'); ?>
Save your old schedule
<?php $view['slots']->stop();

$view['slots']->start('modal-body'); ?>
<p>Study Sauce can help you track your progress over multiple terms.  Use the Grade calculator to see your inputted grades from past terms.  Create a new schedule for each term so you can see how you have improved over time.</p>
<p>Save the previous term as:
    <label class="input"><select>
        <?php for ($y = intval(date('Y')); $y > intval(date('Y')) - 8; $y--) {
            foreach ([11 => 'Winter', 8 => 'Fall', 6 => 'Summer', 1 => 'Spring'] as $m => $t) {
                if(new \DateTime($y . '/' . $m . '/1') > new \DateTime())
                    continue; ?>
                <option value="<?php print $m; ?>/<?php print $y; ?>" <?php
                print (!empty($schedule->getTerm()) && $schedule->getTerm()->format('n/Y') == $m . '/' . $y
                    ? 'selected="selected"'
                    : ''); ?>><?php
                print $t; ?> <?php print $y; ?></option><?php
            }
        }
        ?></select></label></p>
<?php $view['slots']->stop();

$view['slots']->start('modal-footer') ?>
<a href="#close" class="btn" data-dismiss="modal">Cancel</a>
<a href="#create-schedule" class="btn btn-primary">Create a new schedule</a>
<?php $view['slots']->stop() ?>

