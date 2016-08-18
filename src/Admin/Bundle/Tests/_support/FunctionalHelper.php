<?php
namespace Admin\Bundle\Tests;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Admin\Bundle\Controller\EmailsController;
use Codeception\Test\Cest;
use PHPUnit_Framework_TestResult;
use PHPUnit_Framework_TestSuite;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FunctionalHelper
 * @package Admin\Bundle\Tests\Codeception\Module
 */
class FunctionalHelper extends \Codeception\Module
{
    /** @var PHPUnit_Framework_TestResult $test */
    private $result = null;

    // HOOK: before scenario
    /**
     * @param Cest $test
     */
    public function _before(Cest $test)
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
     * @param $email
     * @throws \Codeception\Exception\Module
     */
    public function testEmail($email)
    {
        /** @var ContainerInterface $container */
        $container = $this->getModule('Symfony2')->container;
        $emails = new EmailsController();
        $emails->setContainer($container);
        $emails->confirm = true;
        foreach(explode(';', $container->getParameter('defer_all_emails') ?: 'brian@studysauce.com') as $address) {
            $emails->buildEmail($email, ['userEmail' => $address]);
        }
    }
}
