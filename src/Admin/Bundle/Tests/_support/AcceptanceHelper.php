<?php
namespace Admin\Bundle\Tests\Codeception\Module;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Module\WebDriver;
use Codeception\TestCase;
use Codeception\TestCase\Cest;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestResult;
use PHPUnit_Framework_TestSuite;

/**
 * Class AcceptanceHelper
 * @package Admin\Bundle\Tests\Codeception\Module
 */
class AcceptanceHelper extends \Codeception\Module
{
    /** @var PHPUnit_Framework_TestResult $test */
    private $result = null;

    // HOOK: before scenario
    /**
     * @param TestCase $test
     */
    public function _before(TestCase $test)
    {
        $this->result = $test->getTestResultObject();
    }

    /**
     * @param $methodName
     * @throws \Codeception\Exception\Configuration
     */
    public function test($methodName)
    {
        /** @var PHPUnit_Framework_TestSuite $suite */
        $suite = $this->result->topTestSuite();
        foreach($suite->tests() as $t)
        {
            /** @var Cest $t */
            $t->setBackupStaticAttributes(false);
            $t->setBackupGlobals(false);
            if($t->getName() == $methodName) {
                $t->run($this->result);
                break;
            }
        }
    }

    /**
     * @param $url
     * @throws \Codeception\Exception\Module
     */
    public function seeAmOnPage($url)
    {
        // first assert that we see the url
        /** @var WebDriver $driver */
        $driver = $this->getModule('WebDriver');
        // if already on this page, don't do any navigation
        if($driver->webDriver->getCurrentURL() == 'about:blank') {
            //if(strpos($url, '://') !== false) {
                $driver->amOnPage($url);
            //}
            //else {
            //    $driver->amOnPage($driver->_getUrl() . $url);
            //}
        }
        else if(strcmp($driver->_getCurrentUri(), $url) !== 0)
        {
            $driver->amOnPage($url);
        }
        else
        {
            $driver->seeInCurrentUrl($url);
        }
    }

    public function grabFrom($entity, $fields) {
        // we need to store to database...
        /** @var EntityManager $em */
        $em = $this->getModule('Doctrine2')->em;
        $em->flush();
        $qb = $em->getRepository($entity)->findOneBy($fields);
        return $qb;

    }
}
