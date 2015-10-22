<?php
namespace Admin\Bundle\Tests;

use Codeception\Module\Doctrine2;
use WebDriver;
use WebDriverBy;
use WebDriverKeys;

/**
 * Class PageLoaderCest
 * @package StudySauce\Bundle\Tests
 * @backupGlobals false
 * @backupStaticAttributes false
 */
class AdviserCest
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
    public function tryAdviserLogin(AcceptanceTester $I)
    {
        $I->wantTo('Login as the adviser account brian@studysauce.com');
        $I->amOnPage('/login');
        $I->fillField('#login .email input', 'brian@studysauce.com');
        $I->fillField('#login .password input', 'password');
        $I->click('#login [value="#user-login"]');
        $I->wait(5);
    }

    /**
     * @depends tryAdviserLogin
     * @param AcceptanceTester $I
     */
    public function tryGroupInvite(AcceptanceTester $I)
    {
        $I->wantTo('Invite a student to join study sauce');
        $I->seeAmOnUrl('/userlist');
        $I->click('#right-panel a[href="#expand"]');
        $I->click('User Import');
        $I->wait(5);
        $last = 'tester' . substr(md5(microtime()), -5);
        $I->fillField('#import .edit .first-name input', $last);
        $I->fillField('#import .edit .last-name input', 'last' . $last);
        $I->fillField('#import .edit .email input', 'firstlast' . $last . '@mailinator.com');
        $I->click('#import [href="#save-group"]');
        $I->wait(5);

        $I->wantTo('Try to register as a new student without clicking the email');
        $I->click('a[href*="/logout"]');
        $I->seeAmOnUrl('/register');
        $I->fillField('#register .first-name input', $last);
        $I->fillField('#register .last-name input', 'last' . $last);
        $I->fillField('#register .email input', 'firstlast' . $last . '@mailinator.com');
        $I->fillField('#register .password input', 'password');
        $I->click('[value="#user-register"]');
        $I->wait(5);

        $I->wantTo('See the student\'s entries from the adviser view');
        $I->click('a[href*="/logout"]');
        $I->click('a[href*="/login"]');
        $I->test('tryAdviserLogin');
        // search for student name
        $I->fillField('[name="search"]', $last);
        $I->see('last' . $last); // check for student name in user list
        $I->click('last' . $last); // load the student
        $I->see('last' . $last); // check the name in the corner of the tab

        $I->wantTo('Invite another student to join study sauce');
        $I->click('#right-panel a[href="#expand"]');
        $I->click('User Import');
        $I->wait(5);
        $last = 'tester' . substr(md5(microtime()), -5);
        $I->fillField('#import .edit .first-name input', $last);
        $I->fillField('#import .edit .last-name input', 'last' . $last);
        $I->fillField('#import .edit .email input', 'firstlast' . $last . '@mailinator.com');
        $I->click('#import [href="#save-group"]');
        $I->wait(5);
        $I->amOnPage('/cron/emails');

        // change the created day back 8 days so the invite reminder is sent
        Doctrine2::$em->clear();
        $invite = Doctrine2::$em->getRepository('StudySauceBundle:GroupInvite')->findOneBy(['email' => 'firstlast' . $last . '@mailinator.com']);
        $invite->setCreated(date_sub(new \DateTime(), new \DateInterval('P3D')));
        Doctrine2::$em->merge($invite);
        Doctrine2::$em->flush();

        $I->wantTo('Register as a new student with a different email address using the invite reminder email');
        $I->amOnPage('/cron/emails');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('Invitation to Study Sauce!');
        $I->click('//a[contains(.,"Your invitation to join Study Sauce is still pending")]');
        $I->wait(1);
        $I->switchToIFrame('rendermail');

        $I->seeLink('Go to Study Sauce');
        $I->click('Go to Study Sauce');
        $I->wait(5);
        $I->executeInSelenium(function (WebDriver $webdriver) {
            $handles=$webdriver->getWindowHandles();
            $last_window = end($handles);
            $webdriver->switchTo()->window($last_window);
        });
        $I->seeInCurrentUrl('/register');
        $I->fillField('#register .email input', 'firstlast' . $last . '2@mailinator.com');
        $I->fillField('#register .password input', 'password');
        $I->click('[value="#user-register"]');
        $I->wait(5);
        $I->test('tryCourse1Introduction');

        $I->wantTo('See the student\'s entries from the adviser view');
        $I->click('a[href*="/logout"]');
        $I->click('a[href*="/login"]');
        $I->test('tryAdviserLogin');
        $I->see('last' . $last); // check for student name in user list
        $I->click('last' . $last); // load the student
        $I->see('last' . $last); // check the name in the corner of the tab
        $I->click('//h3[contains(.,"Course 1")]');
        $I->click('//h4[contains(.,"Introduction")]');
        $I->seeCheckboxIsChecked('input[value="college-senior"]');

        $I->wantTo('Register as a new student before my adviser has a chance to invite me');
        $I->click('a[href*="/logout"]');
        $I->seeAmOnUrl('/register');
        $last = 'tester' . substr(md5(microtime()), -5);
        $I->fillField('#register .first-name input', $last);
        $I->fillField('#register .last-name input', 'last' . $last);
        $I->fillField('#register .email input', 'firstlast' . $last . '@mailinator.com');
        $I->fillField('#register .password input', 'password');
        $I->click('[value="#user-register"]');
        $I->wait(5);

        $I->wantTo('invite a user that already jump the gun and signed up early');
        $I->amOnPage('/logout');
        $I->click('a[href*="/login"]');
        $I->test('tryAdviserLogin');
        $I->click('#right-panel a[href="#expand"]');
        $I->click('User Import');
        $I->wait(5);
        $I->fillField('#import .edit .first-name input', $last);
        $I->fillField('#import .edit .last-name input', 'last' . $last);
        $I->fillField('#import .edit .email input', 'firstlast' . $last . '@mailinator.com');
        $I->click('#import [href="#save-group"]');
        $I->wait(5);
        $I->amOnPage('/home');
        $I->see('last' . $last); // check for student name in user list
        $I->click('last' . $last); // load the student
        $I->see('last' . $last); // check the name in the corner of the tab

    }

    /**
     * @depends tryAdviserLogin
     * @depends tryGroupInvite
     * @param AcceptanceTester $I
     */
    public function tryGroupDeadlines(AcceptanceTester $I)
    {
        $I->wantTo('set up course deadlines for my students');
        $I->seeAmOnUrl('/userlist');
        $I->click('#right-panel a[href="#expand"]');
        $I->click('Deadlines');
        $I->wait(5);

        if($I->grabValueFrom('.deadline-row .assignment input') == 'Course completion') {
            $I->click(
                '//div[contains(@class,"deadline-row") and .//input[contains(@value,"Course completion")]]//a[contains(@href,"remove-deadline")]'
            );
            $I->wait(5);
        }

        $I->selectOption('.deadline-row.edit .class-name select', 'Course completion');
        $I->click('.deadline-row.edit input[value="86400"] + i');
        $I->click('.deadline-row.edit input[value="172800"] + i');
        $I->click('.deadline-row.edit input[value="345600"] + i');
        $I->click('.deadline-row.edit input[value="604800"] + i');
        $I->fillField('.deadline-row.edit .due-date input', date_add(new \DateTime(), new \DateInterval('P8D'))->format('m/d/Y'));
        $I->click('#deadlines .highlighted-link [value="#save-deadline"]');
        $I->wait(10);

        $I->wantTo('check mailinator to see if the students received the email');
        $I->amOnPage('/cron/emails');
        $I->amOnUrl('http://mailinator.com');
        $I->fillField('.input-append input', 'studymarketing');
        $I->click('.input-append btn');
        $I->waitForText('a minute ago', 60*5);
        $I->seeLink('Study Sauce course');
        $I->click('//a[contains(.,"Study Sauce course")]');

        // TODO: add past due test


    }
}