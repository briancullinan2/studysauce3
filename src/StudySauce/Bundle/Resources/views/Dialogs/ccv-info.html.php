<?php

$view->extend('StudySauceBundle:Dialogs:dialog.html.php');

$view['slots']->start('modal-header') ?>
What is the CVV?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<p>CVV stands for Card Verification Value. This number is used as a security feature to protect you from credit card
    fraud. Finding the number on your card is a very simple process. Just follow the directions below.</p>

<br><b>Visa, MasterCard, Discover:</b>
<p>The CVV for these cards is found on the back side of the card. It is only the last three digits on the far right of the
    signature panel box.</p><br>

<p><b>American Express:</b></p>
<p>The CVV on American Express cards is found on the front of the card. It is a four digit number printed in smaller text on
    the right side above the credit card number.</p>

<?php $view['slots']->stop();
