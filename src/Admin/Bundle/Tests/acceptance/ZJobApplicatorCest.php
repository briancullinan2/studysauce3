<?php
namespace Admin\Bundle\Tests;

use Codeception\Module\Doctrine2;
use StudySauce\Bundle\Entity\User;
use WebDriver;
use WebDriverBy;
use WebDriverKeys;

/**
 * Class JobApplicatorCest
 * @package StudySauce\Bundle\Tests
 * @backupGlobals false
 * @backupStaticAttributes false
 */
class JobApplicatorCest
{
    var $fh = null;

    /**
     * @param AcceptanceTester $I
     */
    public function _before(AcceptanceTester $I)
    {
        $screenDir = dirname(__FILE__) . '/../../../../../web/bundles/admin/results/jobs.txt';
        $this->fh = fopen($screenDir, 'rw+');
        if(!file_exists($screenDir)) {
            fwrite($this->fh, '');
        }
    }

    /**
     * @param AcceptanceTester $I
     */
    public function _after(AcceptanceTester $I)
    {
        fclose($this->fh);
    }

    // tests

    /**
     * @param AcceptanceTester $I
     */
    public function tryApplyJob(AcceptanceTester $I)
    {
        $I->wantTo('Apply to a thousand jobs');
        fseek($this->fh, 0);
        $size = fstat($this->fh)['size'];
        $alreadyApplied = '';
        if($size > 0) {
            $alreadyApplied = fread($this->fh, $size);
        }
        // can't exclude Java, blech, using linkedin
        $I->amOnUrl('http://www.indeed.com/jobs?q=software+developer&l=Phoenix,+AZ&radius=50&jt=fulltime&explvl=senior_level');
        $I->click('#prime-popover-close-button');

        $this->applyPage($I, $alreadyApplied);
    }

    public function applyPage(AcceptanceTester $I, $alreadyApplied, $pages = 10) {

        // loop through rows
        $i = 1;
        $applied = 0;
        while($i <= 10) {

            $company = $I->grabTextFrom('(//div[contains(@class,"row")])[' . $i . ']//span[contains(@class,"company")]');
            if(strpos($alreadyApplied, $company . "\n") === false) {
                // continue with application
                $I->click('(//div[contains(@class,"row")])[' . $i . ']//a[contains(@target,"_blank")]');
                fseek($this->fh, -1);
                fwrite($this->fh, $company . "\n");

                $applied++;

                $I->wait(2);

                // all done close tab
                $I->executeInSelenium(function (\Facebook\WebDriver\Remote\RemoteWebDriver $webdriver) {
                    $handles=$webdriver->getWindowHandles();
                    if(count($handles) > 1) {
                        $last_window = end($handles);
                        $webdriver->switchTo()->window($last_window);
                        $webdriver->close();
                        $first_window = reset($handles);
                        $webdriver->switchTo()->window($first_window);
                    }
                });
            }
            else {
                // skip job
            }

            $i++;

            if($applied >= 10) {
                break;
            }

        }

        if($applied < 10 && $pages > 0) {
            $I->click('Next');
            $this->applyPage($I, $alreadyApplied, $pages - 1);
        }
    }
}

// career sites:
//linkedin
//indeed
//monster
//craigslist
//angel.co
//careerbuilder
//theladder
//www.hired.com


// writing rules:
// anything with the word government add "I feel a strong sense of responsibility thanks to my mentor who is a former marine."
// anything for women introduction "Your company looks really neat."  <- Wrong, use LanguageAlchemy inputs instead
// discard anything with no exact matches and less than 1,000,000 hits
// switch sweet somethings at the end to grade response
// telephony, media, communications, audio visual, switch to resume at Radio Station

// If everyone can search for a job and do the research, then society gets smarter as a whole.

// Automated queries  (not necessarily important to me):
// I don't even know if you are allowed to know this information about a company?
// How much has this company been hiring in the last month? http://www.detroitnews.com/story/business/autos/general-motors/2015/06/08/gm-wants-workers-hiring-continues/28720893/
// How often is their layoff schedule?
// Do they offer benefits?
// Do they have a union?
// Company hierarchy
// perform searches with appended text "financial data", investment, news, projects, ceo (linkedin), founders, lawsuits


