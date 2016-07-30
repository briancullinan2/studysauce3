<?php
namespace Admin\Bundle\Tests;

use Admin\Bundle\Controller\ValidationController;
use Admin\Bundle\Tests\Codeception\Module\AcceptanceHelper;
use Codeception\Module\Doctrine2;
use Doctrine\ORM\EntityManager;
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
class PackCest
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
    public function tryCreateTestPack(AcceptanceTester $I) {
        $last = substr(md5(microtime()), -5);
        $I->wantTo('Create a pack (TestPack' . $last . ') that contains cards for testing');
        $I->seeAmOnPage('/packs');
        if($I->seePageHas('Access denied.')) {
            $I->test('tryAdminLogin');
        }
        $I->seeAmOnPage('/packs');
        $I->test('tryDeleteTestPack');
        $I->click('a[href*="packs/0"]');
        $I->fillField('.pack-row input[name="title"]', 'TestPack' . $last);
        $I->click('Save');
        $I->wait(3);
        $I->seeInField('input[name="title"]', 'TestPack' . $last);
        $I->click('Edit Pack');
        // enter test cards
        $I->click('a[href="#add-card"]');
        $I->click('.card-row.empty:not(.removed) [name="content"]');
        $I->pressKey('.card-row [name="content"]', 'Prompt');
        $I->click('.card-row.empty:not(.removed) [name="correct"]');
        $I->pressKey('.card-row [name="correct"]', 'Correct');
        $I->click('Save');
        $I->wait(3);
    }

    /**
     * @param AcceptanceTester $I
     */
    public function tryDeleteTestPack(AcceptanceTester $I) {
        $I->wantTo('Delete the existing test packs');
        //$row = $I->grabAttributeFrom('input[name="groupName"]', 'class');
        $I->seeAmOnPage('/packs');
        $I->click('Packs');

    }

}