<?php
namespace Admin\Bundle\Tests;

use WebDriver;
use WebDriverBy;
use WebDriverKeys;

/**
 * Class PageLoaderCest
 * @package StudySauce\Bundle\Tests
 * @backupGlobals false
 * @backupStaticAttributes false
 */
class Course2Cest
{
    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _after(AcceptanceTester $I)
    {
    }

    // tests

    /**
     * @depends tryGuestCheckout
     * @depends tryNewSchedule
     * @param AcceptanceTester $I
     */
    public function tryAllCourse2(AcceptanceTester $I)
    {
        $I->wantTo('complete all of level 2');
        $I->seeAmOnUrl('/course/2/lesson/1/step');
        $I->test('tryCourse2StudyMetrics');
        $I->seeInCurrentUrl('/metrics');
        $I->test('tryNewMetrics');
        // use the menu to get to the next lesson
        $I->click('#left-panel a[href="#expand"] span');
        $I->click('Level 2');
        $I->click('Study plan');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/2/lesson/2/step');
        $I->test('tryCourse2StudyPlan');
        $I->seeInCurrentUrl('/plan');
        $I->test('tryNewPlan');
        // use the menu to get to lesson 4
        $I->click('#left-panel a[href="#expand"] span');
        $I->click('Level 2');
        $I->click('Interleaving');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/2/lesson/3/step');
        $I->test('tryCourse2Interleaving');
        $I->seeInCurrentUrl('/checkin');
        $I->test('tryNewCheckin');
        // use the menu to get to lesson 5
        $I->click('#left-panel a[href="#expand"] span');
        $I->click('Level 2');
        $I->click('Studying for tests');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/2/lesson/4/step');
        $I->test('tryCourse2StudyTests');
        $I->test('tryNewHome');
        // use the menu to get to lesson 6
        $I->click('#left-panel a[href="#expand"] span');
        $I->click('Level 2');
        $I->click('Test-taking');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/2/lesson/5/step');
        $I->test('tryCourse2TestTaking');
        $I->test('tryNewCalculator');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse2StudyMetrics(AcceptanceTester $I)
    {
        $I->wantTo('complete study metrics course');
        $I->seeAmOnUrl('/course/2/lesson/1/step');
        $I->click('#course2_study_metrics .highlighted-link a');
        $I->wait(25);
        $I->click('#course2_study_metrics-step1 a[href="#yt-pause"]');
        $I->click('#course2_study_metrics-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[value="procrastination"] + i');
        $I->click('input[name="quiz-doingWell"][value="1"] + i');
        $I->fillField('input[name="quiz-allTogether"]', 'they don\'t');
        $I->click('#course2_study_metrics-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course2_study_metrics-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course2_study_metrics-step3 .highlighted-link a');
        $I->wait(5);
        $I->click('#course2_study_metrics-step4 .highlighted-link a');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse2StudyPlan (AcceptanceTester $I) {
        $I->wantTo('complete course 2');
        $I->seeAmOnUrl('/course/2/lesson/2/step');
        $I->click('#course2_study_plan .highlighted-link a');
        $I->wait(25);
        $I->click('#course2_study_plan-step1 a[href="#yt-pause"]');
        $I->click('#course2_study_plan-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[name="quiz-multiply"][value="3"] + i');
        $I->fillField('textarea[name="quiz-procrastination"]', 'helps your commit time');
        $I->fillField('textarea[name="quiz-studySessions"]', 'good for projects');
        $I->fillField('textarea[name="quiz-stickPlan"]', 'start immediately');
        $I->click('#course2_study_plan-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course2_study_plan-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course2_study_plan-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Study plan');
        $I->click('Study plan');
        $I->wait(15);

    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse2Interleaving (AcceptanceTester $I) {
        $I->wantTo('complete course 3');
        $I->seeAmOnUrl('/course/2/lesson/3/step');
        $I->click('#course2_interleaving .highlighted-link a');
        $I->wait(25);
        $I->click('#course2_interleaving-step1 a[href="#yt-pause"]');
        $I->click('#course2_interleaving-step1 .highlighted-link a');
        $I->wait(5);
        $I->fillField('input[name="quiz-multipleSessions"]', 'blocked practice');
        $I->fillField('input[name="quiz-otherName"]', 'varied practice');
        $I->click('input[name="quiz-typesCourses"][value="0"] + i');
        $I->click('#course2_interleaving-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course2_interleaving-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course2_interleaving-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Check in');
        $I->click('Check in');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse2StudyTests (AcceptanceTester $I) {
        $I->wantTo('complete course 4');
        $I->seeAmOnUrl('/course/2/lesson/4/step');
        $I->click('#course2_study_tests .highlighted-link a');
        $I->wait(25);
        $I->click('#course2_study_tests-step1 a[href="#yt-pause"]');
        $I->click('#course2_study_tests-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[name="quiz-typesTests"][value="essay"] + i');
        $I->fillField('input[name="quiz-mostImportant"]', 'space out your studying');
        $I->fillField('input[name="quiz-openTips1"]', 'study more');
        $I->fillField('input[name="quiz-openTips2"]', 'get organized');
        $I->click('#course2_study_tests-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course2_study_tests-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course2_study_tests-step3 .highlighted-link a');
        $I->wait(5);
        $I->fillField('#course2_study_tests-step4 textarea', 'they are all subjective, I\'m and art major');
        $I->seeLink('Go home');
        $I->click('Go home');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse2TestTaking (AcceptanceTester $I) {
        $I->wantTo('complete course 5');
        $I->seeAmOnUrl('/course/2/lesson/5/step');
        $I->click('#course2_test_taking .highlighted-link a');
        $I->wait(25);
        $I->click('#course2_test_taking-step1 a[href="#yt-pause"]');
        $I->click('#course2_test_taking-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[name="quiz-ideaCram"][value="0"] + i');
        $I->fillField('input[name="quiz-breathing"]', 'four part breathing');
        $I->fillField('input[name="quiz-skimming"]', 'number of questions');
        $I->click('#course2_test_taking-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course2_test_taking-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course2_test_taking-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Go to grade calculator');
        $I->click('Go to grade calculator');
        $I->wait(15);
    }

}