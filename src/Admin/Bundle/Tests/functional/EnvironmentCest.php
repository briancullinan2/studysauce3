<?php
namespace Admin\Bundle\Tests;

use WebDriver;
use WebDriverBy;
use WebDriverKeys;

/**
 * Class PageLoaderCest
 * @package StudySauce\Bundle\Tests
 * @backupGlobals false
 * @backupStaticAttributes false
 */
class EnvironmentCest
{
    /**
     * @param FunctionalTester $I
     */
    public function _before(FunctionalTester $I)
    {
    }

    /**
     * @param FunctionalTester $I
     */
    public function _after(FunctionalTester $I)
    {
    }

    // tests

    /**
     * @param FunctionalTester $I
     */
    public function tryCheckSettings(FunctionalTester $I)
    {
        $getValue = function ($m) {
            return $m[1] * ($m[2] == 'g' ? 1073741824 : ($m[2] == 'm' ? 1048576 : ($m[2] == 'k' ? 1024 : 1)));
        };
        $I->assertGreaterThanOrEqual(preg_replace_callback('/([0-9])+(m|k|g)*b?$/i', $getValue, strtolower(ini_get('memory_limit'))), 128 * 1024 * 1024, ' memory_limit is high enough');
        $I->assertContains(ini_get('date.timezone'), 'US/Arizona', 'timezone matches database');
    }

    /**
     * @param FunctionalTester $I
     */
    public function tryDialogTitleLength(FunctionalTester $I)
    {

    }
}