<?php
/** @var User $user */
use StudySauce\Bundle\Entity\User;

$user = $app->getUser();
?>
<aside id="right-panel" class="collapsed">
    <nav>
        <a href="#expand"><span class="navbar-toggle">Admin Tools</span></a>
        <ul class="main-menu">
            <li><a href="#collapse">Hide</a><h3>Admin Tools</h3></li>
            <li><a href="<?php print $view['router']->generate('command_control'); ?>"><span>&nbsp;</span>Home</a></li>
            <li><a href="<?php print $view['router']->generate('packs'); ?>"><span>&nbsp;</span>Packs</a></li>
            <li><a href="<?php print $view['router']->generate('import'); ?>"><span>&nbsp;</span>User Import</a></li>
            <li><a href="<?php print $view['router']->generate('emails'); ?>"><span>&nbsp;</span>Emails</a></li>
            <li><a href="<?php print $view['router']->generate('validation'); ?>"><span>&nbsp;</span>Validation</a></li>
            <li><a href="<?php print $view['router']->generate('activity'); ?>"><span>&nbsp;</span>Recent activity</a></li>
            <li><a href="<?php print $view['router']->generate('results'); ?>"><span>&nbsp;</span>Results</a></li>
            <li><a href="<?php print $view['router']->generate('account'); ?>"><span>&nbsp;</span>Account settings</a></li>
        </ul>
    </nav>
</aside>