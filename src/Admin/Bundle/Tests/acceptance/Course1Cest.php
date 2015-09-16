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
class Course1Cest
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
     * @param AcceptanceTester $I
     */
    public function tryFreeCourse(AcceptanceTester $I)
    {
        $I->wantTo('complete all the free courses');
        $I->seeAmOnUrl('/course/1/lesson/1/step');
        $I->test('tryCourse1Introduction');
        $I->seeInCurrentUrl('/course/1/lesson/2/step');
        $I->test('tryCourse1SettingGoals');
        $I->seeInCurrentUrl('/goals');
        $I->test('tryNewGoals');
        // use the menu to get to lesson 3
        $I->click('#left-panel a[href="#expand"] span');
        $I->click('Level 1');
        $I->click('Distractions');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/1/lesson/3/step');
        $I->test('tryCourse1Distractions');
        $I->seeInCurrentUrl('/schedule');
        $I->test('tryNewSchedule');
        // use the menu to get to lesson 4
        $I->click('#left-panel a[href="#expand"] span');
        $I->click('Level 1');
        $I->click('Procrastination');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/1/lesson/4/step');
        $I->test('tryCourse1Procrastination');
        $I->seeInCurrentUrl('/deadlines');
        $I->test('tryNewDeadlines');
        // use the menu to get to lesson 5
        $I->click('#left-panel a[href="#expand"] span');
        $I->click('Level 1');
        $I->click('Study environment');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/1/lesson/5/step');
        $I->test('tryCourse1Environment');
        $I->seeInCurrentUrl('/checkin');
        $I->test('tryNewCheckin');
        $I->seeInCurrentUrl('/metrics');
        // use the menu to get to lesson 6
        $I->click('#left-panel a[href="#expand"] span');
        $I->click('Level 1');
        $I->click('Partners');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/1/lesson/6/step');
        $I->test('tryCourse1Partners');
        $I->seeInCurrentUrl('/partner');
        $I->test('tryNewPartner');
        // use the menu to get to lesson 7
        /*
        $I->click('#left-panel a[href="#expand"] span');
        $I->click('Level 1');
        $I->click('End of Level 1');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/1/lesson/7/step');
        $I->test('tryCourse1End');
        */
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse1Introduction(AcceptanceTester $I)
    {
        $I->wantTo('complete course 1');
        $I->executeInSelenium(function (WebDriver $driver) use ($I) {
            $dialog = $driver->findElement(WebDriverBy::cssSelector('#account-options'));
            if($dialog->isDisplayed()) {
                $I->click('#account-options a[href="#close"]');
            }
        });
        $I->seeAmOnUrl('/course/1/lesson/1/step');
        $I->seeLink('Launch');
        $I->click('Launch');
        $I->wait(25);
        $I->click('#course1_introduction-step1 a[href="#yt-pause"]');
        $I->click('#course1_introduction-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[value="college-senior"] + i');
        $I->click('input[value="born"] + i');
        $I->click('input[value="cram"] + i');
        $I->click('input[value="on"] + i');
        $I->click('input[value="two"] + i');
        $I->click('#course1_introduction-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course1_introduction-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course1_introduction-step3 .highlighted-link a');
        $I->wait(5);
        $I->fillField('#course1_introduction-step4 textarea', 'To get better grades!');
        $I->click('#course1_introduction-step4 .highlighted-link a');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse1SettingGoals (AcceptanceTester $I) {
        $I->wantTo('complete course 2');
        $I->seeAmOnUrl('/course/1/lesson/2/step');
        $I->click('#course1_setting_goals .highlighted-link a');
        $I->wait(25);
        $I->click('#course1_setting_goals-step1 a[href="#yt-pause"]');
        $I->click('#course1_setting_goals-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[value="60"] + i');
        $I->fillField('input[name="quiz-specific"]', 'specific');
        $I->fillField('input[name="quiz-measurable"]', 'measurable');
        $I->fillField('input[name="quiz-achievable"]', 'achievable');
        $I->fillField('input[name="quiz-relevant"]', 'relevant');
        $I->fillField('input[name="quiz-timeBound"]', 'time-bound');
        $I->fillField('input[name="quiz-intrinsic"]', 'intrinsic');
        $I->fillField('input[name="quiz-extrinsic"]', 'extrinsic');
        $I->click('#course1_setting_goals-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course1_setting_goals-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course1_setting_goals-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Set up my goals');
        $I->click('Set up my goals');
        $I->wait(15);

    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse1Distractions (AcceptanceTester $I) {
        $I->wantTo('complete course 3');
        $I->seeAmOnUrl('/course/1/lesson/3/step');
        $I->click('#course1_distractions .highlighted-link a');
        $I->wait(25);
        $I->click('#course1_distractions-step1 a[href="#yt-pause"]');
        $I->click('#course1_distractions-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[name="quiz-multitask"][value="false"] + i');
        $I->click('input[name="quiz-downside"][value="remember"] + i');
        $I->click('input[name="quiz-lower-score"][value="30"] + i');
        $I->click('input[name="quiz-distraction"][value="40"] + i');
        $I->click('#course1_distractions-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course1_distractions-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course1_distractions-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Enter class schedule');
        $I->click('Enter class schedule');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse1Procrastination (AcceptanceTester $I) {
        $I->wantTo('complete course 4');
        $I->seeAmOnUrl('/course/1/lesson/4/step');
        $I->click('#course1_procrastination .highlighted-link a');
        $I->wait(25);
        $I->click('#course1_procrastination-step1 a[href="#yt-pause"]');
        $I->click('#course1_procrastination-step1 .highlighted-link a');
        $I->wait(5);
        $I->fillField('input[name="quiz-activeMemory"]', 'active');
        $I->fillField('input[name="quiz-referenceMemory"]', 'reference');
        $I->fillField('input[name="quiz-studyGoal"]', 'retain information');
        $I->fillField('input[name="quiz-procrastinating"]', 'space studying');
        $I->fillField('input[name="quiz-deadlines"]', 'deadlines');
        $I->fillField('input[name="quiz-plan"]', 'study plan');
        $I->click('#course1_procrastination-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course1_procrastination-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course1_procrastination-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Set up deadlines');
        $I->click('Set up deadlines');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse1Environment (AcceptanceTester $I) {
        $I->wantTo('complete course 5');
        $I->seeAmOnUrl('/course/1/lesson/5/step');
        $I->click('#course1_environment .highlighted-link a');
        $I->wait(25);
        $I->click('#course1_environment-step1 a[href="#yt-pause"]');
        $I->click('#course1_environment-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[name="quiz-bed"][value="0"] + i');
        $I->click('input[name="quiz-mozart"][value="0"] + i');
        $I->click('input[name="quiz-nature"][value="1"] + i');
        $I->click('input[name="quiz-breaks"][value="1"] + i');
        $I->click('#course1_environment-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course1_environment-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course1_environment-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Check in');
        $I->click('Check in');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse1Partners (AcceptanceTester $I) {
        $I->wantTo('complete course 6');
        $I->seeAmOnUrl('/course/1/lesson/6/step');
        $I->click('#course1_partners .highlighted-link a');
        $I->wait(25);
        $I->click('#course1_partners-step1 a[href="#yt-pause"]');
        $I->click('#course1_partners-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[value="focus"] + i');
        $I->click('input[value="incentive"] + i');
        $I->click('input[value="knows"] + i');
        $I->fillField('input[name="quiz-often"]', 'Once a week');
        $I->click('input[value="dieting"] + i');
        $I->click('#course1_partners-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course1_partners-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course1_partners-step3 .highlighted-link a');
        $I->wait(5);
        $I->seeLink('Invite a partner');
        $I->click('Invite a partner');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse1End (AcceptanceTester $I) {
        $I->wantTo('complete course 7');
        $I->seeAmOnUrl('/course/1/lesson/7/step');
        $I->click('#course1_upgrade .highlighted-link a');
        $I->wait(25);
        $I->click('#course1_upgrade-step1 a[href="#yt-pause"]');
        $I->click('#course1_upgrade-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[name="quiz-enjoyed"][value="1"] + i');
        $I->click('#course1_upgrade-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course1_upgrade-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course1_upgrade-step3 .highlighted-link a');
        $I->wait(5);
        $I->click('input[name="investment-net-promoter"][value="5"] + i');
        $I->seeLink('Upgrade to premium');
        $I->click('Upgrade to premium');
        $I->wait(15);
    }
}