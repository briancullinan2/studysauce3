<?php
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Deadline;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/deadlines.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/deadlines.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/deadlines.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<script type="text/javascript">
    $(document).ready(function () {
        $('body').on('change', '#deadlines .class-name select', function () {
            $(this).parents('.deadline-row').find('.assignment input').val($(this).val());
        });
    });
</script>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane <?php
    print ($isEmpty ? ' empty' : ''); ?>" id="deadlines">
    <div class="pane-content">
        <h2>Enter important dates and we will send you email reminders</h2>
        <form action="<?php print $view['router']->generate('update_deadlines'); ?>" method="post">
        <div class="sort-by">
            <label>Sort by: </label>
            <label class="radio"><input type="radio" name="deadlines-sort" value="date"
                                        checked="checked"/><i></i>Date</label>
            <label class="radio"><input type="radio" name="deadlines-sort" value="class"><i></i>Class</label>
            <label class="checkbox" title="Click here to see deadlines that have already passed."><input
                    type="checkbox" checked="checked"><i></i>Past deadlines</label>
        </div>
        <?php if($isEmpty) { ?>
            <div class="deadline-row invalid edit course-id- deadline-id-">
            <div class="class-name">
                <label class="select">
                    <span>Class name</span>
                    <i class="class"></i>
                    <select>
                        <option value="" selected="selected">Select a class</option>
                        <option>Course completion</option>
                        <option>Adviser completion</option>
                        <option>Past due</option>
                    </select>
                </label>
            </div>
            <div class="assignment">
                <label class="select">
                    <span>Assignment</span>
                    <input placeholder="Paper, exam, project, etc." type="text" value="" size="60" maxlength="255">
                </label>
            </div>
            <div class="reminder">
                <label>Reminders</label>
                <label class="checkbox"><input type="checkbox" value="1209600" ><i></i>2 wk</label>
                <label class="checkbox"><input type="checkbox" value="-86400" ><i></i>1 day after</label>
                <label class="checkbox"><input type="checkbox" value="604800" ><i></i>1 wk</label>
                <label class="checkbox"><input type="checkbox" value="-604800" ><i></i>1 wk after</label>
                <label class="checkbox"><input type="checkbox" value="345600" ><i></i>4 days</label>
                <label class="checkbox"><input type="checkbox" value="-1209600" ><i></i>2 wk after</label>
                <label class="checkbox"><input type="checkbox" value="172800" ><i></i>2 days</label>
                <label class="checkbox"><input type="checkbox" value="-2419200" ><i></i>4 wk after</label>
                <label class="checkbox"><input type="checkbox" value="86400" ><i></i>1 day</label>
                <label class="checkbox"><input type="checkbox" value="-4838400"><i></i>8 wk after</label>
                <label class="checkbox"><input type="checkbox" value="0"><i></i>day of</label>
            </div>
            <div class="due-date">
                <label class="input">
                    <span>Due date</span>
                    <input placeholder="Enter due date" type="text" value="" size="5" maxlength="255">
                </label>
            </div>
            <div class="read-only">
                <a href="#edit-deadline">&nbsp;</a><a href="#remove-deadline">&nbsp;</a>
            </div>
            </div>
        <?php } ?>
        <div class="highlighted-link form-actions invalid">
            <a href="<?php print $view['router']->generate('schedule'); ?>">Edit schedule</a>
            <a href="#add-deadline" class="big-add">Add <span>+</span> deadline</a>
            <div class="invalid-only">You must complete all fields before moving on.</div>
            <button type="submit" value="#save-deadline" class="more">Save</button>
        </div>
        <header>
            <label>&nbsp;</label>
            <label>Assignment</label>
            <label>Reminders</label>
            <label>Due date</label>
            <label>% of grade</label>
        </header>
        <?php
        $headStr = '';
        usort($deadlines, function (Deadline $a, Deadline $b) use($courses) {
                $aI = array_search($a->getCourse(), $courses);
                $bI = array_search($b->getCourse(), $courses);
                return ($a->getDueDate()->getTimestamp() + $aI) -
                        ($b->getDueDate()->getTimestamp() + $bI);});
        foreach ($deadlines as $i => $d) {

            /** @var $d Deadline */
            $newHeadStr = $d->getDueDate()->format('j F') . ' <span>' . $d->getDueDate()->format('Y') . '</span>';
            if ($headStr != $newHeadStr) {
                $headStr = $newHeadStr;
                $classes = [];
                if ($d->getDueDate() < date_sub(new \Datetime('today'), new \DateInterval('P1D'))) {
                    $classes[] = 'historic';
                }
                print '<div class="head ' . implode(' ', $classes) . '">' . $headStr . '</div>';
            }
            if (!empty($d->getReminder())) {
                $reminders = $d->getReminder();
            } else {
                $reminders = [];
            }

            ?>
            <div class="deadline-row invalid read-only <?php
            print ' course-id-' . (!empty($d->getCourse()) ? $d->getCourse()->getId() : '');
            print ($d->getDueDate() < date_sub(new \Datetime('today'), new \DateInterval('P1D')) ? ' historic' : '');
            print ' deadline-id-' . $d->getId(); ?>">
            <div class="class-name">
                <label class="select">
                    <span>Class name</span>
                    <i class="class<?php print (empty($d->getCourse()) ? '' : $d->getCourse()->getIndex()); ?>"></i>
                    <span class="sort-date-label"><?php print $newHeadStr; ?></span>
                    <select>
                        <option value="" <?php print (empty($d->getCourse()) ? 'selected="selected"' : ''); ?>>Select a class</option>
                        <option>Course completion</option>
                        <option>Adviser completion</option>
                        <option>Past due</option>
                    </select>
                </label>
            </div>
            <div class="assignment">
                <label class="select">
                    <span>Assignment</span>
                    <input placeholder="Paper, exam, project, etc." type="text"
                           value="<?php print $d->getAssignment(); ?>" size="60" maxlength="255">
                </label>
            </div>
            <div class="reminder">
                <label>Reminders</label>
                <label class="checkbox"><input type="checkbox" value="1209600" <?php print (in_array(
                        1209600,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i>2 wk</label>
                <label class="checkbox"><input type="checkbox" value="-86400" <?php print (in_array(
                        -86400,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i>1 day after</label>
                <label class="checkbox"><input type="checkbox" value="604800" <?php print (in_array(
                        604800,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i>1 wk</label>
                <label class="checkbox"><input type="checkbox" value="-604800" <?php print (in_array(
                        -604800,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i>1 wk after</label>
                <label class="checkbox"><input type="checkbox" value="345600" <?php print (in_array(
                        345600,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i>4 days</label>
                <label class="checkbox"><input type="checkbox" value="-1209600" <?php print (in_array(
                        -1209600,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i>2 wk after</label>
                <label class="checkbox"><input type="checkbox" value="172800" <?php print (in_array(
                        172800,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i>2 days</label>
                <label class="checkbox"><input type="checkbox" value="-2419200" <?php print (in_array(
                        -2419200,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i>4 wk after</label>
                <label class="checkbox"><input type="checkbox" value="86400" <?php print (in_array(
                        86400,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i>1 day</label>
                <label class="checkbox"><input type="checkbox" value="-4838400" <?php print (in_array(
                        -4838400,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i>8 wk after</label>
                <label class="checkbox"><input type="checkbox" value="0" <?php print (in_array(
                        0,
                        $reminders
                    ) ? 'checked="checked"' : ''); ?>><i></i>day of</label>
            </div>
            <div class="due-date">
                <label class="input">
                    <span>Due date</span>
                    <input placeholder="Enter due date" type="text"
                           value="<?php print $d->getDueDate()->format('m/d/Y'); ?>" size="5"
                           maxlength="255">
                </label>
            </div>
            <div class="read-only">
                <a href="#edit-deadline">&nbsp;</a><a href="#remove-deadline">&nbsp;</a>
            </div>
            </div>
        <?php } ?>
        <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>"/>
        </form>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');

$view['slots']->stop();
