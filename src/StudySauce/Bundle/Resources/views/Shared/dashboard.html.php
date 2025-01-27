
<?php
/** @var $view \Symfony\Bundle\FrameworkBundle\Templating\PhpEngine */
use StudySauce\Bundle\Entity\User;

/** @var $router \Symfony\Component\Routing\Router */
$router = $this->container->get('router');

/** @var $collection \Symfony\Component\Routing\RouteCollection */
$collection = $router->getRouteCollection();

/** @var $app \Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables */
$request = $app->getRequest();
if(empty($request->get('_route')) && $request->get('_format') == 'tab') {
    /** @var $router \Symfony\Component\Routing\Router */
    $router = $this->container->get('router');
    /** @var $collection \Symfony\Component\Routing\RouteCollection */
    $collection = $router->getRouteCollection();
    if(strpos($request->get('_controller'), '::') === false) {
        $parser = new \Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser(
            $this->container->get('kernel')
        );
        $name = $parser->parse($request->get('_controller'));
    }
    else {
        $name = $request->get('_controller');
    }
    foreach ($collection->all() as $route => $params) {
        /** @var $params \Symfony\Component\Routing\Route */
        if ($params->getDefault('_controller') == $name &&
            strpos($params->getRequirement('_format'), 'tab') > -1) {
            $pane = $route;
            break;
        }
    }
}
$hasSubKey = false;
if(!empty($request->get('_route'))) {
    $pane = explode('_', $request->get('_route'))[0];
    $route = $collection->get($request->get('_route'));

    foreach($route->getRequirements() as $r => $regex) {
        if ($r != '_format') {
            $pane .= '-' . $r . $request->attributes->get('_route_params')[$r];
            $hasSubKey = true;
        }
    }
    if(!$hasSubKey) {
        $pane = $request->get('_route');
    }
}
if($app->getRequest()->get('_format') == 'index' || $app->getRequest()->get('_format') == 'funnel' ||
    $app->getRequest()->get('_format') == 'adviser') {
    $view->extend('StudySauceBundle:Shared:layout.html.php');

    if($app->getRequest()->get('_format') == 'index') {
        $view['slots']->start('classes'); ?>dashboard-home<?php $view['slots']->stop();
    }
    elseif($app->getRequest()->get('_format') == 'funnel') {
        $view['slots']->start('classes'); ?>dashboard-home funnel<?php $view['slots']->stop();
    }
    elseif($app->getRequest()->get('_format') == 'adviser') {
        $view['slots']->start('classes'); ?>dashboard-home adviser<?php $view['slots']->stop();
    }

    $view['slots']->start('tmp-stylesheets');
    $view['slots']->output('stylesheets');
    $view['slots']->stop();
    $view['slots']->start('stylesheets');
    foreach ($view['assetic']->stylesheets(
        [
            '@StudySauceBundle/Resources/public/css/tipsy.css',
            '@StudySauceBundle/Resources/public/css/header.css',
            '@StudySauceBundle/Resources/public/css/menu.css',
            '@StudySauceBundle/Resources/public/css/dashboard.css',
            '@StudySauceBundle/Resources/public/css/footer.css'
        ],
        [],
        ['output' => 'bundles/studysauce/css/*.css']
    ) as $url):
        ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>" />
    <?php endforeach;
    if($app->getUser() instanceof User && $app->getUser()->hasRole('ROLE_ADMIN')) {
        foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/menu.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
            <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
        <?php endforeach;
    }
    $view['slots']->output('tmp-stylesheets');
    if(!empty($pane)) { ?>
        <style type="text/css">.css-loaded.<?php print $pane; ?> {
                content: "loading-<?php print $pane ?>";
            }</style>
        <?php
    }
    $view['slots']->stop();


    if($app->getRequest()->get('_format') == 'index') {
        $view['slots']->start('tmp-body');
        $view['slots']->output('body');
        $view['slots']->stop();
        $view['slots']->start('body');
        echo $view->render('StudySauceBundle:Shared:header.html.php');
        $view['slots']->output('tmp-body');
        $view['slots']->stop();
    }
    elseif($app->getRequest()->get('_format') == 'funnel') {
        $view['slots']->start('tmp-body');
        $view['slots']->output('body');
        $view['slots']->stop();
        $view['slots']->start('body');
        echo $view->render('StudySauceBundle:Shared:header.html.php');
        $view['slots']->output('tmp-body');
        $view['slots']->stop();
    }
    elseif($app->getRequest()->get('_format') == 'adviser') {
        $view['slots']->start('tmp-body');
        $view['slots']->output('body');
        $view['slots']->stop();
        $view['slots']->start('body');
        echo $view->render('StudySauceBundle:Shared:header.html.php');
        $view['slots']->output('tmp-body');
        $view['slots']->stop();
    }


    if($app->getRequest()->get('_format') != 'funnel') {
        $view['slots']->start('tmp-javascripts');
        $view['slots']->output('javascripts');
        $view['slots']->stop();
        $view['slots']->start('javascripts');
        foreach ($view['assetic']->javascripts(['@dashboard_scripts'],[],['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
            <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
        <?php }
        ?><script type="text/javascript" src="<?php print ($view['router']->generate('template')); ?>"></script><?php
        $view['slots']->output('tmp-javascripts');
        $view['slots']->stop();
    }
    else
    {
        $view['slots']->start('tmp-javascripts');
        $view['slots']->output('javascripts');
        $view['slots']->stop();
        $view['slots']->start('javascripts');
        foreach ($view['assetic']->javascripts(['@funnel',],[],['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
            <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
        <?php }
        ?><script type="text/javascript" src="<?php print ($view['router']->generate('template')); ?>"></script><?php
        $view['slots']->output('tmp-javascripts');
        $view['slots']->stop();
    }

    $view['slots']->start('tmp-stylesheets');
    $view['slots']->stop();
    $view['slots']->start('tmp-body');
    $view['slots']->stop();
    $view['slots']->start('tmp-javascripts');
    $view['slots']->stop();

}

if($app->getRequest()->get('_format') == 'tab' && empty($exclude_layout)) {
    $view['slots']->output('stylesheets');
    if(!empty($pane)) { ?>
        <style type="text/css">.css-loaded.<?php print $pane; ?> {
                content: "loading-<?php print $pane ?>";
            }</style>
        <?php
    }

    $view['slots']->output('body');
    $view['slots']->output('javascripts');
    $view['slots']->output('sincludes');

    // clear the templates
    $view['slots']->start('stylesheets');
    $view['slots']->stop();
    $view['slots']->start('body');
    $view['slots']->stop();
    $view['slots']->start('javascripts');
    $view['slots']->stop();
    $view['slots']->start('sincludes');
    $view['slots']->stop();
}

else if(!empty($exclude_layout)) {
    $view['slots']->output('body');

    $view['slots']->start('stylesheets');
    $view['slots']->stop();
    $view['slots']->start('body');
    $view['slots']->stop();
    $view['slots']->start('javascripts');
    $view['slots']->stop();
    $view['slots']->start('sincludes');
    $view['slots']->stop();
}
