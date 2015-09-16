<?php
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */
/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/signup.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/signup.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="signup">
    <div class="pane-content">
        <h2>Welcome to Study Sauce!</h2>
        <div id="billing-pane">
            <h3>Enter the information below to get started.</h3>
            <br />
            <div class="organization">
                <label class="input"><span>Organization</span><input name="organization" type="text" value="<?php print $organization; ?>"></label>
            </div>
            <div class="first-name">
                <label class="input"><span>Point of contact</span><input name="first-name" type="text" value="<?php print $first . ' ' . $last; ?>"></label>
            </div>
            <div class="title">
                <label class="input"><span>Title</span><input name="title" type="text" value="<?php print $title; ?>"></label>
            </div>
            <div class="email">
                <label class="input"><span>E-mail address</span><input name="email" type="text" value="<?php print $email; ?>"></label>
            </div>
            <div class="phone">
                <label class="input"><span>Phone number</span><input name="phone" type="text" value="<?php print $phone; ?>"></label>
            </div>
            <label class="input"><span>Street address</span><input name="street1" type="text" value=""></label>
            <label class="input"><span>&nbsp;</span><input name="street2" type="text" value=""></label>
            <div class="city">
                <label class="input"><span>City</span><input name="city" type="text" value=""></label>
            </div>
            <div class="zip">
                <label class="input"><span>Postal code</span><input name="zip" type="text" value=""></label>
            </div>
            <label class="select"><span>State/Province</span><select name="state">
                    <option value="" selected="selected">- Select -</option>
                    <option value="Alabama">Alabama</option>
                    <option value="Alaska">Alaska</option>
                    <option value="American Samoa">American Samoa</option>
                    <option value="Arizona">Arizona</option>
                    <option value="Arkansas">Arkansas</option>
                    <option value="Armed Forces Africa">Armed Forces Africa</option>
                    <option value="Armed Forces Americas">Armed Forces Americas</option>
                    <option value="Armed Forces Canada">Armed Forces Canada</option>
                    <option value="Armed Forces Europe">Armed Forces Europe</option>
                    <option value="Armed Forces Middle East">Armed Forces Middle East</option>
                    <option value="Armed Forces Pacific">Armed Forces Pacific</option>
                    <option value="California">California</option>
                    <option value="Colorado">Colorado</option>
                    <option value="Connecticut">Connecticut</option>
                    <option value="Delaware">Delaware</option>
                    <option value="District of Columbia">District of Columbia</option>
                    <option value="Federated States Of Micronesia">Federated States Of Micronesia</option>
                    <option value="Florida">Florida</option>
                    <option value="Georgia">Georgia</option>
                    <option value="Guam">Guam</option>
                    <option value="Hawaii">Hawaii</option>
                    <option value="Idaho">Idaho</option>
                    <option value="Illinois">Illinois</option>
                    <option value="Indiana">Indiana</option>
                    <option value="Iowa">Iowa</option>
                    <option value="Kansas">Kansas</option>
                    <option value="Kentucky">Kentucky</option>
                    <option value="Louisiana">Louisiana</option>
                    <option value="Maine">Maine</option>
                    <option value="Marshall Islands">Marshall Islands</option>
                    <option value="Maryland">Maryland</option>
                    <option value="Massachusetts">Massachusetts</option>
                    <option value="Michigan">Michigan</option>
                    <option value="Minnesota">Minnesota</option>
                    <option value="Mississippi">Mississippi</option>
                    <option value="Missouri">Missouri</option>
                    <option value="Montana">Montana</option>
                    <option value="Nebraska">Nebraska</option>
                    <option value="Nevada">Nevada</option>
                    <option value="New Hampshire">New Hampshire</option>
                    <option value="New Jersey">New Jersey</option>
                    <option value="New Mexico">New Mexico</option>
                    <option value="New York">New York</option>
                    <option value="North Carolina">North Carolina</option>
                    <option value="North Dakota">North Dakota</option>
                    <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                    <option value="Ohio">Ohio</option>
                    <option value="Oklahoma">Oklahoma</option>
                    <option value="Oregon">Oregon</option>
                    <option value="Palau">Palau</option>
                    <option value="Pennsylvania">Pennsylvania</option>
                    <option value="Puerto Rico">Puerto Rico</option>
                    <option value="Rhode Island">Rhode Island</option>
                    <option value="South Carolina">South Carolina</option>
                    <option value="South Dakota">South Dakota</option>
                    <option value="Tennessee">Tennessee</option>
                    <option value="Texas">Texas</option>
                    <option value="Utah">Utah</option>
                    <option value="Vermont">Vermont</option>
                    <option value="Virginia">Virginia</option>
                    <option value="Virgin Islands">Virgin Islands</option>
                    <option value="Washington">Washington</option>
                    <option value="West Virginia">West Virginia</option>
                    <option value="Wisconsin">Wisconsin</option>
                    <option value="Wyoming">Wyoming</option>
                </select></label>
            <label class="select"><span>Country</span><select name="country">
                    <option value="Canada">Canada</option>
                    <option value="United States" selected="selected">United States</option>
                </select></label>
            <div class="students">
                <label class="input"><span># of students</span><input name="students" type="text" value="<?php print $students; ?>"></label>
            </div>
            <div class="payment">
                <label class="input"><span>Payment method</span><select>
                        <option value="">Preferred payment method</option>
                        <option>Credit card</option>
                        <option>Check</option>
                    </select></label>
            </div>
        </fieldset>
        <fieldset id="payment-pane">
            <legend>Payment method</legend>
            <div class="cc-number">
                <label class="input">
                    <span>Card number</span><input name="cc-number" type="text" value="">
                    <div class="cards">
                        <img alt="VISA" src="<?php echo $view->escape($view['assets']->getUrl('bundles/studysauce/images/visa.gif')) ?>" />
                        <img alt="MC" src="<?php echo $view->escape($view['assets']->getUrl('bundles/studysauce/images/mc.gif')) ?>" />
                        <img alt="DISC" src="<?php echo $view->escape($view['assets']->getUrl('bundles/studysauce/images/disc.gif')) ?>" />
                        <img alt="AMEX" src="<?php echo $view->escape($view['assets']->getUrl('bundles/studysauce/images/amex.gif')) ?>" />
                    </div>
                </label>
            </div>
            <div class="cc-month">
                <label class="select"><span>Expiration date</span>
                    <select name="cc-month">
                        <option value="01" selected="selected">01 - January</option>
                        <option value="02">02 - February</option>
                        <option value="03">03 - March</option>
                        <option value="04">04 - April</option>
                        <option value="05">05 - May</option>
                        <option value="06">06 - June</option>
                        <option value="07">07 - July</option>
                        <option value="08">08 - August</option>
                        <option value="09">09 - September</option>
                        <option value="10">10 - October</option>
                        <option value="11">11 - November</option>
                        <option value="12">12 - December</option>
                    </select></label>
            </div>
            <div class="cc-year">
                <label class="select"><span>Expiration year</span>
                    <select name="cc-year">
                        <?php
                        $isFirst = true;
                        for($y = 0; $y < 20; $y++)
                        {
                            ?><option value="<?php print intval(date('y')) + $y; ?>" <?php print ($isFirst ? 'selected="selected"': ''); ?>><?php print intval(date('Y')) + $y; ?></option><?php
                            $isFirst = false;
                        } ?></select></label>
            </div>
            <label class="input"><span>CCV</span><input name="cc-ccv" type="text" value="">
                <a href="#ccv-info" data-toggle="modal">What's the CVV?</a>
            </label>
        </div>
        <div class="form-actions highlighted-link invalid"><a href="#business-order" class="more">Save</a></div>
    </div>
</div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');

$view['slots']->stop();
