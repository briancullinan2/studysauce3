<?php
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="error404">
        <div class="pane-content">
            <?php foreach ($view['assetic']->image(['@StudySauceBundle/Resources/public/images/not-found_compressed.png'], [], ['output' => 'bundles/studysauce/images/*']) as $url): ?>
                <img src="<?php echo $view->escape($url) ?>" alt="404" />
            <?php endforeach; ?>
            <h3 style="text-align:center; font-size:56px;">Page not found.  Go back to the <a href="<?php print $view['router']->generate('home'); ?>">homepage</a>.</h3>
        </div>
    </div>
<?php $view['slots']->stop();
