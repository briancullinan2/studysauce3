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
                <tr>
                    <td><h4>Settings goals</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course1Bundle:SettingGoals:quiz.html.php', ['quiz' => $course1->getQuiz2()->first() ?: new Quiz2(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
                <tr>
                    <td><h4>Distractions</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course1Bundle:Distractions:quiz.html.php', ['quiz' => $course1->getQuiz4()->first() ?: new Quiz4(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
                <tr>
                    <td><h4>Procrastination</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course1Bundle:Procrastination:quiz.html.php', ['quiz' => $course1->getQuiz3()->first() ?: new Quiz3(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
                <tr>
                    <td><h4>Study environment</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course1Bundle:Environment:quiz.html.php', ['quiz' => $course1->getQuiz5()->first() ?: new Quiz5(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
                <tr>
                    <td><h4>Partners</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course1Bundle:Partners:quiz.html.php', ['quiz' => $course1->getQuiz6()->first() ?: new Quiz6(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
            </table>
        </td></tr>
    <tr>
        <td><h3>Course 2</h3></td>
    </tr>
    <tr><td>
            <table>
                <tr>
                    <td><h4>Study metrics</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course2Bundle:StudyMetrics:quiz.html.php', ['quiz' => $course2->getStudyMetrics()->first() ?: new \Course2\Bundle\Entity\StudyMetrics(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
                <tr>
                    <td><h4>Study plans</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course2Bundle:StudyPlan:quiz.html.php', ['quiz' => $course2->getStudyPlan()->first() ?: new \Course2\Bundle\Entity\StudyPlan(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
                <tr>
                    <td><h4>Interleaving</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course2Bundle:Interleaving:quiz.html.php', ['quiz' => $course2->getInterleaving()->first() ?: new \Course2\Bundle\Entity\Interleaving(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
                <tr>
                    <td><h4>Studying for tests</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course2Bundle:StudyTests:quiz.html.php', ['quiz' => $course2->getStudyTests()->first() ?: new \Course2\Bundle\Entity\StudyTests(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
                <tr>
                    <td><h4>Test taking</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course2Bundle:TestTaking:quiz.html.php', ['quiz' => $course2->getTestTaking()->first() ?: new \Course2\Bundle\Entity\TestTaking(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
            </table>
        </td></tr>
    <tr>
        <td><h3>Course 3</h3></td>
    </tr>
    <tr><td>
            <table>
                <tr>
                    <td><h4>Intro to strategies</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course3Bundle:Strategies:quiz.html.php', ['quiz' => $course3->getStrategies()->first() ?: new \Course3\Bundle\Entity\Strategies(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
                <tr>
                    <td><h4>Group study</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course3Bundle:GroupStudy:quiz.html.php', ['quiz' => $course3->getGroupStudy()->first() ?: new \Course3\Bundle\Entity\GroupStudy(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
                <tr>
                    <td><h4>Teach to learn</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course3Bundle:Teaching:quiz.html.php', ['quiz' => $course3->getTeaching()->first() ?: new \Course3\Bundle\Entity\Teaching(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
                <tr>
                    <td><h4>Active reading</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course3Bundle:ActiveReading:quiz.html.php', ['quiz' => $course3->getActiveReading()->first() ?: new \Course3\Bundle\Entity\ActiveReading(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
                    </td>
                </tr>
                <tr>
                    <td><h4>Spaced repetition</h4></td>
                </tr>
                <tr>
                    <td class="read-only">
                        <?php print $view->render('Course3Bundle:SpacedRepetition:quiz.html.php', ['quiz' => $course3->getSpacedRepetition()->first() ?: new \Course3\Bundle\Entity\SpacedRepetition(), 'csrf_token' => '', 'exclude_layout' => true]); ?>
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