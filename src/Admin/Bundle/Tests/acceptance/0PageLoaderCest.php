<?php
namespace Admin\Bundle\Tests;

use Codeception\Module\Doctrine2;
use StudySauce\Bundle\Entity\Event;
use StudySauce\Bundle\Entity\User;
use WebDriver;
use WebDriverBy;
use WebDriverKeys;

/**
 * Class PageLoaderCest
 * @package StudySauce\Bundle\Tests
 * @backupGlobals false
 * @backupStaticAttributes false
 */
class PageLoaderCest
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
    public function tryLandingPages(AcceptanceTester $I)
    {
        $I->wantTo('see StudySauce in title');
        $I->seeAmOnUrl('/');
        $I->seeInTitle('StudySauce');
        $I->wait(3);
        $I->executeInSelenium(function (WebDriver $driver) {
            $driver->switchTo()->defaultContent();
            $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('iframe[src*="youtube.com"]')));
        });
        $I->wait(2);
        $I->click('[class*="play"]');
        $I->wait(2);
        $I->executeInSelenium(function (WebDriver $driver) {
            $driver->switchTo()->window($driver->getWindowHandle());
        });
        $I->click('a[href="#yt-pause"]');
        $I->wantTo('read the About us page');
        $I->seeLink('About us');
        $I->click('About us');
        $I->seeInCurrentUrl('/about');
        $I->wantTo('return to the homepage');
        $I->seeLink('Go home');
        $I->click('Go home');
        $I->wantTo('read the privacy policy');
        $I->seeLink('Privacy policy');
        $I->click('Privacy policy');
        $I->seeInCurrentUrl('/privacy');
        $I->wantTo('return to the homepage');
        $I->seeLink('Go home');
        $I->click('Go home');
        $I->wantTo('read terms of service');
        $I->seeLink('Terms of service');
        $I->click('Terms of service');
        $I->seeInCurrentUrl('/terms');
        $I->wantTo('return to the homepage');
        $I->seeLink('Go home');
        $I->click('Go home');
        $I->wantTo('read refund policy');
        $I->seeLink('Refund policy');
        $I->click('Refund policy');
        $I->seeInCurrentUrl('/refund');
        $I->wantTo('return to the homepage');
        $I->seeLink('Go home');
        $I->click('Go home');
        $I->test('tryContactUs');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryContactUs(AcceptanceTester $I)
    {
        $I->wantTo('contact the site\'s administrators');
        $I->seeAmOnUrl('/');
        $I->wait(5);
        $I->click('.footer a[href="#contact-support"]');
        $I->fillField('#contact-support input[name="your-name"]', 'test testers');
        $I->fillField('#contact-support input[name="your-email"]', 'tester@mailinator.com');
        $I->fillField('#contact-support textarea', 'I love this site.');
        $I->click('#contact-support [value="#submit-contact"]');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryTorchAndLaurel(AcceptanceTester $I)
    {
        $I->wantTo('checkout as a student');
        $I->amOnPage('/torchandlaurel');
        $I->seeLink('Get the Deal');
        $I->click('Get the Deal');
        $I->see('75% off');
        $I->test('tryGuestCheckout');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryBillMyParents(AcceptanceTester $I)
    {
        $I->wantTo('bill my parents');
        $I->seeAmOnUrl('/torchandlaurel');
        $I->wait(5);
        $I->click('a[href="#bill-parents"]');
        $I->fillField('#bill-parents .first-name input', 'Test');
        $I->fillField('#bill-parents .last-name input', 'Parent');
        $I->fillField('#bill-parents .email input', 'TestParent@mailinator.com');
        $I->fillField('#bill-parents .your-first input', 'Test');
        $I->fillField('#bill-parents .your-last input', 'Student');
        $I->fillField('#bill-parents .your-email input', 'TestStudent@mailinator.com');
        $I->click('#bill-parents [value="#submit-contact"]');
        $I->wait(10);

        $I->wantTo('check mailinator for bill my parents email');
        $I->amOnPage('/cron/emails');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('Test has asked for your help with school');
        $I->click('//a[contains(.,"Test has asked for your help with school")]');
        $I->executeInSelenium(function (WebDriver $driver) {
            $driver->switchTo()->defaultContent();
            $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('iframe[name="rendermail"]')));
        });

        $I->seeLink('Go to Study Sauce');
        $I->click('Go to Study Sauce');
        $I->wait(5);
        $I->executeInSelenium(function (WebDriver $webdriver) {
                $handles=$webdriver->getWindowHandles();
                $last_window = end($handles);
                $webdriver->switchTo()->window($last_window);
            });
        $I->seeInCurrentUrl('/torchandlaurelparents');
        $I->test('tryPrepayParent');
        $I->seeInCurrentUrl('/torchandlaurelregister');
        $I->fillField('.password input', 'password');
        $I->click('Register');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryPrepayParent(AcceptanceTester $I)
    {
        $I->wantTo('prepay for my student as a parent');
        $I->seeAmOnUrl('/parents');
        $I->wait(5);
        $I->seeLink('Tell your student');
        $I->click('Tell your student');
        $last = 'tester' . substr(md5(microtime()), -5);
        $I->fillField('#student-invite .first-name input', $last);
        $I->fillField('#student-invite .last-name input', 'test');
        $I->fillField('#student-invite .email input', $last . 'test' . '@mailinator.com');
        $I->fillField('#student-invite .your-first input', 'test');
        $I->fillField('#student-invite .your-last input', 'parent');
        $I->fillField('#student-invite .your-email input', 'testparent@mailinator.com');
        $I->click('#student-invite [value="#submit-contact"]');
        $I->wait(10);
        // previous invite will autofill checkout page otherwise it will fail
        $I->test('tryGuestCheckout');
        $I->seeInCurrentUrl('/thanks');
        $I->amOnPage('/cron/emails');

        // check mailinator for emails
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('test has prepaid for your study plan');
        $I->click('//a[contains(.,"test has prepaid for your study plan")]');
        $I->executeInSelenium(function (WebDriver $driver) {
                $driver->switchTo()->defaultContent();
                $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('iframe[name="rendermail"]')));
            });

        $I->seeLink('Go to Study Sauce');
        $I->click('//a[contains(.,"Go to Study Sauce")]');
        $I->wait(5);
        $I->executeInSelenium(function (WebDriver $webdriver) {
                $handles=$webdriver->getWindowHandles();
                $last_window = end($handles);
                $webdriver->switchTo()->window($last_window);
            });
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryGuestCheckout(AcceptanceTester $I)
    {
        $I->wantTo('complete the checkout');
        $I->seeAmOnUrl('/checkout');
        $I->fillField('input[name="first-name"]', 'test');
        $last = 'tester' . substr(md5(microtime()), -5);
        $I->fillField('input[name="last-name"]', $last);
        $I->fillField('input[name="email"]', 'test' . $last . '@mailinator.com');
        $I->fillField('input[name="password"]', 'password');
        $I->fillField('input[name="street1"]', '6934 E sandra ter');
        $I->fillField('input[name="city"]', 'scottsdale');
        $I->fillField('input[name="zip"]', '85254');
        $I->selectOption('select[name="state"]', 'Arizona');
        $I->selectOption('select[name="country"]', 'United States');
        $I->fillField('input[name="cc-number"]', '4007000000027');
        $I->selectOption('select[name="cc-month"]', '09');
        $I->selectOption('select[name="cc-year"]', '2019');
        $I->fillField('input[name="cc-ccv"]', '123');
        $I->seeLink('Complete order');
        $I->click('Complete order');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryNewGoals(AcceptanceTester $I) {

        $I->wantTo('complete goals');
        $I->seeAmOnUrl('/goals');
        $I->selectOption('.goal-row .behavior select', '15');
        $I->fillField('.goal-row .reward textarea', 'No studying on saturday');
        $I->selectOption('.goal-row + .goal-row select', 'B');
        $I->fillField('.goal-row + .goal-row textarea', 'One free spending');
        $I->selectOption('.goal-row + .goal-row + .goal-row select', '3.75');
        $I->fillField('.goal-row + .goal-row + .goal-row textarea', 'Special dinner out');
        $I->click('[value="#save-goal"]');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryNewSchedule(AcceptanceTester $I)
    {
        $I->wantTo('fill out my class schedule');
        $I->seeAmOnUrl('/schedule');
        $I->click('#schedule .selectize-control');
        $I->pressKey('#schedule .selectize-input input', WebDriverKeys::BACKSPACE);
        $I->fillField('#schedule .selectize-input input', 'Ariz');
        $I->wait(10);
        $I->click('//span[contains(.,"Arizona State University")]');
        $I->fillField('#schedule .class-row:nth-child(1) .class-name input', 'PHIL 101');
        $I->click('#schedule .class-row:nth-child(1) input[value="M"] + i');
        $I->click('#schedule .class-row:nth-child(1) input[value="W"] + i');
        $I->click('#schedule .class-row:nth-child(1) input[value="F"] + i');
        $I->fillField('#schedule .class-row:nth-child(1) .start-time input', '11');
        $I->fillField('#schedule .class-row:nth-child(1) .end-time input', '12');
        $I->pressKey('#schedule .class-row:nth-child(1) .end-time input', WebDriverKeys::TAB);
        $I->pressKey('#schedule .class-row:nth-child(1) .end-time input', WebDriverKeys::NUMPAD1);
        $I->pressKey('#schedule .class-row:nth-child(1) .end-time input', WebDriverKeys::NUMPAD9);
        $I->click('#schedule .class-row:nth-child(1) .start-date input');
        $I->click('.ui-datepicker-calendar tr:first-child td:first-child a');
        $I->click('#schedule .class-row:nth-child(1) .end-date input');
        $I->click('.ui-datepicker-calendar tr:last-child td:last-child a');
        $I->click('#schedule .highlighted-link [value="#save-class"]');
        $I->wait(10);
    }

    /**
     * @depends tryNewSchedule
     * @param AcceptanceTester $I
     */
    public function tryNewDeadlines(AcceptanceTester $I)
    {
        $I->wantTo('set up a deadline');
        $I->seeAmOnUrl('/deadlines');
        $I->selectOption('.deadline-row.edit .class-name select', 'PHIL 101');
        $I->fillField('.deadline-row.edit .assignment input', 'Exam 1');
        $I->click('.deadline-row.edit input[value="172800"] + i');
        $d = date_add(new \DateTime(), new \DateInterval('P8D'))->format('m/d/Y');
        $I->fillField('.deadline-row.edit .due-date input', $d);
        $I->wait(1);
        $I->click('.deadline-row.edit .percent input');
        $I->fillField('.deadline-row.edit .percent input', '10');
        $I->click('#deadlines .highlighted-link [value="#save-deadline"]');
        $I->wait(10);

        $I->click('#deadlines a[href="#add-deadline"]');

        $I->selectOption('.deadline-row.edit .class-name select', 'PHIL 101');
        $I->fillField('.deadline-row.edit .assignment input', 'Exam 2');
        $I->click('.deadline-row.edit input[value="172800"] + i');
        $d = date_add(new \DateTime(), new \DateInterval('P8D'))->format('m/d/Y');
        $I->fillField('.deadline-row.edit .due-date input', $d);
        $I->wait(1);
        $I->click('.deadline-row.edit .percent input');
        $I->fillField('.deadline-row.edit .percent input', '10');
        $I->click('#deadlines .highlighted-link [value="#save-deadline"]');
        $I->wait(10);

        $I->click('//div[contains(@class,"deadline-row") and .//input[contains(@value,"Exam 2")]]//a[contains(@href,"remove-deadline")]');
        $I->wait(10);
    }

    /**
     * @depends tryNewSchedule
     * @param AcceptanceTester $I
     */
    public function tryNewCheckin (AcceptanceTester $I)
    {
        $I->wantTo('checkin for the first time');
        $I->seeAmOnUrl('/checkin');
        $I->wait(5);
        $I->click('#checkin .classes a:first-child');
        $I->seeLink('Continue to session');
        $I->click('Continue to session');
        $I->wait(20);
        $I->click('#checkin .classes a:first-child');
        $I->click('#timer-expire a[href="#close"]');
        $I->wait(10);
    }

    /**
     * @depends tryNewSchedule
     * @param AcceptanceTester $I
     */
    public function tryNewMetrics(AcceptanceTester $I) {
        $I->wantTo('enter a manually study session');
        $I->seeAmOnUrl('/metrics');
        $empty = $I->grabAttributeFrom('#metrics', 'class');
        if(strpos($empty, 'demo')) {
            $I->test('tryNewCheckin');
        }
        $I->click('#metrics a[href="#add-study-hours"]');
        $I->selectOption('#add-study-hours .class-name select', 'PHIL 101');
        $I->click('#add-study-hours .date input');
        $I->click('.ui-datepicker-calendar td:not(.ui-datepicker-unselectable) a');
        $I->selectOption('#add-study-hours .time select', '45');
        $I->click('#add-study-hours [value="#submit-checkin"]');
        $I->wait(5);
        $I->seeElement('//div[contains(.,"45 minutes")]');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryNewPartner(AcceptanceTester $I)
    {
        $I->wantTo('invite a new accountability partner');
        $I->seeAmOnUrl('/partner');
        $I->fillField('#partner .first-name input', 'Test');
        $I->fillField('#partner .last-name input', 'Partner');
        $I->fillField('#partner .email input', 'TestPartner@mailinator.com');
        $I->click('[value="#partner-save"]');
        $I->wait(15);
    }

    /**
     * @param AcceptanceTester $I
     * @depends tryGuestCheckout
     */
    public function tryDetailedSchedule(AcceptanceTester $I)
    {
        $I->wantTo('invite a new accountability partner');
        $I->seeAmOnUrl('/schedule');
        $I->click('.selectize-input');
        $I->pressKey('.selectize-input input', WebDriverKeys::BACKSPACE);
        $I->fillField('.selectize-input input', 'Ariz');
        $I->wait(10);
        $I->click('//span[contains(.,"Arizona State University")]');

        // add one class
        $I->fillField('.class-row:nth-child(1) .class-name input', 'PHIL 101');
        $I->click('.class-row:nth-child(1) input[value="M"] + i');
        $I->click('.class-row:nth-child(1) input[value="W"] + i');
        $I->click('.class-row:nth-child(1) input[value="F"] + i');
        $I->fillField('.class-row:nth-child(1) .start-time input', '11');
        $I->fillField('.class-row:nth-child(1) .end-time input', '12');
        $I->click('.class-row:nth-child(1) .start-date input');
        $I->click('.ui-datepicker-calendar tr:first-child td:first-child a');
        $I->click('.class-row:nth-child(1) .end-date input');
        $I->click('.ui-datepicker-calendar tr:last-child td:last-child a');

        // add a second class
        $I->fillField('.class-row:nth-child(2) .class-name input', 'CALC 102');
        $I->click('.class-row:nth-child(2) input[value="M"] + i');
        $I->click('.class-row:nth-child(2) input[value="W"] + i');
        $I->click('.class-row:nth-child(2) input[value="F"] + i');
        $I->fillField('.class-row:nth-child(2) .start-time input', '11');
        $I->fillField('.class-row:nth-child(2) .end-time input', '12');

        $I->click('#schedule .highlighted-link [value="#save-class"]');
        $I->see('Cannot overlap'); // should fail if hidden
        // fix time
        $I->fillField('.class-row:nth-child(2) .start-time input', '9');
        $I->fillField('.class-row:nth-child(2) .end-time input', '10');

        // add a third class
        $I->fillField('.class-row:nth-child(3) .class-name input', 'GEO 102');
        $I->click('.class-row:nth-child(3) input[value="M"] + i');
        $I->click('.class-row:nth-child(3) input[value="W"] + i');
        $I->click('.class-row:nth-child(3) input[value="F"] + i');
        $I->fillField('.class-row:nth-child(3) .start-time input', '9');
        $I->fillField('.class-row:nth-child(3) .end-time input', '10');

        $I->click('.class-row:nth-child(3) .end-date input');
        $I->click('.ui-datepicker .ui-datepicker-next');
        $I->click('.ui-datepicker .ui-datepicker-next');
        $I->click('.ui-datepicker-calendar tr:last-child td:last-child a');
        $I->click('.class-row:nth-child(3) .start-date input');
        $I->click('.ui-datepicker .ui-datepicker-next');
        $I->click('.ui-datepicker .ui-datepicker-next');
        $I->click('.ui-datepicker-calendar tr:first-child td:first-child a');
        $I->click('#schedule .highlighted-link [value="#save-class"]');
        $I->wait(10);


        // add a new term
        $I->click('#schedule a[href="#manage-terms"]');
        $I->selectOption('#manage-terms select', '1/2014');
        $I->click('#manage-terms a[href="#create-schedule"]');

        // enter new schedule
        $I->click('.selectize-input');
        $I->pressKey('.selectize-input input', WebDriverKeys::BACKSPACE);
        $I->fillField('.selectize-input input', 'Ariz');
        $I->wait(10);
        $I->click('//span[contains(.,"Arizona State University")]');

        $I->fillField('.class-row:nth-child(1) .class-name input', 'MAT 202');
        $I->click('.class-row:nth-child(1) input[value="M"] + i');
        $I->click('.class-row:nth-child(1) input[value="W"] + i');
        $I->click('.class-row:nth-child(1) input[value="F"] + i');
        $I->fillField('.class-row:nth-child(1) .start-time input', '11');
        $I->fillField('.class-row:nth-child(1) .end-time input', '12');
        $I->click('.class-row:nth-child(1) .start-date input');
        $I->click('.ui-datepicker-calendar tr:first-child td:first-child a');
        $I->click('.class-row:nth-child(1) .end-date input');
        $I->click('.ui-datepicker-calendar tr:last-child td:last-child a');

        $I->click('#schedule .highlighted-link [value="#save-class"]');
        $I->wait(10);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function trySignup(AcceptanceTester $I)
    {
        $I->wantTo('sign up for study sauce');
        $I->seeAmOnUrl('/signup');
        $I->fillField('input[name="organization"]', 'Study Sauce');
        $I->fillField('input[name="first-name"]', 'test');
        $I->fillField('input[name="title"]', 'Mr');
        $last = 'tester' . substr(md5(microtime()), -5);
        $I->fillField('input[name="email"]', 'test' . $last . '@mailinator.com');
        $I->fillField('input[name="phone"]', '4804660856');
        $I->fillField('input[name="street1"]', '6934 E sandra ter');
        $I->fillField('input[name="city"]', 'scottsdale');
        $I->fillField('input[name="zip"]', '85254');
        $I->selectOption('select[name="state"]', 'Arizona');
        $I->selectOption('select[name="country"]', 'United States');
        $I->fillField('input[name="students"]', '10');
        $I->selectOption('.payment select', 'Credit card');
        $I->seeLink('Save');
        $I->click('Save');
        $I->wait(10);

        $I->wantTo('visit mailinator and check for organization email');
        $I->amOnPage('/cron/emails');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('Contact Us');
        $I->click('//a[contains(.,"Contact Us")]');
        $I->executeInSelenium(function (WebDriver $driver) {
            $driver->switchTo()->defaultContent();
            $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('iframe[name="rendermail"]')));
        });
        $I->see('Organization:');
    }

    /**
     * @depends tryGuestCheckout
     * @param AcceptanceTester $I
     */
    public function trySocialLogin(AcceptanceTester $I)
    {
        $I->seeAmOnUrl('/course/1/lesson/1/step');
        $I->click('a[href*="/google"]');
        $I->fillField('input[name="Email"]', 'brian@studysauce.com');
        $I->click('Next');
        $I->wait(1);
        $I->fillField('input[name="Passwd"]', 'Da1ddy23');
        $I->click('Sign in');
        $I->seeAmOnUrl('/course/1/lesson/1/step');

        // log out and log back in using social login
        $I->click('a[href*="/logout"]');
        $I->click('a[href*="/login"]');
        $I->click('a[href*="/google"]');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryNotesCloseFirstTime(AcceptanceTester $I) {
        $I->seeAmOnUrl('/notes');
        $I->click('#notes-connect a[href="#close"]');
    }

    /**
     * @depends tryNotesCloseFirstTime
     * @param AcceptanceTester $I
     */
    public function tryNewNote(AcceptanceTester $I)
    {
        $I->wantTo('create a new note regardless of connectivity');
        $I->seeAmOnUrl('/notes');
        $I->click('study note');
        $I->selectOption('#notes select[name="notebook"]', 'PHIL 101');
        $I->fillField('#notes .input.title input', 'This is a new note ' . date('Y-m-d'));
        $time = '' . time();
        for($i = 0; $i < strlen($time); $i++)
        {
            $key = constant('WebDriverKeys::NUMPAD' . substr($time, $i, 1));
            $I->pressKey('#editor1', $key);
        }
        $I->click('#notes a[href="#save-note"]');
        $I->wait(10);
        $I->fillField('#notes [name="search"]', date('Y-m-d'));
        $I->click('#notes [value="search"]');
        $I->wait(5);
        $I->see($time);
        $I->fillField('#notes [name="search"]', '');
        $I->click('//div[@class="summary" and contains(.,"' . $time . '")]');
        // change tags
        $I->executeJS('window.scrollTo(0,0);');
        $I->click('#notes .selectize-input');
        $I->pressKey('#notes .selectize-input input', WebDriverKeys::BACKSPACE);
        $I->fillField('#notes .selectize-input input', 'StudySauce123');
        $I->click('#notes .selectize-dropdown-content .create');
        $I->click('#notes a[href="#save-note"]');
        $I->wait(10);
    }

    /**
     * @depends tryNotesCloseFirstTime
     * @param AcceptanceTester $I
     */
    public function tryEvernoteConnect(AcceptanceTester $I) {
        $I->wantTo('log in to evernote');
        $I->seeAmOnUrl('/notes');
        $I->click('#notes a[href*="evernote"]');
        $I->fillField('input[name="username"]', 'brian@studysauce.com');
        $I->fillField('input[name="password"]', 'Da1ddy23');
        $I->click('[type="submit"]');
        $I->click('authorize');
        $I->seeInCurrentUrl('/notes');
    }

    /**
     * @depemds tryNewSchedule
     * @depends tryEvernoteConnect
     * @depends tryNotesCloseFirstTime
     * @param AcceptanceTester $I
     */
    public function tryNewEvernote(AcceptanceTester $I)
    {
        $I->wantTo('run the same tests then check evernote sync');
        $I->seeAmOnUrl('/notes');
        $I->click('study note');
        $I->selectOption('#notes select[name="notebook"]', 'Add notebook');
        $I->fillField('#add-notebook input', 'Test notebook');
        $I->click('#add-notebook button');
        $I->wait(6);
        $I->selectOption('#notes select[name="notebook"]', 'Test notebook');
        $I->fillField('#notes .input.title input', 'This is a new note ' . date('Y-m-d'));
        $time = '' . time();
        for($i = 0; $i < strlen($time); $i++)
        {
            $key = constant('WebDriverKeys::NUMPAD' . substr($time, $i, 1));
            $I->pressKey('#editor1', $key);
        }
        $I->click('#notes a[href="#save-note"]');
        $I->wait(10);
        $I->fillField('#notes [name="search"]', date('Y-m-d'));
        $I->click('#notes [value="search"]');
        $I->wait(5);
        $I->see($time);
        $I->fillField('#notes [name="search"]', '');
        $I->click('//div[@class="summary" and contains(.,"' . $time . '")]');
        // change tags
        $I->executeJS('window.scrollTo(0,0);');
        $I->click('#notes .selectize-input');
        $I->pressKey('#notes .selectize-input input', WebDriverKeys::BACKSPACE);
        $I->fillField('#notes .selectize-input input', 'StudySauce123');
        $I->click('#notes .selectize-dropdown-content .create');
        $I->click('#notes a[href="#save-note"]');
        $I->wait(10);
        // check evernote to make sure changes were synced
        $I->amOnPage('/cron/sync');
        $I->amOnUrl('https://sandbox.evernote.com');
        $I->wait(1);
        $I->click('#gwt-debug-DialogB-skip');
        $I->click('#gwt-debug-Sidebar-notebooksButton-container');
        $I->click('//div[contains(@class,"qa-notebookWidget") and contains(.,"Test notebook")]');
        $I->wait(2);
        $I->click('//div[contains(@class,"qa-noteWidget") and contains(.,"' . $time . '")]//div[contains(@class,"qa-title")]');
        $I->see('StudySauce123');

        // delete the evernote notebook
        $I->click('#gwt-debug-Sidebar-notebooksButton-container');
        $I->moveMouseOver('//div[contains(@class,"qa-notebookWidget") and contains(.,"Test notebook")]');
        $I->click('//div[contains(@class,"qa-notebookWidget") and contains(.,"Test notebook")]//div[contains(@class,"qa-deleteButton")]');
        $I->click('#gwt-debug-ConfirmationDialog-confirm');
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/cron/sync');
        $I->amOnPage('/notes');
    }

    /**
     * @depends tryGuestCheckout
     * @param AcceptanceTester $I
     */
    public function tryDetailedNotes(AcceptanceTester $I)
    {
        $I->seeAmOnUrl('/notes');
        $I->test('tryNewSchedule');
        $I->test('tryNotesCloseFirstTime');
        $I->test('tryNewNote');
        $I->test('tryEvernoteConnect');
        $I->test('tryNewEvernote');
        $I->wait(5);
    }

    /**
     * @depends tryGuestCheckout
     * @depends tryNewSchedule
     * @param AcceptanceTester $I
     */
    public function tryNewCalculator(AcceptanceTester $I)
    {
        $I->seeAmOnUrl('/calculator');
        $I->fillField('#calculator .hours input', '3');
        $I->fillField('#calculator .edit .assignment input', 'Exam 1');
        $I->fillField('#calculator .edit .percent input', '50');
        $I->fillField('#calculator .edit .score input', '90');
        $I->click('#calculator [value="#save-grades"]');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryNewPlan(AcceptanceTester $I)
    {
        // complete the setup wizard
        $I->wantTo('complete the plan setup');
        $I->seeAmOnUrl('/plan');
        $empty = $I->grabAttributeFrom('#plan', 'class');
        if(strpos($empty, 'empty-schedule')) {
            $I->test('tryNewSchedule');
        }
        $plan0 = $I->grabAttributeFrom('#plan-step-0 .highlighted-link', 'class');
        if(strpos($plan0, 'invalid')) {
            $I->test('tryCourse2StudyPlan');
        }
        $I->click('Get started');
        $I->click('#plan-step-1 input[value="easy"] + i');
        $I->click('#plan-step-1 [type="submit"]');
        $I->wait(10);

        $I->click('Add pre-work');
        $start = (new \DateTime('this Monday 9:00'))->format('r');
        $end = (new \DateTime('this Monday 10:00'))->format('r');
        $newEvent = <<< EOJS
        var event = $('#external-events .event-type-p:nth-child(2)').data('event');
        event.start = new Date('$start');
        event.end = new Date('$end');
        $('#calendar').fullCalendar('renderEvent', event, true)
EOJS;
        $I->executeJS($newEvent);
        $start = (new \DateTime('this Wednesday 9:00'))->format('r');
        $end = (new \DateTime('this Wednesday 10:00'))->format('r');
        $newEvent = <<< EOJS
        var event = $('#external-events .event-type-p:nth-child(3)').data('event');
        event.start = new Date('$start');
        event.end = new Date('$end');
        $('#calendar').fullCalendar('renderEvent', event, true)
EOJS;
        $I->executeJS($newEvent);
        $start = (new \DateTime('this Friday 9:00'))->format('r');
        $end = (new \DateTime('this Friday 10:00'))->format('r');
        $newEvent = <<< EOJS
        var event = $('#external-events .event-type-p:nth-child(4)').data('event');
        event.start = new Date('$start');
        event.end = new Date('$end');
        $('#calendar').fullCalendar('renderEvent', event, true)
EOJS;
        $I->executeJS($newEvent);
        $I->click('#external-events a[href="#save-plan"]');
        $I->wait(10);

        $I->click('#plan-step-2-2 [href="#add-spaced-repetition"]');
        $start = (new \DateTime('this Monday 14:00'))->format('r');
        $end = (new \DateTime('this Monday 15:00'))->format('r');
        $newEvent = <<< EOJS
        var event = $('#external-events .event-type-sr:nth-child(2)').data('event');
        event.start = new Date('$start');
        event.end = new Date('$end');
        $('#calendar').fullCalendar('renderEvent', event, true)
EOJS;
        $I->executeJS($newEvent);
        $start = (new \DateTime('this Wednesday 14:00'))->format('r');
        $end = (new \DateTime('this Wednesday 15:00'))->format('r');
        $newEvent = <<< EOJS
        var event = $('#external-events .event-type-sr:nth-child(3)').data('event');
        event.start = new Date('$start');
        event.end = new Date('$end');
        $('#calendar').fullCalendar('renderEvent', event, true)
EOJS;
        $I->executeJS($newEvent);
        $start = (new \DateTime('this Friday 14:00'))->format('r');
        $end = (new \DateTime('this Friday 15:00'))->format('r');
        $newEvent = <<< EOJS
        var event = $('#external-events .event-type-sr:nth-child(4)').data('event');
        event.start = new Date('$start');
        event.end = new Date('$end');
        $('#calendar').fullCalendar('renderEvent', event, true)
EOJS;
        $I->executeJS($newEvent);
        $I->click('#external-events a[href="#save-plan"]');
        $I->wait(10);

        $I->click('Add free study');
        $start = (new \DateTime('this Sunday 12:00'))->format('r');
        $end = (new \DateTime('this Sunday 13:00'))->format('r');
        $newEvent = <<< EOJS
        var event = $('#external-events .event-type-f:nth-child(3)').data('event');
        event.start = new Date('$start');
        event.end = new Date('$end');
        $('#calendar').fullCalendar('renderEvent', event, true)
EOJS;
        $I->executeJS($newEvent);
        $start = (new \DateTime('this Saturday 12:00'))->format('r');
        $end = (new \DateTime('this Saturday 13:00'))->format('r');
        $newEvent = <<< EOJS
        var event = $('#external-events .event-type-f:nth-child(4)').data('event');
        event.start = new Date('$start');
        event.end = new Date('$end');
        $('#calendar').fullCalendar('renderEvent', event, true)
EOJS;
        $I->executeJS($newEvent);
        $I->click('#external-events a[href="#save-plan"]');
        $I->wait(10);

        $I->click('#plan-step-3 [type="submit"]');
        $I->wait(10);
        $I->selectOption('#plan-step-4 select', 'memorization');
        $I->click('#plan-step-4 [type="submit"]');
        $I->wait(10);
        $I->click('Make final adjustments');
        $I->doubleClick('#calendar .event-type-p');
        $I->fillField('#edit-event .location input', 'The Library');
        $I->click('#edit-event [type="submit"]');
        $I->wait(1);
        $I->click('Done');
        $I->wait(10);
        $I->executeJS('$(\'#plan-step-6 [href*="download"]\').trigger(\'click\');');
        $I->wait(1);
        $I->click('Go to study plan');
        $I->wait(1);
    }

    public function tryNewStudySession(AcceptanceTester $I)
    {
        $I->wantTo('start a study session');
        $I->click('.fc-agendaWeek-button');
        $I->click('#calendar .event-type-p');
        $I->wait(1);
        $I->click('#plan a.checkin');
        $I->click('Continue to session');
        $I->wait(20);
        $I->click('new note');
        $I->fillField('#notes .input.title input', 'This is a new note ' . date('Y-m-d'));
        $time = '' . time();
        for($i = 0; $i < strlen($time); $i++)
        {
            $key = constant('WebDriverKeys::NUMPAD' . substr($time, $i, 1));
            $I->pressKey('#editor1', $key);
        }
        $I->click('#notes a[href="#save-note"]');
        $I->wait(10);
        $I->click('#right-panel a[href="#expand"] span');
        $I->click('#right-panel a[href*="/plan"]');
        $I->wait(1);
        $I->click('#plan a.checkin');
        $I->click('#timer-expire a[href="#close"]');

    }

    public function tryGoogleLogin(AcceptanceTester $I)
    {
        $I->wantTo('connect to google');
        $I->seeAmOnUrl('/account');
        $I->click('#account a[href*="gcal"]');
        $I->fillField('input[id="Email"]', 'brian@studysauce.com');
        $I->click('Next');
        $I->fillField('input[id="Passwd"]', 'Da1ddy23');
        $I->click('Sign in');
        $I->click('Allow');
    }

    private static $googleI = 0;
    /**
     * @depends tryGuestCheckout
     * @param AcceptanceTester $I
     */
    public function tryGoogleSync(AcceptanceTester $I)
    {
        $I->wantTo('connect to Google Calendar');
        $I->amOnPage('/plan');
        $I->seeElement('#plan-step-6-3');
        $I->click('#plan-step-6-3 .highlighted-link a');
        $I->wait(1);
        $I->doubleClick('#calendar .fc-agendaWeek-button');
        $I->wait(2);
        $I->doubleClick('#calendar td:nth-child(3) .fc-event-container .event-type-p');
        $I->selectOption('#edit-event .reminder select', 15);
        $I->click('#edit-event button');
        $I->wait(1);
        $I->click('#plan-drag [href="#all"]');
        $I->wait(5);
        $I->amOnPage('/cron/sync');
        $I->wantTo('check if schedule has synced');
        $I->amOnUrl('https://www.google.com/calendar/b/' . self::$googleI . '/render');
        $I->click('//span[contains(.,"PHIL 101: Pre-work")]');
        // test changes in gcal syncing to studysauce
        $I->seeInField('span[title="Reminder time"] input', 15);
        $I->fillField('input[title="From time"]', '11');
        $I->fillField('input[placeholder="Enter a location"]', 'the library');
        $I->fillField('span[title="Reminder time"] input', 30);
        $I->click('//div[contains(.,"Save") and contains(@class,"goog-imageless-button-content")]');
        $I->wait(1);
        $I->click('//div[contains(.,"All events") and contains(@class,"goog-imageless-button-content")]');
        $I->wait(3);
        $I->amOnUrl('https://' . $_SERVER['HTTP_HOST'] . '/cron/sync');
        $I->amOnPage('/plan');
        // check studysauce for the changes we just made
        $I->wait(1);
        $I->doubleClick('#calendar .fc-agendaWeek-button');
        $I->wait(2);
        $I->doubleClick('#calendar td:nth-child(3) .fc-event-container .event-type-p');
        $I->seeInField('#edit-event .start-time input', '11:00 AM');
        $I->seeInField('#edit-event .location input', 'the library');
        $I->seeOptionIsSelected('#edit-event .reminder select', '30');
    }

    public function tryGoogleReconnect(AcceptanceTester $I)
    {
        $I->seeAmOnUrl('/account');
        $I->click('#account a[href="#remove-gcal"]');
        $I->wait(5);
        $I->click('#account a[href*="gcal"]');
        $I->click('Deny');
        $I->click('#account a[href*="gcal"]');
        $I->click('brian@studysauce.com');
        $I->click('Add account');
        $I->fillField('input[id="Email"]', 'bjcullinan@gmail.com');
        $I->click('Next');
        $I->fillField('input[id="Passwd"]', '%rm0#B&Z59$*LOr7');
        $I->click('Sign in');
        $I->click('Allow');
        self::$googleI = 1;
        $I->test('tryGoogleSync');
    }

    public function tryPlanDuplicates(AcceptanceTester $I)
    {
        $I->seeAmOnUrl('/account');
        $I->wait(1);
        $email = $I->grabValueFrom('#account .email input');
        /** @var User $user */
        Doctrine2::$em->clear();
        $dupes = Doctrine2::$em->getRepository('StudySauceBundle:Event')->createQueryBuilder('e')
            ->select(['e'])
            ->leftJoin('e.schedule', 's')
            ->leftJoin('s.user', 'u')
            ->where('u.email LIKE :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getResult();
        $events = [];
        foreach($dupes as $e) {
            /** @var Event $e */
            $key = (!empty($e->getCourse()) ? $e->getCourse()->getId() : '')
                . $e->getSchedule()->getId()
                . $e->getType()
                . $e->getStart()->format('H:i:s')
                . (empty($e->getRecurrence()) ? '' : $e->getRecurrence()[0]);
            $I->assertTrue(!in_array($key, $events), 'The event is not duplicated');
            $events[] = $key;
        }
    }

    /**
     * @depends tryGuestCheckout
     * @param AcceptanceTester $I
     */
    public function tryDetailedPlan(AcceptanceTester $I)
    {
        $I->wantTo('test entire plan');
        $I->seeAmOnUrl('/plan');
        $I->click('Edit schedule');
        $I->wait(10);
        $I->seeAmOnUrl('/schedule');
        $I->test('tryNewSchedule');
        $I->test('tryNewPlan');
        $I->test('tryNewStudySession');
        $I->test('tryGoogleLogin');
        $I->test('tryGoogleSync');
        $I->test('tryGoogleReconnect');
        $I->test('tryPlanDuplicates');
    }

    /**
     * @depends tryGuestCheckout
     * @param AcceptanceTester $I
     */
    public function trySessionExpire(AcceptanceTester $I) {
        $I->seeAmOnUrl('/schedule');
        for($i = 0; $i < 35; $i++) {
            $I->wait(60);
        }
        $I->test('tryNewSchedule');
        $I->test('tryNewPlan');
    }

}


