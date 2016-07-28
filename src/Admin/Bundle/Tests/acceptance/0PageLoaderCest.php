<?php
namespace Admin\Bundle\Tests;

use Codeception\Module\Doctrine2;
use Facebook\WebDriver\Remote\RemoteWebDriver;
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
        $I->executeInSelenium(function (RemoteWebDriver $driver) {
            $driver->switchTo()->defaultContent();
            $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('iframe[src*="youtube.com"]')));
        });
        $I->wait(2);
        $I->click('[class*="play"]');
        $I->wait(2);
        $I->executeInSelenium(function (RemoteWebDriver $driver) {
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
    public function tryGuestCheckout(AcceptanceTester $I)
    {
        $I->wantTo('complete the checkout');
        $I->seeAmOnPage('/cart');

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
        $I->fillField('#inboxfield', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('Contact Us');
        $I->click('//a[contains(.,"Contact Us")]');
        $I->executeInSelenium(function (RemoteWebDriver $driver) {
            $driver->switchTo()->defaultContent();
            $driver->switchTo()->frame($driver->findElement(WebDriverBy::cssSelector('iframe[name="rendermail"]')));
        });
        $I->see('Organization:');
    }


}


