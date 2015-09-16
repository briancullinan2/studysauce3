<?php
/** @var User $user */
use StudySauce\Bundle\Entity\User;

$user = $app->getUser();
?>
<aside id="right-panel" class="collapsed">
    <nav>
        <a href="#expand"><span class="navbar-toggle">Adviser Tools</span></a>
        <ul class="main-menu">
            <li><a href="#collapse">Hide</a><h3>Adviser Tools</h3></li>
            <li><a href="<?php print $view['router']->generate('userlist'); ?>"><span>&nbsp;</span>Home</a></li>
            <li><a href="<?php print $view['router']->generate('deadlines'); ?>"><span>&nbsp;</span>Deadlines</a></li>
            <li><a href="<?php print $view['router']->generate('import'); ?>"><span>&nbsp;</span>User Import</a></li>
            <li><a href="<?php print $view['router']->generate('account'); ?>"><span>&nbsp;</span>Account settings</a></li>
        </ul>
    </nav>
</aside>