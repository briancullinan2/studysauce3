<?php
use StudySauce\Bundle\Entity\Coupon;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\HttpKernel\Controller\ControllerReference;
/** @var GlobalVariables $app */
/** @var User $user */
$user = $app->getUser();

$isPartner =
    $app->getSession()->has('parent') || $app->getUser()->hasRole('ROLE_PARENT') ||
    $app->getSession()->has('partner') || $app->getUser()->hasRole('ROLE_PARTNER') ||
    // invite information is autofilled
    !empty($studentfirst);

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/buy.css'],[],['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/buy.js'],[],['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane funnel" id="checkout">
        <div class="pane-content clearfix">
            <fieldset id="billing-pane">
                <legend>Billing information</legend>
                <div class="first-name">
                    <label class="input"><span>First name</span><input name="first-name" type="text" value="<?php print $first; ?>"></label>
                </div>
                <div class="last-name">
                    <label class="input"><span>Last name</span><input name="last-name" type="text" value="<?php print $last; ?>"></label>
                </div>
                <div class="email">
                    <label class="input"><span>E-mail address</span><input name="email" type="text" value="<?php print $email; ?>"></label>
                </div>
                <?php if(!is_object($user) || $user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO')) { ?>
                    <div class="password">
                        <label class="input"><span>Password</span><input name="password" type="password" value=""></label>
                    </div>
                <?php } ?>
                <label class="input"><span>Street address</span><input name="street1" type="text" value=""></label>
                <label class="input"><input name="street2" type="text" value=""></label>
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
                <a href="#show-coupon" class="cloak">Have a coupon code? Click <span class="reveal">here</span>.</a>
                <a href="#show-gift" class="cloak" <?php print ($isPartner ? 'style="display:none;"' : ''); ?>>Are you purchasing as a gift? Click <span class="reveal">here</span>.</a>
            </fieldset>
            <fieldset id="payment-pane">
                <legend>Payment method</legend>
                <div class="product-option">
                    <?php /** @var Coupon $coupon */
                    if(empty($coupon) || empty($options = $coupon->getOptions())) {
                        $options = \StudySauce\Bundle\Controller\BuyController::$defaultOptions;
                    }
                    $first = true;
                    foreach($options as $o => $option) {
                        ?><label class="radio">
                            <input name="reoccurs" type="radio" value="<?php print $o; ?>" <?php
                            print ($first || empty($option) || $option == $o ? 'checked="checked"' : ''); ?>>
                            <i></i>
                            <span><?php print $option['description']; ?></span>
                        </label><?php
                        $first = false;
                    }
                    if(!empty($coupon)) {
                        ?><div class="line-item"><?php print $coupon->getDescription();; ?></div><?php
                    } ?>
                </div>
                <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/money_back_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                    <img src="<?php echo $view->escape($url) ?>" />
                <?php endforeach; ?>
                <div class="cc-number">
                    <label class="input">
                        <span>Card number</span><input name="cc-number" type="text" value="">
                        <div class="cards">
                            <img alt="VISA" src="<?php echo $view->escape($view['assets']->getUrl('bundles/studysauce/images/visa.gif')) ?>" />
                            <img alt="MC" src="<?php echo $view->escape($view['assets']->getUrl('bundles/studysauce/images/mc.gif')) ?>" />
                            <img alt="DISC" src="<?php echo $view->escape($view['assets']->getUrl('bundles/studysauce/images/disc.gif')) ?>" />
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
            </fieldset>
            <div class="form-actions highlighted-link invalid"><a href="#submit-order" class="more">Complete order</a></div>
            <fieldset id="gift-pane" class="<?php print ($isPartner ? 'shown-by-default' : ''); ?>">
                <legend>Student information</legend>
                <div class="first-name">
                    <label class="input"><span>First name</span><input name="first-name" type="text" value="<?php print $studentfirst; ?>"></label>
                </div>
                <div class="last-name">
                    <label class="input"><span>Last name</span><input name="last-name" type="text" value="<?php print $studentlast; ?>"></label>
                </div>
                <div class="email">
                    <label class="input"><span>E-mail address</span><input name="email" type="text" value="<?php print $studentemail; ?>"></label>
                </div>
            </fieldset>
            <fieldset id="coupon-pane">
                <legend>Coupon discount</legend>
                <?php if(!empty($coupon)) { ?>
                        <div class="coupon-code"><strong><?php print $coupon->getName(); ?> - </strong><?php print $coupon->getDescription(); ?></div>
                        <a href="#coupon-remove" class="more">Remove</a>
                <?php } else { ?>
                    <div class="coupon-code">
                        <label class="input"><input name="coupon-code" type="text" placeholder="Enter code" value=""></label>
                    </div>
                    <a href="#coupon-apply" class="more">Apply to order</a>
                <?php } ?>
            </fieldset>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
echo $view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'ccv-info']), ['strategy' => 'sinclude']);
$view['slots']->stop();
