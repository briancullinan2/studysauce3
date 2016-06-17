<?php

use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use DateTime as Date;

/** @var GlobalVariables $app */

$request = $app->getRequest();

$total = 0;
$wrong = 0;
$cardId = 0;
$retention = isset($results['user_pack'][0]) ? [$results['user_pack'][0]] : [];
if(isset($results['user_pack'][0]) && $request->cookies->get('retention_shuffle') == 'true') {
    // TODO: count all cards
    $retention = $results['user_pack'][0]->getUser()->getUserPacks()->toArray();
}

foreach($retention as $up) {
    /** @var UserPack $up */
    if ($up->getRemoved() || $up->getPack()->getStatus() == 'DELETED' || $up->getPack()->getStatus() == 'UNPUBLISHED') {
        continue;
    }
    foreach ($up->getRetention() as $id => $r) {
        if ($r[2]) {
            $wrong += 1;
            $cardId = $id;
        }
        if (new Date($r[3]) > new Date($request->cookies->get('retention'))) {
            $total += 1;
        }
    }
}

?>
<h2>You scored</h2>
<h1><?php print ($total > 0 ? round(($total - $wrong) / $total * 100) : 0); ?>%</h1>
<?php if($wrong > 0) { ?>
    <h3>Go back through what you missed?</h3>
    <div class="preview-footer">
        <a href="<?php print ($view['router']->generate('home')); ?>" class="preview-wrong">✘</a>
        <div class="preview-guess">&nbsp;</div>
        <a href="<?php print ($view['router']->generate('cards', ['card' => $cardId])); ?>" class="preview-right">&#x2714;︎</a>
    </div>
<?php }
else { ?>
    <h3>Congratulations!<br />You answered all of today&rsquo;s questions correctly.</h3>
    <div class="preview-footer">
        <a href="<?php print ($view['router']->generate('home')); ?>" class="btn">Go home</a>
    </div>
<?php }
