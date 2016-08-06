<?php
namespace Admin\Bundle\Tests;

use Admin\Bundle\Controller\ValidationController;
use Admin\Bundle\Tests\Codeception\Module\AcceptanceHelper;
use Codeception\Module\Doctrine2;
use Doctrine\ORM\EntityManager;
use StudySauce\Bundle\Entity\Coupon;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\Pack;
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
class StoreCest
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
    public function tryAddFreeToCart(AcceptanceTester $I) {
        $I->wantTo('Add a free product to the cart and checkout');
        $I->seeAmOnPage('/store');
        if($I->seePageHas('Access denied.')) {
            $I->test('tryAdminLogin');
        }
        $I->seeAmOnPage('/store');
        if(!$I->seePageHas('Free')) {
            $I->test('tryCreateProductListing');
        }
        $I->seeAmOnPage('/store');
        $I->click('Free');
        $I->click('a[href*="/store/cart"]');
        $I->wait(3);
        $value = $I->grabAttributeFrom('.coupon-row select option:not([value=""]):not([disabled])', 'value');
        $I->selectOption('.coupon-row select', $value);
        $I->click('Place order');
        $I->wait(3);
        $I->see('Thank you');
    }

    public function tryCreateProductListing(AcceptanceTester $I) {
        $last = substr(md5(microtime()), -5);
        /** @var Pack $freePack */
        $I->seeAmOnPage('/packs');
        if(!$I->seePageHas('TestPack')) {
            $I->test('tryCreateTestPack');
        }
        $I->seeAmOnPage('/packs');
        $pack = $I->grabTextFrom('//label[contains(.,"TestPack")]');
        $freePack = $I->grabFrom('StudySauceBundle:Pack', ['title' => $pack]);
        $coupon = new Coupon();
        $coupon->addPack($freePack);
        $coupon->setOptions(['ST' => ['price' => 0.0]]);
        $coupon->setDescription('Study Sauce ' . $freePack->getTitle());
        $coupon->setName('StudyTest' . $last);
        $I->persistEntity($coupon);
        $I->flushToDatabase();
    }

}