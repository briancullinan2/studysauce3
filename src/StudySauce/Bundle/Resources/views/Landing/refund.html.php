<?php
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var $view TimedPhpEngine */
/** @var $user User */

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');

$view['slots']->stop();

$view['slots']->start('javascripts');

$view['slots']->stop();

$view['slots']->start('body'); ?>
<div class="panel-pane" id="refund">
    <div class="pane-content">
        <h2>Refund policy</h2>
        <p>We offer a 30-day Money Back Guarantee. Please email customer service at <a href="mailto:support@studysauce.com">support@studysauce.com</a> within 30 days of purchase date if you are not completely satisfied with our service and we will refund your purchase.</p>
        <p class="highlighted-link"><a href="<?php print $view['router']->generate('_welcome'); ?>" class="more">Go home</a></p>
    </div>
</div>
<?php $view['slots']->stop(); ?>
