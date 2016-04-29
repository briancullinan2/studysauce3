<?php

namespace Admin\Bundle\Controller;

use Codeception\Configuration;
use Codeception\Event\FailEvent;
use Codeception\Event\StepEvent;
use Codeception\Event\TestEvent;
use Codeception\Events;
use Codeception\Exception\ElementNotFound;
use Codeception\Exception\TestRuntime;
use Codeception\Module\Doctrine2;
use Codeception\Module\Symfony2;
use Codeception\Module\WebDriver;
use Codeception\PHPUnit\Listener;
use Codeception\PHPUnit\ResultPrinter\UI;
use Codeception\PHPUnit\Runner;
use Codeception\Scenario;
use Codeception\Subscriber\AutoRebuild;
use Codeception\Subscriber\BeforeAfterTest;
use Codeception\Subscriber\Bootstrap;
use Codeception\Subscriber\ErrorHandler;
use Codeception\Subscriber\Module;
use Codeception\SuiteManager;
use Codeception\TestCase;
use Codeception\TestCase\Cest;
use Codeception\Lib\TestLoader;
use Codeception\Util\Locator;
use Doctrine\ORM\Query;
use PHP_Timer;
use PHPUnit_Framework_TestFailure;
use PHPUnit_Util_Test;
use RemoteWebDriver;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ValidationController
 * @package StudySauce\Bundle\Controller
 */
class ValidationController extends Controller
{

    public static $dispatcher;
    private static $config = [];
    public static $settings;

    private static function setupThis()
    {
        if (!defined('PHPUNIT_TESTSUITE')) {
            define('PHPUNIT_TESTSUITE', true);
        }
        require_once(__DIR__ . '/../../../../vendor/codeception/codeception/autoload.php');

        Configuration::config(__DIR__ . '/../');

        self::$config = [
            'suites' => Configuration::suites(),
            'tests' => []
        ];
        foreach (self::$config['suites'] as $suite) {
            self::$config[$suite] = Configuration::suiteSettings($suite, Configuration::config());
            $testLoader = new TestLoader(self::$config[$suite]['path']);
            $testLoader->loadTests();
            self::$config['tests'][$suite] = $testLoader->getTests();
            self::$config['tests'][$suite] = array_combine(array_map(function (Cest $t) { return $t->getName();}, self::$config['tests'][$suite]), self::$config['tests'][$suite]);
        }

    }

    /**
     * @param Request $request
     * @return Response
     */
    public function indexAction(Request $request)
    {

        /** @var $user User */
        $user = $this->getUser();
        if (!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }

        self::setupThis();

        return $this->render('AdminBundle:Validation:tab.html.php', self::$config);
    }

    public function resultAction(Request $request)
    {

        /** @var $user User */
        $user = $this->getUser();
        if (!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }
        self::setupThis();

        foreach(scandir(codecept_log_dir()) as $file) {
            $fpath = codecept_log_dir() . DIRECTORY_SEPARATOR . $file;
            $ftime = filectime($fpath);
            if($file != $request->get('result'))
                continue;
            return new JsonResponse(array_merge(unserialize(file_get_contents($fpath)), ['created' => $ftime]));
        }
        throw new NotFoundHttpException();
    }

    public function refreshAction(Request $request)
    {

        /** @var $user User */
        $user = $this->getUser();
        if (!$user->hasRole('ROLE_ADMIN')) {
            throw new AccessDeniedHttpException();
        }
        self::setupThis();
        $nodes = [];
        $edges = [];
        $edgeIndex = [];
        $count = 2;
        $deployIncludes = self::getIncludedTests(self::$config['tests']['acceptance']['tryDeploy']);
        $since = date_sub(new \DateTime(), new \DateInterval('P1D'));
        foreach(scandir(codecept_log_dir()) as $file) {
            $fpath = codecept_log_dir() . DIRECTORY_SEPARATOR . $file;
            $ftime = filectime($fpath);
            if($ftime < $since->getTimestamp() ||
                substr($file, 0, strlen('TestResults-')) != 'TestResults-')
                continue;

            $results[substr($file, strlen('TestResults-PASS-'), -10)][] = [
                'status' => substr($file, strlen('TestResults-'), 4),
                'created' => date_timestamp_set(new \DateTime(), $ftime)->format('r'),
                'resultId' => $file
            ];
        }
        $ids = call_user_func_array('array_merge', array_map(function ($s) {return array_keys($s);}, self::$config['tests']));
        foreach (self::$config['suites'] as $suite) {
            foreach (self::$config['tests'][$suite] as $id => $t) {
                /** @var Cest $t */

                $depends = array_map(
                    function ($d) {
                        $test = explode('::', $d);

                        return count($test) == 1 ? $test[0] : $test[1];
                    },
                    PHPUnit_Util_Test::getDependencies(get_class($t->getTestClass()), $id)
                );
                $includes = self::getIncludedTests($t);

                $clusters = array_unique(array_intersect($deployIncludes, array_merge($depends, $includes)));
                if(isset($results[$id])) {
                    usort($results[$id], function ($a, $b) {
                        return strtotime($b['created']) - strtotime($a['created']);
                    });
                }

                $nodes[] = [
                    'id' => $id,
                    'label' => preg_replace('/[A-Z]/', " $0", substr($id, 3)),
                    'x' => floor($count / 20) * 10,
                    'y' => $count % 20,
                    'color' => !isset($results[$id]) ? '#555' : ($results[$id][0]['status'] == 'SKIP' ? '#55C' : (($results[$id][0]['status'] == 'FAIL') ? '#C55' : '#5C5')),
                    'size' => ($id == 'tryInstall' || $id == 'tryDeploy' ? 5 : 0) + (in_array($id, $deployIncludes) ? 2 : 0) +
                        + (count($depends) > 0 ? 1 : 0) + (count($includes) > 0 ? 1 : 0) + 1,
                    'depends' => array_values($depends),
                    'includes' => array_values($includes),
                    'results' => isset($results[$id]) ? $results[$id] : null,
                    'type' => !isset($results[$id]) || $results[$id][0]['status'] == 'SKIP' ? 'circle' : (($results[$id][0]['status'] == 'FAIL') ? 'square' : 'diamond'),
                    'suite' => $suite
                    //'cluster' => implode(' ' . $suite . '-', $clusters)
                ];

                foreach($depends as $edge) {
                    if(!in_array($edge, $ids))
                        continue;
                    $existing = array_merge(isset($edgeIndex[$id . $edge]) ? $edgeIndex[$id . $edge] : [], isset($edgeIndex[$edge . $id]) ? $edgeIndex[$id . $edge] : []);
                    $size = count($existing) + 1;
                    foreach($existing as $eI) {
                        $edges[$eI]['size'] = $size;
                        $edges[$eI]['type'] = 'parallel';
                    }

                    $edgeIndex[$id . $edge][] = count($edges);
                    $edges[] = [
                        'id' => 'e' . count($edges),
                        'source' => $id,
                        'type' => $id == $edge ? 'curve' : ($size > 1 ? 'parallel' : 'curvedArrow'),
                        'target' => $edge,
                        'color' => 'rgba(0,0,0,0)',
                        'size' => $size
                        //'weight' => (in_array($id, $deployIncludes) || in_array($edge, $deployIncludes) ? 2 : 1)
                    ];
                }

                foreach($includes as $edge) {
                    if(!in_array($edge, $ids))
                        continue;
                    $existing = array_merge(isset($edgeIndex[$id . $edge]) ? $edgeIndex[$id . $edge] : [], isset($edgeIndex[$edge . $id]) ? $edgeIndex[$id . $edge] : []);
                    $size = count($existing) + 1;
                    foreach($existing as $eI) {
                        $edges[$eI]['size'] = $size;
                        $edges[$eI]['type'] = 'parallel';
                    }

                    $edgeIndex[$id . $edge][] = count($edges);
                    $edges[] = [
                        'id' => 'e' . count($edges),
                        'source' => $id,
                        'target' => $edge,
                        'type' => $id == $edge ? 'curve' : ($size > 1 ? 'parallel' : 'arrow'),
                        'color' => 'rgba(0,0,0,0)',
                        'size' => $size
                        //'weight' => (in_array($id, $deployIncludes) || in_array($edge, $deployIncludes) ? 2 : 1)
                    ];
                }
                $count++;
            }
        }

        return new JsonResponse(['nodes' => $nodes, 'edges' => array_values($edges)]);
    }

    /**
     * @param $allTests
     * @param $tests
     * @param int $level
     * @return array
     */
    private static function getTestDependencies($allTests, $tests, $level = 1)
    {
        $dependencies = [];
        if ($level <= 0) {
            return $dependencies;
        }
        foreach ($allTests as $i => $t) {
            /** @var Cest $t */
            if (in_array($t->getName(), $tests)) {
                // automatically include dependencies
                $depends = array_map(
                    function ($d) {
                        $test = explode('::', $d);

                        return count($test) == 1 ? $test[0] : $test[1];
                    },
                    PHPUnit_Util_Test::getDependencies(get_class($t->getTestClass()), $t->getName())
                );
                $dependencies = array_merge(
                    array_merge($dependencies, $depends),
                    self::getTestDependencies($allTests, self::getIncludedTests($t), $level - 1)
                );
            }
        }

        return $dependencies;
    }

    protected function getStrictLocator(array $by)
    {
        $type = key($by);
        $locator = $by[$type];
        switch ($type) {
            case 'id':
                return \WebDriverBy::id($locator);
            case 'name':
                return \WebDriverBy::name($locator);
            case 'css':
                return \WebDriverBy::cssSelector($locator);
            case 'xpath':
                return \WebDriverBy::xpath($locator);
            case 'link':
                return \WebDriverBy::linkText($locator);
            case 'class':
                return \WebDriverBy::className($locator);
            default:
                throw new TestRuntime(
                    "Locator type '$by' is not defined. Use either: xpath, css, id, link, class, name"
                );
        }
    }

    protected function match(RemoteWebDriver $page, $selector)
    {
        $nodes = array();
        if (is_array($selector)) {
            return $page->findElements($this->getStrictLocator($selector));
        }
        if ($selector instanceof \WebDriverBy) {
            return $page->findElements($selector);
        }

        if (Locator::isID($selector)) {
            $nodes = $page->findElements(\WebDriverBy::id(substr($selector, 1)));
        }
        if (!empty($nodes)) {
            return $nodes;
        }
        if (Locator::isCSS($selector)) {
            $nodes = $page->findElements(\WebDriverBy::cssSelector($selector));
        }
        if (!empty($nodes)) {
            return $nodes;
        }
        if (Locator::isXPath($selector)) {
            $nodes = $page->findElements(\WebDriverBy::xpath($selector));
        } else {
            codecept_debug("XPath `$selector` is malformed!");
        }

        return $nodes;
    }

    protected function findClickable(RemoteWebDriver $page, $link)
    {
        if (is_array($link) or ($link instanceof \WebDriverBy)) {
            $els = $this->match($page, $link);
            if (count($els)) {
                return reset($els);
            }
            throw new ElementNotFound($link, 'CSS or XPath');
        }

        // try to match by CSS or XPath
        $els = $this->match($page, $link);
        if (!empty($els)) {
            return reset($els);
        }

        $locator = Crawler::xpathLiteral(trim($link));

        // narrow
        $xpath = Locator::combine(
            ".//a[normalize-space(.)=$locator]",
            ".//button[normalize-space(.)=$locator]",
            ".//a/img[normalize-space(@alt)=$locator]/ancestor::a",
            ".//input[./@type = 'submit' or ./@type = 'image' or ./@type = 'button'][normalize-space(@value)=$locator]"
        );

        $els = $page->findElements(\WebDriverBy::xpath($xpath));
        if (count($els)) {
            return reset($els);
        }

        // wide
        $xpath = Locator::combine(
            ".//a[./@href][((contains(normalize-space(string(.)), $locator)) or .//img[contains(./@alt, $locator)])]",
            ".//input[./@type = 'submit' or ./@type = 'image' or ./@type = 'button'][contains(./@value, $locator)]",
            ".//input[./@type = 'image'][contains(./@alt, $locator)]",
            ".//button[contains(normalize-space(string(.)), $locator)]",
            ".//input[./@type = 'submit' or ./@type = 'image' or ./@type = 'button'][./@name = $locator]",
            ".//button[./@name = $locator]"
        );

        $els = $page->findElements(\WebDriverBy::xpath($xpath));
        if (count($els)) {
            return reset($els);
        }

        return null;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function testAction(Request $request)
    {
        set_time_limit(0);

        self::setupThis();

        $steps = [];
        if (!empty(static::$settings = self::$config[$suite = $request->get('suite')])) {
            // get the path of the test
            $options = ['verbosity' => 3, 'colors' => false];
            if (!empty($request->get('test'))) {
                $tests = explode('|', $request->get('test'));
                $depends = self::getTestDependencies(self::$config['tests'][$suite], $tests);
                $tests = array_merge($tests, $depends);
                $options['filter'] = implode('|', array_unique($tests));
                if (!isset($options['filter'])) {
                    return new JsonResponse(true);
                }
            }

            // set customized settings
            if (!empty($request->get('host'))) {
                static::$settings['modules']['config']['WebDriver']['host'] = $request->get('host');
            }
            if (!empty($request->get('browser'))) {
                static::$settings['modules']['config']['WebDriver']['browser'] = $request->get('browser');
            }
            if (!empty($request->get('wait'))) {
                static::$settings['modules']['config']['WebDriver']['wait'] = $request->get('wait');
            }
            if (!empty($request->get('url'))) {
                static::$settings['modules']['config']['WebDriver']['url'] = $request->get('url');
            }


            /** @var EventDispatcher self::$dispatcher */
            self::$dispatcher = new EventDispatcher();
            $result = new \PHPUnit_Framework_TestResult;
            $runner = new Runner($options);
            $printer = new UI(self::$dispatcher, $options);
            // required
            self::$dispatcher->addSubscriber(new ErrorHandler());
            self::$dispatcher->addSubscriber(new Bootstrap());
            self::$dispatcher->addSubscriber(new Module());
            self::$dispatcher->addSubscriber(new BeforeAfterTest());
            self::$dispatcher->addSubscriber(new AutoRebuild());

            $features = [];
            $screenDir = $this->container->getParameter('kernel.root_dir') . '/../web/bundles/admin/results/';
            self::$dispatcher->addListener(
                Events::STEP_BEFORE,
                function (StepEvent $x) use (&$steps, &$features, $screenDir) {
                    /** @var Scenario $scenario */
                    if (($scenario = $x->getTest()->getScenario()) && $scenario->getFeature() != end($features)) {
                        $steps[$x->getTest()->getName()] .= '<h4>I want to ' . $scenario->getFeature() . '</h4>';
                        array_push($features, $scenario->getFeature());
                    }
                    // take a screenshot before click
                    if ($x->getStep()->getAction() == 'wait') {
                        $steps[$x->getTest()->getName()] .= '<span class="step">I <strong>' . $x->getStep()->getAction(
                            ) . '</strong> ' . str_replace(
                                '"',
                                '',
                                $x->getStep()->getArguments(true)
                            ) . ' seconds</span>';
                    } elseif ($x->getStep()->getAction() == 'click') {
                        $ss = 'TestClick' . substr(md5(microtime()), -5);
                        /** @var WebDriver $driver */
                        $driver = SuiteManager::$modules['WebDriver'];
                        /** @var \RemoteWebElement $ele */
                        $ele = $this->findClickable($driver->webDriver, trim($x->getStep()->getArguments()[0], '"'));
                        if (!empty($ele) && $ele->getSize()->getWidth() > 0 && $ele->getSize()->getHeight() > 0) {
                            $driver->webDriver->executeScript('if(typeof $ != \'undefined\' && typeof DASHBOARD_MARGINS != \'undefined\') { $(arguments[0]).scrollintoview(DASHBOARD_MARGINS); } else { arguments[0].scrollIntoView(true); }', [$ele]);
                            $driver->wait(1);
                            $driver->makeScreenshot($ss);
                            $point = $ele->getCoordinates()->inViewPort();
                            $size = $ele->getSize();
                            $im = imagecreatefrompng(codecept_log_dir() . 'debug' . DIRECTORY_SEPARATOR . $ss . '.png');
                            $targetImage = imagecreatetruecolor($size->getWidth(), $size->getHeight());
                            $background = imagecolorallocate($im, 0, 0, 0);
                            imagecolortransparent($im, $background);
                            imagealphablending($targetImage, false);
                            imagesavealpha($targetImage, true);
                            imagecopyresampled($targetImage, $im,
                                0, 0,
                                $point->getX(), $point->getY(),
                                $size->getWidth(), $size->getHeight(),
                                $size->getWidth(), $size->getHeight());
                            imagepng($targetImage, codecept_log_dir() . 'debug' . DIRECTORY_SEPARATOR . $ss . '.png');
                            //Get width and height of the element
                            $steps[$x->getTest()->getName()] .= '<span class="step">I <strong>' . $x->getStep(
                                )->getAction() . '</strong> <a target="_blank" href="/bundles/admin/results/debug/' .
                                $ss . '.png"><img style="max-width:300px;" src="/bundles/admin/results/debug/' . $ss . '.png" /></a></span>';
                        } else {
                            $steps[$x->getTest()->getName()] .= '<span class="step">I <strong>' . $x->getStep(
                                )->getAction() . '</strong> ' . str_replace(
                                    '"',
                                    '',
                                    $x->getStep()->getArguments(true)
                                ) . '</span>';
                        }
                    } else {
                        $steps[$x->getTest()->getName()] .= '<span class="step">I <strong>' . $x->getStep()->getAction(
                            ) . '</strong> ' . str_replace('"', '', $x->getStep()->getArguments(true)) . '</span>';
                    }
                }
            );
            self::$dispatcher->addListener(
                Events::TEST_BEFORE,
                function (TestEvent $x) use (&$steps, &$features) {
                    if (!isset($steps[$x->getTest()->getName()])) {
                        $steps[$x->getTest()->getName()] = '';
                    }
                    array_push($features, end($features));
                }
            );
            self::$dispatcher->addListener(
                Events::TEST_AFTER,
                function (TestEvent $x) use (&$features, $result, $runner, &$steps) {
                    array_pop($features);
                    if (isset($result) && isset($runner)) {
                        $result->flushListeners();
                        $printer = $runner->getPrinter();
                        $errors = [];
                        foreach ($result->errors() as $e) {
                            /** @var PHPUnit_Framework_TestFailure $e */
                            $errors[] = $e->thrownException() . '';
                        }
                        $output = '';
                        ob_start(function ($str) use (&$output, $printer, $result) {
                            $output .= $str;
                        });
                        $printer->printResult($result);
                        ob_end_flush();
                        $results = [$x->getTest()->getName() => [
                            'result' => $output,
                            'errors' => $errors,
                            'steps' => $steps[$x->getTest()->getName()]
                        ]];
                        $status = strpos($output, 'FAILURES!') !== false ? 'FAIL' : (strpos($output, 'OK (') !== false ? 'PASS' : 'SKIP');
                        $fh = fopen(codecept_log_dir() . 'TestResults-' . $status . '-' . $x->getTest()->getName() . substr(md5(microtime()), -5) . '.html', 'w+');
                        fwrite($fh, serialize($results));
                        fclose($fh);
                        $steps[$x->getTest()->getName()] = '';
                    }
                }
            );
            self::$dispatcher->addListener(
                Events::STEP_AFTER,
                function (StepEvent $x) use (&$steps) {
                    // look for javascript errors
                    if (isset(SuiteManager::$modules['WebDriver'])) {
                        /** @var WebDriver $driver */
                        $driver = SuiteManager::$modules['WebDriver'];
                        $driver->wait(1);
                        $jsErrors = $driver->executeJS(
                            'if(typeof window.jsErrors != \'undefined\') { return (function () {var tmpErrors = window.jsErrors; window.jsErrors = []; return tmpErrors || [];})() };'
                        );
                        try {
                            $x->getTest()->assertEmpty(
                                $jsErrors,
                                'Javascript errors: ' . (is_array($jsErrors) ? implode($jsErrors) : $jsErrors)
                            );
                        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
                            $x->getTest()->getTestResultObject()->addFailure($x->getTest(), $e, PHP_Timer::stop());
                        }
                    }

                    // check for failures
                    //$x->getTest()->getTestResultObject()->failures()
                }
            );
            self::$dispatcher->addListener(
                Events::TEST_ERROR,
                function (FailEvent $x) use (&$steps, $screenDir) {
                    $ss = 'TestFailure' . substr(md5(microtime()), -5);
                    $steps[$x->getTest()->getName()] .= '<pre class="error">' . htmlspecialchars(
                            $x->getFail()->getMessage(),
                            ENT_QUOTES
                        );
                    // try to get a screenshot to show in the browser
                    if (isset(SuiteManager::$modules['WebDriver'])) {
                        /** @var WebDriver $driver */
                        $driver = SuiteManager::$modules['WebDriver'];
                        $driver->makeScreenshot($ss);
                        $steps[$x->getTest()->getName()] .= '<br /><a target="_blank" href="/bundles/admin/results/debug/' .
                            $ss . '.png"><img width="300" src="/bundles/admin/results/debug/' . $ss . '.png" /></a>';
                    }
                    $steps[$x->getTest()->getName()] .= '</pre>';
                }
            );
            self::$dispatcher->addListener(
                Events::TEST_SKIPPED,
                function (FailEvent $x) use (&$steps, $screenDir) {
                    $ss = 'TestFailure' . substr(md5(microtime()), -5);
                    if(!isset($steps[$x->getTest()->getName()]))
                        $steps[$x->getTest()->getName()] = '';
                    $steps[$x->getTest()->getName()] .= '<pre class="error">' . htmlspecialchars(
                            $x->getFail()->getMessage(),
                            ENT_QUOTES
                        );
                    // try to get a screenshot to show in the browser
                    if (isset(SuiteManager::$modules['WebDriver'])) {
                        /** @var WebDriver $driver */
                        $driver = SuiteManager::$modules['WebDriver'];
                        $driver->makeScreenshot($ss);
                        $steps[$x->getTest()->getName()] .= '<br /><a target="_blank" href="/bundles/admin/results/debug/' .
                            $ss . '.png"><img width="300" src="/bundles/admin/results/debug/' . $ss . '.png" /></a>';
                    }
                    $steps[$x->getTest()->getName()] .= '</pre>';
                }
            );
            self::$dispatcher->addListener(
                Events::TEST_FAIL,
                function (FailEvent $x, $y, $z) use (&$steps, $screenDir) {
                    $ss = 'TestFailure' . substr(md5(microtime()), -5);
                    $steps[$x->getTest()->getName()] .= '<pre class="failure">' . htmlspecialchars(
                            $x->getFail()->getMessage(),
                            ENT_QUOTES
                        );
                    // try to get a screenshot to show in the browser
                    if (isset(SuiteManager::$modules['WebDriver'])) {
                        /** @var WebDriver $driver */
                        $driver = SuiteManager::$modules['WebDriver'];
                        $driver->makeScreenshot($ss);
                        rename(
                            codecept_log_dir() . 'debug' . DIRECTORY_SEPARATOR . $ss . '.png',
                            $screenDir . $ss . '.png'
                        );
                        $steps[$x->getTest()->getName()] .= '<br /><a target="_blank" href="/bundles/admin/results/' .
                            $ss . '.png"><img width="200" src="/bundles/admin/results/' . $ss . '.png" /></a>';
                    }
                    $steps[$x->getTest()->getName()] .= '</pre>';
                }
            );

            $result->addListener(new Listener(self::$dispatcher));
            $runner->setPrinter($printer);

            // don't initialize Symfony2 module because we are already running and will feed it the right parameters
            if (($i = array_search('Symfony2', static::$settings['modules']['enabled'])) !== false) {
                unset(static::$settings['modules']['enabled'][$i]);
            }

            $suiteManager = new SuiteManager(self::$dispatcher, $suite, static::$settings);
            $suiteManager->initialize();
            // add Symfony2 module back in without initializing, setting the correct kernel for the current instance
            static::$settings['modules']['enabled'] = ['Symfony2'];
            /** @var Symfony2 $symfony */
            $symfony = Configuration::modules(static::$settings)['Symfony2'];
            SuiteManager::$modules['Symfony2'] = $symfony;
            $symfony->kernel = $this->container->get('kernel');
            $suiteManager->getSuite()->setBackupGlobals(false);
            $suiteManager->getSuite()->setBackupStaticAttributes(false);
            $suiteManager->loadTests(null);
            Doctrine2::$em = $this->get('doctrine')->getManager();
            $suiteManager->run($runner, $result, $options);
        }

        return new JsonResponse(true);
    }

    /**
     * @param Cest $test
     * @return array
     */
    public static function getIncludedTests(Cest $test)
    {
        // get a list of all tests
        $allTests = '';
        foreach (self::$config['tests'] as $suite) {
            $allTests .= (!empty($allTests) ? '|' : '') . implode(
                    '|',
                    array_map(
                        function (Cest $t) {
                            return $t->getName();
                        },
                        $suite
                    )
                );
        }

        $tests = [];
        // get function code
        $reflector = new \ReflectionClass(get_class($test->getTestClass()));
        if ($reflector->hasMethod($test->getName())) {
            $method = $reflector->getMethod($test->getName());
            $line_start = $method->getStartLine() - 1;
            $line_end = $method->getEndLine();
            $line_count = $line_end - $line_start;
            $line_array = file($method->getFileName());
            $text = implode("", array_slice($line_array, $line_start, $line_count));

            // find calls to other functions in the class in this test
            preg_match_all('/' . $allTests . '/i', $text, $matches);
            foreach ($matches[0] as $i => $m) {
                if ($m == $test->getName() || empty($m)) {
                    continue;
                }
                $tests[] = $m;
            }
        }

        return array_unique($tests);
    }

}


