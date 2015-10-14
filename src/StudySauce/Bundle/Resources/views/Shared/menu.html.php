<?php use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var @var User $user */
$user = $app->getUser();
?>

<aside id="right-panel" class="collapsed">
    <nav>
        <a href="#expand"><span class="navbar-toggle">Study Tools</span></a>
        <ul class="main-menu">
            <li><a href="#collapse">Hide</a><h3>Study Tools</h3></li>
            <li><a href="<?php print $view['router']->generate('home'); ?>"><span>&nbsp;</span>Home</a></li>
            <li><a href="<?php print $view['router']->generate('account'); ?>"><span>&nbsp;</span>Account settings</a></li>
        </ul>
    </nav>
</aside>