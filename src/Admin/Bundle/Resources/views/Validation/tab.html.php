<?php
use Codeception\Configuration;
use Codeception\TestCase\Cest;
use Codeception\Util\Annotation;
use StudySauce\Bundle\Entity\User;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/validation.css'],[],['output' => 'bundles/admin/css/*.css']) as $url) { ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php }
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@sigma', '@AdminBundle/Resources/public/js/validation.js'],[],['output' => 'bundles/admin/js/*.js']) as $url) { ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php }
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="validation">
        <div class="pane-content">
            <div id="settings">
                <h2>Validation / <a href="#settings">Settings</a></h2>
                <label class="input host-setting"><span>Selenium Server</span>
                    <input type="text"
                           value="<?php print $view->escape(
                               $_SERVER['REMOTE_ADDR']
                           // from settings: $acceptance['modules']['config']['WebDriver']['host']
                           ); ?>"/>
                    <small>You must run <a href="http://www.seleniumhq.org/download/">Selenium Server</a> and <a
                            href="https://sites.google.com/a/chromium.org/chromedriver/downloads">ChromeDriver</a> with
                        the
                        command
                        <code>java -jar selenium-server-standalone-2.48.2.jar
                            -Dwebdriver.chrome.driver=.\chromedriver.exe
                            -port
                            4444</code>
                    </small>
                </label>
                <label class="input select browser-setting"><span>Browser</span>
                    <select>
                        <option
                            value="phantomjs" <?php print ($acceptance['modules']['config']['WebDriver']['browser'] == 'phantomjs' ? 'selected="selected"' : ''); ?>>
                            PhantomJS
                        </option>
                        <option
                            value="chrome" <?php print ($acceptance['modules']['config']['WebDriver']['browser'] == 'chrome' ? 'selected="selected"' : ''); ?>>
                            Chrome
                        </option>
                        <option
                            value="firefox" <?php print ($acceptance['modules']['config']['WebDriver']['browser'] == 'firefox' ? 'selected="selected"' : ''); ?>>
                            Firefox
                        </option>
                        <option
                            value="ie" <?php print ($acceptance['modules']['config']['WebDriver']['browser'] == 'ie' ? 'selected="selected"' : ''); ?>>
                            Internet Explorer
                        </option>
                    </select>
                    <small></small>
                </label>
                <label class="input wait-setting"><span>Wait</span>
                    <input type="text"
                           value="<?php print $view->escape(
                               $acceptance['modules']['config']['WebDriver']['wait']
                           ); ?>"/>
                    <small>Number of seconds between each step. Some steps require additional wait which will be shown
                        in the results.
                    </small>
                </label>
                <label class="input url-setting"><span>StudySauce URL</span>
                    <input type="text" value="https://<?php print $view->escape($_SERVER['HTTP_HOST']); ?>"/>
                    <small>Path to StudySauce instance to test (e.g. https://staging.studysauce.com or
                        https://test.studysauce.com). WARNING: database changes will occur on the selected instance.
                    </small>
                </label>
                <label class="input window_size"><span>Window Size</span>
                    <select name="window_size">
                        <option
                            value="1280x1024" <?php print ($acceptance['modules']['config']['WebDriver']['window_size'] == '1280x1024' ? 'selected="selected"' : ''); ?>>
                            Standard (1280x1024)
                        </option>
                        <option
                            value="480x800" <?php print ($acceptance['modules']['config']['WebDriver']['window_size'] == '480x800' ? 'selected="selected"' : ''); ?>>
                            Mobile Portrait (480x800)
                        </option>
                        <option
                            value="800x480" <?php print ($acceptance['modules']['config']['WebDriver']['window_size'] == '800x480' ? 'selected="selected"' : ''); ?>>
                            Mobile Landscape (800x480)
                        </option>
                    </select>
                    <small></small>
                </label>
            </div>
            <div id="sigma-container"></div>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');

$view['slots']->stop();
