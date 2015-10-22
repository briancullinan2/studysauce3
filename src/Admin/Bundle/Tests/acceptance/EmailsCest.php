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

}