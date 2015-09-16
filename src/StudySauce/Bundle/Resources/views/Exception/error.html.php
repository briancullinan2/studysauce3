<?php
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');
/** @var GlobalVariables $app */
/** @var User $user */
$user = $app->getUser();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="error">
        <div class="pane-content">
            <h3 style="text-align:center; font-size:56px;">Sorry, an error occurred.  Go back to the <a href="<?php print $view['router']->generate('_welcome'); ?>">homepage</a>.</h3>
            <?php if($app->getEnvironment() == 'dev') {
                print sprintf('Uncaught PHP Exception %s: "%s" at %s line %s', get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine());
            } ?>
        </div>
    </div>
<?php $view['slots']->stop();
