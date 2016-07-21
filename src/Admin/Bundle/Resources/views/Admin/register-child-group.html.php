<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;

$context = !empty($context) ? $context : jQuery($this);


$parentVal = $context->find('.parent select')->val();
$year = $context->find('.year select');
$yearVal = $year->val();
$school = $context->find('._code select');
$schoolVal = $school->val();

/** @var Invite $invite */
if(!empty($invite)) {
    $schoolVal = $invite->getCode();
}

if(isset($invites)) {
    $publicGroups = [];
    $visited = [];
    foreach ($invites as $i) {
        /** @var Invite $i */
        $group = $i->getGroup();
        do {
            if(!in_array($group->getId(), $visited)) {
                $publicGroups[count($publicGroups)] = $group;
                $visited[count($visited)] = $group->getId();
            }
            $hasParent = true;
            if(!empty($invite) && !empty($group->getParent()) && $group->getId() == $invite->getGroup()->getId()) {
                $yearVal = $group->getParent()->getId();
            }
            if (empty($group->getParent()) || $group->getParent()->getId() == $group->getId()) {
                $hasParent = false;
            }
            else {
                $group = $group->getParent();
            }
        } while ($hasParent && !in_array($group->getId(), $visited));
    }
}
foreach($publicGroups as $group) {
    /** @var Group $group */
    if(!empty($invite) && !empty($group->getParent()) && $group->getId() == $yearVal) {
        $parentVal = $group->getParent()->getId();
    }
}

$groupStr = '';
$yearStr = '';
$schoolStr = '';
$codes = [];
foreach($publicGroups as $group) {
    /** @var Group $group */
    if($group->getDeleted()) {
        continue;
    }
    if (empty($group->getParent()) || $group->getParent()->getId() == $group->getId()) {
        $groupStr = implode('', [$groupStr, '<option value="' , $group->getId() , '"' , $parentVal == $group->getId() ? 'selected="selected"' : '' , '>' , $group->getName() , '</option>']);
    }
    if(!empty($group->getParent()) && !$group->getParent()->getDeleted()) {
        if($group->getParent()->getId() == $parentVal) {
            $yearStr = implode('', [$yearStr, '<option value="' , $group->getId() , '" ' , $yearVal == $group->getId() ? 'selected="selected"' : '' , '>' , $group->getName() , '</option>']);
        }
        if($group->getParent()->getId() == $yearVal) {
            $code = $group->getId();
            foreach($invites as $i) {
                /** @var Invite $i */
                if($i->getGroup()->getId() == $group->getId()) {
                    $code = $i->getCode();
                    break;
                }
            }
            $codes[count($codes)] = $code;
            $schoolStr = implode('', [$schoolStr, '<option value="' , $code , '" ' , $schoolVal == $code ? 'selected="selected"' : '' , '>' , $group->getName() , '</option>']);
        }
    }
}
foreach($invites as $i) {
    /** @var Invite $i */
    if($i->getGroup()->getId() == $yearVal && !in_array($i->getCode(), $codes)) {
        $schoolStr = implode('', [$schoolStr, '<option value="' , $i->getCode() , '"' , $schoolVal == $i->getCode() ? 'selected="selected"' : '' , '>' , $i->getGroup()->getName() , '</option>']);
    }
}

// update list of groups
$year->find('option:not(:first-of-type)')->remove();
$year->append($yearStr)->val($yearVal);

$school->find('option:not(:first-of-type)')->remove();
$school->append($schoolStr)->val($schoolVal);

?>

<label class="input parent"><select name="parent">
        <option value="">- Select child&rsquo;s school system -</option>
        <?php print ($groupStr); ?>
    </select></label>
<label class="input year"><select name="year">
        <option value="">- Select child&rsquo;s school year -</option>
        <?php print ($yearStr); ?>
    </select></label>
<label class="input _code"><select name="_code">
        <option value="">- Select child&rsquo;s school name -</option>
        <?php print ($schoolStr); ?>
    </select>
</label>