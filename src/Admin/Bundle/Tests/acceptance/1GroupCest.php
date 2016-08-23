<?php
namespace Admin\Bundle\Tests;

use Admin\Bundle\Controller\ValidationController;
use Admin\Bundle\Tests\Codeception\Module\AcceptanceHelper;
use Codeception\Module\Doctrine2;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Group;
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
class GroupCest
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

    /**
     * @param AcceptanceTester $I
     */
    public function tryCreateTestGroup(AcceptanceTester $I) {
        $last = substr(md5(microtime()), -5);
        $I->wantTo('Create a group (TestGroup' . $last . ') that contains users for testing');
        $I->seeAmOnPage('/groups');
        if($I->seePageHas('Access denied.')) {
            $I->test('tryAdminLogin');
        }
        $I->seeAmOnPage('/groups');
        $I->click('Groups');
        $I->test('tryDeleteTestGroup');
        $I->click('a[href*="groups/0"]');
        $I->fillField('.ss_group-row input[name="name"]', 'TestGroup' . $last);
        $I->click('Save');
        $I->wait(3);
        $I->seeInField('input[name="name"]', 'TestGroup' . $last);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryDeleteTestGroup(AcceptanceTester $I) {
        $I->wantTo('Delete the existing test groups');
        //$row = $I->grabAttributeFrom('input[name="groupName"]', 'class');
        $i = 0;
        while($i < 20) {
            $I->seeAmOnPage('/groups');
            if($I->seePageHas('Access denied.')) {
                $I->test('tryAdminLogin');
            }
            $I->seeAmOnPage('/groups');
            $I->click('Groups');
            if(!$I->seePageHas('TestGroup')) {
                break;
            }
            $test = $I->grabTextFrom('//a/span[contains(.,"TestGroup")]');
            /** @var Group $testGroup */
            $testGroup = $I->grabFrom('StudySauceBundle:Group', ['name' => $test]);
            if (!empty($testGroup)) {
                $testGroup->setDeleted(true);
                $I->mergeEntity($testGroup);
                $I->flushToDatabase();
                $I->seeAmOnPage('/home');
            } else {
                break;
            }
            $i++;
        }
    }

}