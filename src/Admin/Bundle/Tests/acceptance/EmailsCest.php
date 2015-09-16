<?php
namespace Admin\Bundle\Tests;

use Codeception\Module\Doctrine2;
use StudySauce\Bundle\Entity\User;
use WebDriver;
use WebDriverBy;
use WebDriverKeys;

/**
 * Class EmailsCest
 * @package StudySauce\Bundle\Tests
 * @backupGlobals false
 * @backupStaticAttributes false
 */
class EmailsCest
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
     * @param AcceptanceTester $I
     */
    public function tryAllEmails(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $this->setLastLogin($I);
        $I->seeLink('deadlines sneak up on you');
        $I->click('//a[contains(.,"deadlines sneak up on you")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/deadlines"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/deadlines');
        $I->test('tryNewDeadlines');
        $this->setLastLogin($I);
        $I->seeLink('study notes again');
        $I->click('//a[contains(.,"study notes again")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/notes"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/notes');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $this->setLastLogin($I);
        $I->seeLink('Procrastinate much');
        $I->click('//a[contains(.,"Procrastinate much")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/course/1/lesson/4"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/course/1/lesson/4/step');
        $I->test('tryCourse1Procrastination');
        $this->setLastLogin($I);
        $I->seeLink('your grades like');
        $I->click('//a[contains(.,"your grades like")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/calculator"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/calculator');
        $I->test('tryNewCalculator');
        $this->setLastLogin($I);
        $I->seeLink('phone is killing');
        $I->click('//a[contains(.,"phone is killing")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/course/1/lesson/3"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/course/1/lesson/3/step');
        $I->test('tryCourse1Distractions');
        $this->setLastLogin($I);
        $I->seeLink('study for tests');
        $I->click('//a[contains(.,"study for tests")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/course/2/lesson/4"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/course/2/lesson/4/step');
        $I->test('tryCourse2StudyTests');
        $this->setLastLogin($I);
        $I->seeLink('taking tests freak');
        $I->click('//a[contains(.,"taking tests freak")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/course/2/lesson/5"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/course/2/lesson/5/step');
        $I->test('tryCourse2TestTaking');
        $this->setLastLogin($I);
        $I->seeLink('forget everything');
        $I->click('//a[contains(.,"forget everything")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/course/3/lesson/5"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/course/3/lesson/5/step');
        $I->test('tryCourse3SpacedRepetition');
        $this->setLastLogin($I);
        $I->seeLink('studying enough');
        $I->click('//a[contains(.,"studying enough")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/course/2/lesson/1"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/course/2/lesson/1/step');
        $I->test('tryCourse2StudyMetrics');
        $this->setLastLogin($I);
        $I->seeLink('music when you study');
        $I->click('//a[contains(.,"music when you study")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/course/1/lesson/5"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/course/1/lesson/5/step');
        $I->test('tryCourse1Environment');
        $this->setLastLogin($I);
        $I->seeLink('holding you accountable');
        $I->click('//a[contains(.,"holding you accountable")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/partner"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/partner');
        $I->test('tryNewPartner');
        $this->setLastLogin($I);
        $I->seeLink('spacing out when you read');
        $I->click('//a[contains(.,"spacing out when you read")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/course/3/lesson/4"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/course/3/lesson/4/step');
        $I->test('tryCourse3ActiveReading');
        $this->setLastLogin($I);
        $I->seeLink('setting goals');
        $I->click('//a[contains(.,"setting goals")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/goals"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/goals');
        $I->test('tryNewGoals');
        $this->setLastLogin($I);
        $I->seeLink('train your brain');
        $I->click('//a[contains(.,"train your brain")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/course/2/lesson/3"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/course/2/lesson/3/step');
        $I->test('tryCourse2Interleaving');
        $this->setLastLogin($I);
        $I->seeLink('studying with groups');
        $I->click('//a[contains(.,"studying with groups")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/course/3/lesson/2"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/course/3/lesson/2/step');
        $I->test('tryCourse3GroupStudy');
        $this->setLastLogin($I);
        $I->seeLink('Memorizing facts');
        $I->click('//a[contains(.,"Memorizing facts")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->seeElement('a[href*="/course/3/lesson/3"]');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/course/3/lesson/3/step');
        $I->test('tryCourse3Teaching');
    }

    public function tryStudentWelcomeEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->wantTo('visit mailinator and check for welcome student email');
        $I->amOnPage('/cron/emails');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('Welcome to Study Sauce');
        $I->click('//a[contains(.,"Welcome to Study Sauce")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/login"]');
    }

    public function tryPartnerWelcomeEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewPartner');
        $I->amOnPage('/cron/emails');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('needs your help with school');
        $I->click('//a[contains(.,"needs your help with school")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/partners"]');
    }

    public function tryDeadlineEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->amOnPage('/cron/emails');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('notification');
        $I->click('//a[contains(.,"notification")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->see('Exam 1');
    }

    public function tryCheckoutEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->wantTo('visit mailinator and check for welcome student email');
        $I->amOnPage('/cron/emails');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('Welcome to Study Sauce');
        $I->click('//a[contains(.,"Thank you")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/home"]');
    }

    private function setLastLogin(AcceptanceTester $I) {
        $I->seeAmOnUrl('/account');
        $I->wait(1);
        $email = $I->grabValueFrom('#account .email input');
        // change sign up date
        /** @var User $user */
        Doctrine2::$em->clear();
        $user = Doctrine2::$em->getRepository('StudySauceBundle:User')->findOneBy(['email' => $email]);
        $user->setLastLogin(date_sub(new \DateTime(), new \DateInterval('P8D')));
        if(!empty($user->getProperty('inactivity')))
            $user->setProperty('inactivity', $user->getProperty('inactivity') - 86400 * 8);
        Doctrine2::$em->merge($user);
        Doctrine2::$em->flush();
        $I->amOnPage('/cron/emails');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
    }

    public function tryInactivityDeadlinesEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $this->setLastLogin($I);
        $I->seeLink('deadlines sneak up on you');
        $I->click('//a[contains(.,"deadlines sneak up on you")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/deadlines"]');
    }

    public function tryInactivityNotesEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $this->setLastLogin($I);
        $I->seeLink('study notes again');
        $I->click('//a[contains(.,"study notes again")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/notes"]');
    }

    public function tryInactivityProcrastinationEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $this->setLastLogin($I);
        $I->seeLink('Procrastinate much');
        $I->click('//a[contains(.,"Procrastinate much")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/course/1/lesson/4"]');
    }

    public function tryInactivityCalculatorEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $this->setLastLogin($I);
        $I->seeLink('your grades like');
        $I->click('//a[contains(.,"your grades like")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/calculator"]');
    }

    public function tryInactivityDistractionsEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $I->test('tryNewCalculator');
        $this->setLastLogin($I);
        $I->seeLink('phone is killing');
        $I->click('//a[contains(.,"phone is killing")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/course/1/lesson/3"]');
    }

    public function tryInactivityStudyTestsEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $I->test('tryNewCalculator');
        $I->test('tryCourse1Distractions');
        $this->setLastLogin($I);
        $I->seeLink('study for tests');
        $I->click('//a[contains(.,"study for tests")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/course/2/lesson/4"]');
    }

    public function tryInactivityTestTakingEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $I->test('tryNewCalculator');
        $I->test('tryCourse1Distractions');
        $I->test('tryCourse2StudyTests');
        $this->setLastLogin($I);
        $I->seeLink('taking tests freak');
        $I->click('//a[contains(.,"taking tests freak")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/course/2/lesson/5"]');
    }

    public function tryInactivitySpacedRepetitionEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $I->test('tryNewCalculator');
        $I->test('tryCourse1Distractions');
        $I->test('tryCourse2StudyTests');
        $I->test('tryCourse2TestTaking');
        $this->setLastLogin($I);
        $I->seeLink('forget everything');
        $I->click('//a[contains(.,"forget everything")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/course/3/lesson/5"]');
    }

    public function tryInactivityStudyMetricsEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $I->test('tryNewCalculator');
        $I->test('tryCourse1Distractions');
        $I->test('tryCourse2StudyTests');
        $I->test('tryCourse2TestTaking');
        $I->test('tryCourse3SpacedRepetition');
        $this->setLastLogin($I);
        $I->seeLink('studying enough');
        $I->click('//a[contains(.,"studying enough")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/course/2/lesson/1"]');
    }

    public function tryInactivityEnvironmentEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $I->test('tryNewCalculator');
        $I->test('tryCourse1Distractions');
        $I->test('tryCourse2StudyTests');
        $I->test('tryCourse2TestTaking');
        $I->test('tryCourse3SpacedRepetition');
        $I->test('tryCourse2StudyMetrics');
        $this->setLastLogin($I);
        $I->seeLink('music when you study');
        $I->click('//a[contains(.,"music when you study")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/course/1/lesson/5"]');
    }

    public function tryInactivityPartnerEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $I->test('tryNewCalculator');
        $I->test('tryCourse1Distractions');
        $I->test('tryCourse2StudyTests');
        $I->test('tryCourse2TestTaking');
        $I->test('tryCourse3SpacedRepetition');
        $I->test('tryCourse2StudyMetrics');
        $I->test('tryCourse1Environment');
        $this->setLastLogin($I);
        $I->seeLink('holding you accountable');
        $I->click('//a[contains(.,"holding you accountable")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/partner"]');
    }

    public function tryInactivityActiveReadingEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $I->test('tryNewCalculator');
        $I->test('tryCourse1Distractions');
        $I->test('tryCourse2StudyTests');
        $I->test('tryCourse2TestTaking');
        $I->test('tryCourse3SpacedRepetition');
        $I->test('tryCourse2StudyMetrics');
        $I->test('tryCourse1Environment');
        $I->test('tryNewPartner');
        $this->setLastLogin($I);
        $I->seeLink('spacing out when you read');
        $I->click('//a[contains(.,"spacing out when you read")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/course/3/lesson/4"]');
    }

    public function tryInactivityGoalsEmail(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $I->test('tryNewCalculator');
        $I->test('tryCourse1Distractions');
        $I->test('tryCourse2StudyTests');
        $I->test('tryCourse2TestTaking');
        $I->test('tryCourse3SpacedRepetition');
        $I->test('tryCourse2StudyMetrics');
        $I->test('tryCourse1Environment');
        $I->test('tryNewPartner');
        $I->test('tryCourse3ActiveReading');
        $this->setLastLogin($I);
        $I->seeLink('setting goals');
        $I->click('//a[contains(.,"setting goals")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/goals"]');
    }

    public function tryInactivityInterleaving(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $I->test('tryNewCalculator');
        $I->test('tryCourse1Distractions');
        $I->test('tryCourse2StudyTests');
        $I->test('tryCourse2TestTaking');
        $I->test('tryCourse3SpacedRepetition');
        $I->test('tryCourse2StudyMetrics');
        $I->test('tryCourse1Environment');
        $I->test('tryNewPartner');
        $I->test('tryCourse3ActiveReading');
        $I->test('tryNewGoals');
        $this->setLastLogin($I);
        $I->seeLink('train your brain');
        $I->click('//a[contains(.,"train your brain")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/course/2/lesson/3"]');
    }

    public function tryInactivityGroupStudy(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $I->test('tryNewCalculator');
        $I->test('tryCourse1Distractions');
        $I->test('tryCourse2StudyTests');
        $I->test('tryCourse2TestTaking');
        $I->test('tryCourse3SpacedRepetition');
        $I->test('tryCourse2StudyMetrics');
        $I->test('tryCourse1Environment');
        $I->test('tryNewPartner');
        $I->test('tryCourse3ActiveReading');
        $I->test('tryNewGoals');
        $I->test('tryCourse2Interleaving');
        $this->setLastLogin($I);
        $I->seeLink('studying with groups');
        $I->click('//a[contains(.,"studying with groups")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/course/3/lesson/2"]');
    }

    public function tryInactivityTeaching(AcceptanceTester $I)
    {
        $I->test('tryGuestCheckout');
        $I->test('tryNewSchedule');
        $I->test('tryNewDeadlines');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryCourse1Procrastination');
        $I->test('tryNewCalculator');
        $I->test('tryCourse1Distractions');
        $I->test('tryCourse2StudyTests');
        $I->test('tryCourse2TestTaking');
        $I->test('tryCourse3SpacedRepetition');
        $I->test('tryCourse2StudyMetrics');
        $I->test('tryCourse1Environment');
        $I->test('tryNewPartner');
        $I->test('tryCourse3ActiveReading');
        $I->test('tryNewGoals');
        $I->test('tryCourse2Interleaving');
        $I->test('tryCourse3GroupStudy');
        $this->setLastLogin($I);
        $I->seeLink('Memorizing facts');
        $I->click('//a[contains(.,"Memorizing facts")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');
        $I->click('a[href*="/course/3/lesson/3"]');
    }

    public function tryInactivityNone(AcceptanceTester $I)
    {
        $I->test('tryInactivityTeaching');
        $I->amOnPage('/cron/emails');
    }
}