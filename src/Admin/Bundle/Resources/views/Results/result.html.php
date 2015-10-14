<?php
use Course1\Bundle\Entity\Course1;
use Course1\Bundle\Entity\Quiz1;
use Course1\Bundle\Entity\Quiz2;
use Course1\Bundle\Entity\Quiz3;
use Course1\Bundle\Entity\Quiz4;
use Course1\Bundle\Entity\Quiz5;
use Course1\Bundle\Entity\Quiz6;
use Course2\Bundle\Entity\Course2;
use Course3\Bundle\Entity\Course3;

/** @var Course1 $course1 */
/** @var Course2 $course2 */
/** @var Course3 $course3 */

?>
<table>
    <tr>
        <td><h3>Course 1</h3></td>
    </tr>
    <tr><td>
            <table>
                <tr>
                    <td><h4>Introduction</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course1Bundle:Introduction:quiz.html.php', ['quiz' => $course1->getQuiz1()->first() ?: new Quiz1(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
            </table>
        </td></tr>
    <tr>
        <td><h3>Feedback</h3></td>
    </tr>
    <tr><td class="read-only">
            <div class="panel-pane course1 step2" id="course1_introduction-step4">
                <div class="pane-content">
                    <h3>Why do you want to become better at studying?</h3>
                    <label class="input">
                        <textarea placeholder="" cols="60" rows="2"><?php print $view->escape($course1->getWhyStudy()); ?></textarea>
                    </label>
                    <h3>Do you have any feedback?</h3>
                    <label class="input">
                        <textarea name="investment-feedback"><?php print $course3->getFeedback(); ?></textarea>
                    </label>
                    <h3>Score</h3>
                    <label class="radio">
                        <input type="radio" name="investment-net-promoter" checked="checked"/><i></i><br/><span><?php print $course3->getNetPromoter(); ?></span>
                    </label>
                </div>
            </div>
    </td></tr>
</table>