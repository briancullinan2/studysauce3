<?php
namespace Admin\Bundle\Tests;

use Admin\Bundle\Controller\ValidationController;
use Admin\Bundle\Tests\Codeception\Module\AcceptanceHelper;
use Codeception\Module\Doctrine2;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\Response;
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
class AdminCest
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
    public function tryCreateAdmin(AcceptanceTester $I) {
        $I->wantTo('check for an admin account');
        /** @var User $admin */
        $admin = Doctrine2::$em->getRepository('StudySauceBundle:User')->findOneBy(['username' => 'brian@studysauce.com']);
        if(empty($admin)) {
            $I->wantTo('sign up for an admin account');
            $I->seeAmOnPage('/register');
            $I->fillField('input[name="first"]', 'Brian');
            $I->fillField('input[name="last"]', 'Cullinan');
            $I->fillField('input[name="email"]', 'brian@studysauce.com');
            $I->fillField('input[name="password"]', 'password');
            $I->seeLink('Save');
            $I->click('Save');
            $I->wait(5);
            $admin = Doctrine2::$em->getRepository('StudySauceBundle:User')->findOneBy(['username' => 'brian@studysauce.com']);
            if(!empty($admin)) {
                $admin->addRole('ROLE_ADMIN');
                Doctrine2::$em->merge($admin);
                Doctrine2::$em->flush();
            }
        }

        $I->test('tryAdminLogin');
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryAdminLogin(AcceptanceTester $I)
    {
        $I->wantTo('Login as the adviser account brian@studysauce.com');
        $I->amOnPage('/login');
        $I->fillField('#login .email input', 'brian@studysauce.com');
        $I->fillField('#login .password input', 'password');
        $I->click('#login [value="#user-login"]');
        $I->wait(5);
    }

    /**
     * @depends tryAdminLogin
     * @param AcceptanceTester $I
     */
    public function tryCreateTestGroup(AcceptanceTester $I) {
        $last = substr(md5(microtime()), -5);
        $I->wantTo('Create a group (TestGroup' . $last . ') that contains users for testing');
        $I->seeAmOnPage('/command');
        $I->click('Groups');
        $I->test('tryDeleteTestGroup');
        $I->click('a[href="#add-group"]');
        $I->fillField('#groups input[name="groupName"]', 'TestGroup' . $last);
        $I->click('Create group');
        $I->wait(3);
        $I->seeInField('input[name="groupName"]', 'TestGroup' . $last);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryDeleteTestGroup(AcceptanceTester $I) {
        $I->wantTo('Delete the existing test groups');
        //$row = $I->grabAttributeFrom('input[name="groupName"]', 'class');
    }

    /**
     * @depends tryAdminLogin
     * @depends tryCreateTestGroup
     * @param AcceptanceTester $I
     */
    public function tryInviteTestUser(AcceptanceTester $I) {
        $last = substr(md5(microtime()), -5);
        $I->wantTo('Invite a test user (TestInvite' . $last . ') to download the app and register');
        $I->seeAmOnPage('/import');
        $I->fillField('#import .first-name input', 'Test');
        $I->fillField('#import .last-name input', 'Invite' . $last);
        $I->fillField('#import .email input', 'TestInvite' . $last . '@mailinator.com');
        $test = $I->grabValueFrom('//option[contains(.,"TestGroup")]');
        $I->selectOption('#import .group select', $test);
        $I->click('Import');
        $I->wait(3);
        $I->seeInField('#import .last-name input', 'Invite' . $last);
    }

    /**
     * @depends tryAdminLogin
     * @depends tryCreateTestGroup
     * @depends tryInviteTestUser
     * @param AcceptanceTester $I
     */
    public function tryAcceptInvite(AcceptanceTester $I) {
        $host = ValidationController::$settings['modules']['config']['WebDriver']['host'];
        $I->amOnPage('/cron');
        $I->amOnPage('http://localhost:50001/startp');
        $I->see('PASS');
        $I->amOnPage('http://localhost:50001/scripts/home/Documents/studysauceapp/StudySauceTests/');
        $I->see('PASS');
        $I->amOnPage('http://localhost:50001/run/invite_code');
        $I->see('PASS');
    }

    private function subtract1Day($user) {
        Doctrine2::$em->clear();
        /** @var Response[] $responses */
        $responses = Doctrine2::$em->getRepository('StudySauceBundle:Response')->findBy(['user' => $user]);
        foreach($responses as $r) {
            $r->setCreated(date_sub(clone $r->getCreated(), new \DateInterval('P1D')));
            Doctrine2::$em->merge($r);
        }
        Doctrine2::$em->flush();
        return count($responses);
    }

    /**
     * @depends tryAdminLogin
     * @depends tryCreateTestGroup
     * @depends tryInviteTestUser
     * @depends tryAcceptInvite
     * @param AcceptanceTester $I
     */
    public function tryAllHomeCards(AcceptanceTester $I) {
        // get the last test user added
        $I->amOnPage('https://' . $_SERVER['HTTP_HOST'] . '/import');
        $test = $I->grabAttributeFrom('//div[contains(@class,"import-row") and .//input[contains(@value,"Test")]]', 'class');
        $I->assertTrue(preg_match('/invite-id-([0-9]*)/', $test, $matches) == 1);
        /** @var Invite $invite */
        $invite = Doctrine2::$em->getRepository('StudySauceBundle:Invite')->findOneBy(['id' => $matches[1]]);

        $I->seeAmOnPage('/home');

        // TODO: answer all cards
        $I->wait(1);
        $totalCards = $I->grabTextFrom('.user-shuffle header label:last-of-type');
        $total = intval(explode(' ', $totalCards)[0]);
        if($total > 0) {
            $I->click('.user-shuffle header a');

        }

        return;


        // check if results are recorded properly
        $count = self::subtract1Day($invite->getInvitee());
        $I->assertEquals(7, $count);
        $I->wait(10);
        // TODO: change response dates, reset app and check for
        $I->amOnPage('http://localhost:50001/run/home_screen_1');
        $I->see('PASS');
        $I->wait(1);
        $count = self::subtract1Day($invite->getInvitee()->getId());
        $I->assertEquals(8, $count);
        $I->wait(10);
        $I->amOnPage('http://localhost:50001/run/home_screen_5');
        $I->see('PASS');
        $I->wait(1);
        $count = self::subtract1Day($invite->getInvitee()->getId());
        $I->assertEquals(13, $count);
        $I->wait(10);
        $I->amOnPage('http://localhost:50001/run/home_screen_1');
        $I->see('PASS');
        $I->wait(1);
        $count = self::subtract1Day($invite->getInvitee()->getId());
        $I->assertEquals(14, $count);
        $I->wait(10);
        $I->amOnPage('http://localhost:50001/run/home_screen_empty');
        $I->see('PASS');
        $I->wait(1);
        $count = self::subtract1Day($invite->getInvitee()->getId());
        $I->assertEquals(14, $count);
        $I->wait(10);
        $I->amOnPage('http://localhost:50001/run/home_screen_empty');
        $I->see('PASS');
        $I->wait(1);
        $count = self::subtract1Day($invite->getInvitee()->getId());
        $I->assertEquals(14, $count);
        $I->wait(10);
        $I->amOnPage('http://localhost:50001/run/home_screen_5');
        $I->see('PASS');
        $I->wait(1);
        $count = self::subtract1Day($invite->getInvitee()->getId());
        $I->assertEquals(19, $count);
        $I->wait(10);
        $I->amOnPage('http://localhost:50001/run/home_screen_1');
        $I->see('PASS');
        $I->wait(1);
        $count = self::subtract1Day($invite->getInvitee()->getId());
        $I->assertEquals(20, $count);
        $I->wait(10);
    }

    /**
     * @depends tryAdminLogin
     * @param AcceptanceTester $I
     */
    public function tryGroupInvite(AcceptanceTester $I)
    {
        $I->wantTo('Invite a student to join study sauce');
        $I->seeAmOnPage('/userlist');
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
        $I->seeAmOnPage('/register');
        $I->fillField('#register .first-name input', $last);
        $I->fillField('#register .last-name input', 'last' . $last);
        $I->fillField('#register .email input', 'firstlast' . $last . '@mailinator.com');
        $I->fillField('#register .password input', 'password');
        $I->click('[value="#user-register"]');
        $I->wait(5);

        $I->wantTo('See the student\'s entries from the adviser view');
        $I->click('a[href*="/logout"]');
        $I->click('a[href*="/login"]');
        $I->test('tryAdminLogin');
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
        $I->test('tryAdminLogin');
        $I->see('last' . $last); // check for student name in user list
        $I->click('last' . $last); // load the student
        $I->see('last' . $last); // check the name in the corner of the tab
        $I->click('//h3[contains(.,"Course 1")]');
        $I->click('//h4[contains(.,"Introduction")]');
        $I->seeCheckboxIsChecked('input[value="college-senior"]');

        $I->wantTo('Register as a new student before my adviser has a chance to invite me');
        $I->click('a[href*="/logout"]');
        $I->seeAmOnPage('/register');
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
        $I->test('tryAdminLogin');
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

}