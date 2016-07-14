<?php

use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use DateTime as Date;

/** @var Card $card */
/** @var GlobalVariables $app */
$httpRequest = $app->getRequest();
// check if we need to update or create template
$row = !empty($context) ? $context : jQuery($this);

$total = [];
$remaining = [];
$index = 1;
$retention = isset($results['user_pack'][0]) ? [$results['user_pack'][0]] : [];

$isSummary = $httpRequest->cookies->get('retention_summary') == 'true';
if($isSummary) {
    $retentionDate = $httpRequest->cookies->get(implode('', ['retention_', $request['pack-id']]));
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
                    jQuery('.header')->data('user', $appUser);
                }
            }
            $retention = $app->getUser()->getUserPacks()->toArray();
        }
    }
}

$retentionObj = [];
foreach($retention as $up) {
    /** @var UserPack $up */
    if($up->getRemoved() || $up->getPack()->getStatus() == 'DELETED' || $up->getPack()->getStatus() == 'UNPUBLISHED') {
        continue;
    }
    $retentionObj[count($retentionObj)] = AdminController::toFirewalledEntityArray($up, $request['tables'], 1);
    foreach($up->getRetention() as $id => $r) {
        if($isSummary || empty($r[3]) || new Date($retentionDate) < new Date($r[3]) || $r[2]) {
            $total[count($total)] = $id;
        }
        if(implode('', ['', $id]) == implode('', ['', $card->getId()])) {
            continue;
        }
        if(!empty($r[3]) && new Date($r[3]) > new Date($retentionDate)) {
            $index += 1;
        }
        else if ($isSummary || $r[2]) {
            $remaining[count($remaining)] = $id;
        }
    }
}

$row->append(implode('', ['<div class="preview-count">', $index, ' of ', count($total), '</div>']));
$row->find('.preview-count')->attr('data-remaining', json_encode($remaining))->data('remaining', $remaining);
$row->find('.preview-count')->attr('data-retention', json_encode($retentionObj))->data('retention', $retentionObj);

print ($row->html());