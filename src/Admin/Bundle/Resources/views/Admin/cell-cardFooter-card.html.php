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
$appUser = $app->getUser();

$total = [];
$index = 1;
if(isset($results['user_pack'][0])) {
    $retention = [$results['user_pack'][0]];

    // add to user data storage
    $hasUp = false;
    $allUserPacks = $appUser->getUserPacks()->toArray();
    foreach($allUserPacks as $ur => $upr) {
        /** @var UserPack $upr */
        if($results['user_pack'][0]->getPack()->getId() == $upr->getPack()->getId() && !empty($results['user_pack'][0]->getRetention())) {
            $upr->setRetention($results['user_pack'][0]->getRetention());
            $hasUp = true;
        }
    }
    if(!$hasUp) {
        $appUser->userPacks = array_merge($appUser->getUserPacks()->toArray(), [$results['user_pack'][0]]);
    }
    $appUser->userPacks = $allUserPacks;
}
else {
    $retention = [];
}

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

        if($appUser->getId() == $results['user_pack'][0]->getUser()->getId()) {
            // add to user data storage
            foreach($retention as $r => $up) {
                /** @var UserPack $up */
                $hasUp = false;
                $allUserPacks = $appUser->getUserPacks()->toArray();
                foreach($allUserPacks as $ur => $upr) {
                    /** @var UserPack $upr */
                    if($up->getPack()->getId() == $upr->getPack()->getId() && !empty($up->getRetention())) {
                        $upr->setRetention($up->getRetention());
                        $hasUp = true;
                    }
                }
                if(!$hasUp) {
                    $allUserPacks = array_merge($allUserPacks, [$up]);
                }
                $appUser->userPacks = $allUserPacks;
            }
            $retention = $appUser->getUserPacks()->toArray();
        }
    }
}
jQuery('.header')->data('user', $appUser);

foreach($retention as $up) {
    /** @var UserPack $up */
    if($up->getPack()->getStatus() == 'DELETED' || ($up->getPack()->getStatus() == 'UNPUBLISHED' && $up->getPack()->getOwnerId() != $appUser->getId())) {
        continue;
    }
    if($up->getPack()->getStatus() != 'PUBLIC' && (empty($up) || $up->getRemoved())) {
        continue;
    }

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
    }
}

$row->append(implode('', ['<div class="preview-count">', $index, ' of ', count($total), '</div>']));

print ($row->html());