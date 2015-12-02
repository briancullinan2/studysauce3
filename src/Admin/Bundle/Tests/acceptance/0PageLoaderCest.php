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
        $I->seeAmOnPage('/');
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
        $I->seeAmOnPage('/');
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
        $I->seeAmOnPage('/torchandlaurel');
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
        $I->seeAmOnPage('/parents');
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
        $I->seeAmOnPage('/checkout');
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
    public function trySignup(AcceptanceTester $I)
    {
        $I->wantTo('sign up for study sauce');
        $I->seeAmOnPage('/signup');
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
        $I->seeAmOnPage('/course/1/lesson/1/step');
        $I->click('a[href*="/google"]');
        $I->fillField('input[name="Email"]', 'brian@studysauce.com');
        $I->click('Next');
        $I->wait(1);
        $I->fillField('input[name="Passwd"]', 'Da1ddy23');
        $I->click('Sign in');
        $I->seeAmOnPage('/course/1/lesson/1/step');

        // log out and log back in using social login
        $I->click('a[href*="/logout"]');
        $I->click('a[href*="/login"]');
        $I->click('a[href*="/google"]');
    }

    public function tryGoogleLogin(AcceptanceTester $I)
    {
        $I->wantTo('connect to google');
        $I->seeAmOnPage('/account');
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
        $I->seeAmOnPage('/account');
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

}


