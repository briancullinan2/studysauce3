<?php
use StudySauce\Bundle\Entity\Coupon;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var GlobalVariables $app */
/** @var User $user */
$user = $app->getUser();

$context = !empty($context) ? $context : jQuery($this);
$tab = $context->filter('.panel-pane');

$isPartner =
    $app->getSession()->has('parent') || $app->getUser()->hasRole('ROLE_PARENT') ||
    $app->getSession()->has('partner') || $app->getUser()->hasRole('ROLE_PARTNER') ||
    // invite information is autofilled
    !empty($studentfirst);

if ($tab->length == 0) {

    $view->extend('StudySauceBundle:Shared:dashboard.html.php');

    $view['slots']->start('stylesheets');
    foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/buy.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url) { ?>
        <link type="text/css" rel="stylesheet" href="<?php print ($view->escape($url)); ?>"/>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('javascripts');
    foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/buy.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
        <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('body'); ?>
    <div class="panel-pane funnel" id="checkout">
        <div class="pane-content clearfix">
            <form action="<?php print ($view['router']->generate('checkout_pay')); ?>" method="post">
                <fieldset id="billing-pane">
                    <legend>Billing information</legend>
                    <label class="input first"><span>First name</span><input name="first" type="text"
                                                                       value="<?php print ($first); ?>"></label>
                    <label class="input last"><span>Last name</span><input name="last" type="text"
                                                                      value="<?php print ($last); ?>"></label>
                    <label class="input email"><span>E-mail address</span><input name="email" type="text"
                                                                           value="<?php print ($email); ?>"></label>
                    <?php if (!is_object($user) || $user->hasRole('ROLE_GUEST') || $user->hasRole('ROLE_DEMO')) { ?>
                        <label class="input password"><span>Password</span><input name="password" type="password"
                                                                         value=""></label>
                    <?php } ?>
                    <label class="input street1"><span>Street address</span><input name="street1" type="text" value=""></label>
                    <label class="input street2"><input name="street2" type="text" value=""></label>
                    <label class="input city"><span>City</span><input name="city" type="text" value=""></label>
                    <label class="input zip"><span>Postal code</span><input name="zip" type="text" value=""></label>
                    <label class="input state"><span>State/Province</span><select name="state">
                            <option value="" selected="selected">- Select state -</option>
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
                    <label class="input country"><span>Country</span><select name="country">
                            <option value="Canada">Canada</option>
                            <option value="United States" selected="selected">United States</option>
                        </select></label>
                </fieldset>
                <fieldset id="payment-pane">
                    <legend>Payment method</legend>
                    <?php
                    $request = (array)(new stdClass());
                    $request['count-file'] = -1;
                    $request['count-pack'] = -1;
                    $request['count-coupon'] = 0;
                    $request['count-card'] = -1;
                    $request['count-ss_group'] = -1;
                    $request['count-ss_user'] = 1;
                    $request['count-user_pack'] = -1;
                    $request['read-only'] = false;
                    $request['inCartOnly'] = true;
                    $request['tables'] = (array)(new stdClass());
                    $request['tables']['file'] = ['id', 'url'];
                    $request['tables']['coupon'] = ['idTilesSummary' => ['id', 'name', 'description', 'packs', 'options']];
                    $request['tables']['ss_group'] = ['id', 'name', 'users', 'deleted'];
                    $request['tables']['ss_user'] = ['id' => ['id', 'first', 'last', 'userPacks']];
                    $request['tables']['user_pack'] = ['pack', 'removed', 'downloaded'];
                    $request['tables']['card'] = ['id', 'deleted'];
                    $request['tables']['pack'] = ['idTilesSummary' => ['created', 'id', 'title', 'logo'], 'actions' => ['cards', 'status']];
                    $request['classes'] = [];
                    $request['headers'] = false;
                    $request['footers'] = ['coupon' => 'cart'];
                    if ($tab->length == 0) {
                        print ($view['actions']->render(new ControllerReference('AdminBundle:Admin:results', $request)));
                    } ?>
                    <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/money_back_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url) { ?>
                        <img src="<?php print ($view->escape($url)); ?>"/>
                    <?php } ?>
                    <label class="input number">
                        <span>Card number</span>
                        <input name="number" type="text" value="">
                        <span class="cards">
                            <img alt="VISA"
                                 src="<?php print ($view->escape($view['assets']->getUrl('bundles/studysauce/images/visa.gif'))); ?>"/>
                            <img alt="MC"
                                 src="<?php print ($view->escape($view['assets']->getUrl('bundles/studysauce/images/mc.gif'))); ?>"/>
                            <img alt="DISC"
                                 src="<?php print ($view->escape($view['assets']->getUrl('bundles/studysauce/images/disc.gif'))); ?>"/>
                        </span>
                    </label>
                    <label class="input month"><span>Expiration date</span>
                        <select name="month">
                            <option value="" selected="selected">- Select month -</option>
                            <option value="01">01 - January</option>
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
                    <label class="input year"><span>Expiration year</span>
                        <select name="year">
                            <option value="" selected="selected">- Select year -</option>
                            <?php
                            for ($y = 0; $y < 20; $y++) { ?>
                                <option
                                value="<?php print (intval(date('y')) + $y); ?>"><?php print (intval(date('Y')) + $y); ?></option><?php
                            } ?></select></label>
                    <label class="input ccv"><span>CCV</span><input name="ccv" type="text" value="">
                        <a href="#ccv-info" data-toggle="modal">What&rsquo;s the CVV?</a>
                    </label>
                </fieldset>
                <input type="hidden" name="csrf_token" value="<?php print ($csrf_token); ?>"/>
                <div class="form-actions highlighted-link invalid">
                    <div class="invalid-error">You must complete all fields before moving on.</div>
                    <button type="submit" value="#save-payment" class="more">Complete order</button>
                </div>
            </form>
        </div>
    </div>
    <?php $view['slots']->stop();

    $view['slots']->start('sincludes');
    print ($view['actions']->render(new ControllerReference('StudySauceBundle:Dialogs:deferred', ['template' => 'ccv-info']), ['strategy' => 'sinclude']));
    $view['slots']->stop();
}