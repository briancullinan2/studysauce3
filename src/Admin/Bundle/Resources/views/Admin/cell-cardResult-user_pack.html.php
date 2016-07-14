<?php

use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use DateTime as Date;

/** @var GlobalVariables $app */
// check if we need to update or create template
$row = !empty($context) ? $context : jQuery($this);

$httpRequest = $app->getRequest();

$total = 0;
$wrong = 0;
$cardId = 0;
$retention = isset($results['user_pack'][0]) ? [$results['user_pack'][0]] : [];
$remaining = [];

$isSummary = $httpRequest->cookies->get('retention_summary') == 'true';
if($isSummary) {
    $retentionDate = $httpRequest->cookies->get(implode('', ['retention_', $card->getPack()->getId()]));
}
else {
    $retentionDate = $httpRequest->cookies->get('retention');
    if(empty($request['skipRetention']) && $httpRequest->cookies->get('retention_shuffle') == 'true') {
        // TODO: count all cards
        if(isset($results['user_pack'][0])) {
            $retention = array_merge($retention, $results['user_pack'][0]->getUser()->getUserPacks()->toArray());
        }
        if($app->getUser()->getId() == $results['user_pack'][0]->getUser()->getId()) {
            foreach($retention as $r => $up) {
                /** @var UserPack $up */
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
                    jQuery('#welcome-message')->data('user', $appUser);
                }
            }
            $retention = $app->getUser()->getUserPacks()->toArray();
        }
    }
}

$retentionObj = [];
foreach($retention as $up) {
    /** @var UserPack $up */
    if ($up->getRemoved() || $up->getPack()->getStatus() == 'DELETED' || $up->getPack()->getStatus() == 'UNPUBLISHED') {
        continue;
    }
    $retentionObj[count($retentionObj)] = AdminController::toFirewalledEntityArray($up, $request['tables'], 1);
    foreach ($up->getRetention() as $id => $r) {
        if (new Date($r[3]) > new Date($httpRequest->cookies->get('retention'))) {
            $total += 1;
            if ($r[2]) {
                $wrong += 1;
                $cardId = $id;
                $remaining[count($remaining)] = $id;
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

$row->find('h1')->attr('data-remaining', json_encode($remaining))->data('remaining', $remaining);
$row->find('h1')->attr('data-retention', json_encode($retentionObj))->data('retention', $retentionObj);

print ($row->html());