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
class Course3Cest
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
    public function tryAllCourse3(AcceptanceTester $I)
    {
        $I->wantTo('complete all of level 3');
        $I->seeAmOnUrl('/course/3/lesson/1/step');
        $I->test('tryCourse3Strategies');
        // use the menu to get to the next lesson
        $I->click('#left-panel a[href="#expand"]');
        $I->click('Level 3');
        $I->click('Group study');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/3/lesson/2/step');
        $I->test('tryCourse3GroupStudy');
        // use the menu to get to lesson 4
        $I->click('#left-panel a[href="#expand"]');
        $I->click('Level 3');
        $I->click('Teach to learn');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/3/lesson/3/step');
        $I->test('tryCourse3Teaching');
        // use the menu to get to lesson 5
        $I->click('#left-panel a[href="#expand"]');
        $I->click('Level 3');
        $I->click('Active reading');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/3/lesson/4/step');
        $I->test('tryCourse3ActiveReading');
        // use the menu to get to lesson 6
        $I->click('#left-panel a[href="#expand"]');
        $I->click('Level 3');
        $I->click('Spaced repetition');
        $I->wait(5);
        $I->seeInCurrentUrl('/course/3/lesson/5/step');
        $I->test('tryCourse3SpacedRepetition');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse3Strategies(AcceptanceTester $I)
    {
        $I->wantTo('complete intro to strategies course');
        $I->seeAmOnUrl('/course/3/lesson/1/step');
        $I->click('#course3_strategies .highlighted-link a');
        $I->wait(25);
        $I->click('#course3_strategies-step1 a[href="#yt-pause"]');
        $I->click('#course3_strategies-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[value="flash"] + i');
        $I->click('input[value="teaching"] + i');
        $I->click('input[value="practice"] + i');
        $I->click('#course3_strategies-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course3_strategies-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course3_strategies-step3 .highlighted-link a');
        $I->wait(5);
        $I->click('#course3_strategies-step4 .highlighted-link a');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse3GroupStudy (AcceptanceTester $I) {
        $I->wantTo('complete course 2');
        $I->seeAmOnUrl('/course/3/lesson/2/step');
        $I->click('#course3_group_study .highlighted-link a');
        $I->wait(25);
        $I->click('#course3_group_study-step1 a[href="#yt-pause"]');
        $I->click('#course3_group_study-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[value="writing"] + i');
        $I->click('input[value="memorizing"] + i');
        $I->click('input[name="quiz-building"][value="3"] + i');
        $I->fillField('input[name="quiz-groupRole"]', 'The leader role');
        $I->click('input[name="quiz-groupBreaks"][value="1"] + i');
        $I->click('#course3_group_study-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course3_group_study-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course3_group_study-step3 .highlighted-link a');
        $I->wait(5);
        $I->fillField('#course3_group_study-step4 textarea', 'working with other people');
        $I->click('#course3_group_study-step4 .highlighted-link a');
        $I->wait(15);

    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse3Teaching (AcceptanceTester $I) {
        $I->wantTo('complete course 3');
        $I->seeAmOnUrl('/course/3/lesson/3/step');
        $I->click('#course3_teaching .highlighted-link a');
        $I->wait(25);
        $I->click('#course3_teaching-step1 a[href="#yt-pause"]');
        $I->click('#course3_teaching-step1 .highlighted-link a');
        $I->wait(5);
        $I->fillField('input[name="quiz-newLanguage"]', 'can\'t guess answers');
        $I->click('input[name="quiz-memorizing"] + i');
        $I->fillField('input[name="quiz-videotaping"]', 'You can tell if you understand it by watching yourself');
        $I->click('#course3_teaching-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course3_teaching-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course3_teaching-step3 .highlighted-link a');
        $I->wait(5);
        $I->click('#course3_teaching-step4 .highlighted-link a');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse3ActiveReading (AcceptanceTester $I) {
        $I->wantTo('complete course 4');
        $I->seeAmOnUrl('/course/3/lesson/4/step');
        $I->click('#course3_active_reading .highlighted-link a');
        $I->wait(25);
        $I->click('#course3_active_reading-step1 a[href="#yt-pause"]');
        $I->click('#course3_active_reading-step1 .highlighted-link a');
        $I->wait(5);
        $I->fillField('textarea[name="quiz-whatReading"]', 'recognizing the important parts');
        $I->click('input[name="quiz-highlighting"][value="0"] + i');
        $I->click('input[name="quiz-skimming"][value="1"] + i');
        $I->click('input[name="quiz-selfExplanation"][value="1"] + i');
        $I->click('#course3_active_reading-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course3_active_reading-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course3_active_reading-step3 .highlighted-link a');
        $I->wait(5);
        $I->click('#course3_active_reading-step4 .highlighted-link a');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryCourse3SpacedRepetition (AcceptanceTester $I) {
        $I->wantTo('complete course 5');
        $I->seeAmOnUrl('/course/3/lesson/5/step');
        $I->click('#course3_spaced_repetition .highlighted-link a');
        $I->wait(25);
        $I->click('#course3_spaced_repetition-step1 a[href="#yt-pause"]');
        $I->click('#course3_spaced_repetition-step1 .highlighted-link a');
        $I->wait(5);
        $I->click('input[name="quiz-spaceOut"][value="0"] + i');
        $I->fillField('textarea[name="quiz-forgetting"]', 'forgetting everything in a few days');
        $I->click('input[value="weekly"] + i');
        $I->click('input[value="blocked"] + i');
        $I->click('#course3_spaced_repetition-step2 .highlighted-link a:nth-of-type(1)');
        $I->wait(15);
        $I->click('#course3_spaced_repetition-step2 .highlighted-link a:nth-of-type(2)');
        $I->wait(5);
        $I->click('#course3_spaced_repetition-step3 .highlighted-link a');
        $I->wait(5);
        $I->click('#course3_spaced_repetition-step4 .highlighted-link a');
        $I->wait(15);
    }

}