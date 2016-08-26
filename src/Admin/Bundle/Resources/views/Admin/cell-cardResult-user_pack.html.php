<?php

use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use DateTime as Date;

/** @var GlobalVariables $app */
// check if we need to update or create template
$row = !empty($context) ? $context : jQuery($this);

$httpRequest = $app->getRequest();

/** @var UserPack $user_pack */

$total = 0;
$wrong = 0;
$cardId = 0;
$retention = [$user_pack];

$isSummary = $httpRequest->cookies->get('retention_summary') == 'true';
if($isSummary) {
    $retentionDate = $httpRequest->cookies->get(implode('', ['retention_', $user_pack->getPack()->getId()]));
}
else {
    $retentionDate = $httpRequest->cookies->get('retention');
    if(empty($request['skipRetention']) && $httpRequest->cookies->get('retention_shuffle') == 'true') {
        // TODO: count all cards
        if(isset($user_pack)) {
            $retention = array_merge($retention, $user_pack->getUser()->getUserPacks()->toArray());
        }
        if($app->getUser()->getId() == $user_pack->getUser()->getId()) {
            $retention = $app->getUser()->getUserPacks()->toArray();
        }
    }
}

foreach($retention as $up) {
    /** @var UserPack $up */
    if ($up->getRemoved() || $up->getPack()->getStatus() == 'DELETED' || $up->getPack()->getStatus() == 'UNPUBLISHED') {
        continue;
    }
    $hasUp = false;
    $allUserPacks = $app->getUser()->getUserPacks()->toArray();
    foreach($allUserPacks as $ur => $upr) {
        /** @var UserPack $upr */
        if($up->getPack()->getId() == $upr->getPack()->getId()) {
            $upr->setRetention($up->getRetention());
            $hasUp = true;
        }
    }
    if(!$hasUp) {
        $appUser = $app->getUser();
        $appUser->userPacks = array_merge($app->getUser()->getUserPacks()->toArray(), [$up]);
        jQuery('.header')->data('user', $appUser);
    }
    foreach ($up->getRetention() as $id => $r) {
        if (new Date($r[3]) > new Date($httpRequest->cookies->get('retention'))) {
            $total += 1;
            if ($r[2]) {
                $wrong += 1;
                $cardId = $id;
            }
        }
    }
}

$view['slots']->start('card-results-page'); ?>
<h2>You scored</h2>
<h1><?php print ($total > 0 ? round(($total - $wrong) / $total * 100) : 0); ?>%</h1>
<?php if($wrong > 0) { ?>
    <h3>Go back through what you missed?</h3>
    <div class="preview-footer">
        <a href="<?php print ($view['router']->generate('home')); ?>" class="preview-wrong">&#x2717;</a>
        <div class="preview-guess">&nbsp;</div>
        <a href="<?php print ($view['router']->generate('cards', ['card' => $cardId])); ?>" class="preview-right">&#x2713;︎</a>
    </div>
<?php }
else { ?>
    <h3>Congratulations!<br />You answered all of today&rsquo;s questions correctly.</h3>
    <div class="preview-footer">
        <a href="<?php print ($view['router']->generate('home')); ?>" class="btn">Go home</a>
    </div>
<?php }
$view['slots']->stop();

$row->append($view['slots']->get('card-results-page'));

print ($row->html());