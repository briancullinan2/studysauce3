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
$hasYear = false;
$hasSchool = false;
foreach($publicGroups as $group) {
    /** @var Group $group */
    if($group->getDeleted()) {
        continue;
    }
    if (empty($group->getParent()) || $group->getParent()->getId() == $group->getId()) {
        $hasYear |= $parentVal == $group->getId();
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
            $hasSchool |= $schoolVal == $code;
            $schoolStr = implode('', [$schoolStr, '<option value="' , $code , '" ' , $schoolVal == $code ? 'selected="selected"' : '' , '>' , $group->getName() , '</option>']);
        }
    }
}
foreach($invites as $i) {
    /** @var Invite $i */
    if($i->getGroup()->getId() == $yearVal && !in_array($i->getCode(), $codes)) {
        $hasSchool |= $schoolVal == $i->getCode();
        $schoolStr = implode('', [$schoolStr, '<option value="' , $i->getCode() , '"' , $schoolVal == $i->getCode() ? 'selected="selected"' : '' , '>' , $i->getGroup()->getName() , '</option>']);
    }
}

// update list of groups
$year->find('option:not(:first-of-type)')->remove();
$year->append($yearStr);
if($hasYear) {
    $year->val($yearVal);
}
else {
    $year->val('');
}

$school->find('option:not(:first-of-type)')->remove();
$school->append($schoolStr);
if($hasSchool) {
    $school->val($schoolVal);
}
else {
    $school->val('');
}

?>

<label class="input select parent"><span>School system</span>
    <select name="parent" placeholder="School system">
        <option value="">- Select child&rsquo;s school system -</option>
        <?php print ($groupStr); ?>
    </select></label>
<label class="input select year"><span>Grade</span>
    <select name="year" placeholder="School year">
        <option value="">- Select child&rsquo;s school year -</option>
        <?php print ($yearStr); ?>
    </select></label>
<label class="input select _code"><span>School</span>
    <select name="_code" placeholder="School name">
        <option value="">- Select child&rsquo;s school name -</option>
        <?php print ($schoolStr); ?>
    </select>
</label>