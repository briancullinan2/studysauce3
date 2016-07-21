<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;

$context = !empty($context) ? $context : jQuery($this);
$tab = $context->filter('.panel-pane');


$parentVal = $tab->find('.parent select')->val();
$year = $tab->find('.year select');
$yearVal = $year->val();
$school = $tab->find('._code select');
$schoolVal = $school->val();

/** @var Invite $invite */
if(!empty($invite)) {
    $yearVal = $invite->getGroup()->getParent()->getId();
    $schoolVal = $invite->getCode();
}

if(isset($invites)) {
    $publicGroups = [];
    $visited = [];
    $groupStr = '';
    foreach ($invites as $i) {
        /** @var Invite $i */
        $group = $i->getGroup();
        do {
            $publicGroups[count($publicGroups)] = $group;
            $hasParent = true;
            if (empty($group->getParent()) || $group->getParent()->getId() == $group->getId()) {
                $hasParent = false;
                if (!$group->getDeleted() && !in_array($group->getId(), $visited)) {
                    $groupStr = implode('', [$groupStr, '<option value="' , $group->getId() , '"' , $parentVal == $group->getId() ? 'selected="selected"' : '' , '>' , $group->getName() , '</option>']);
                }
            }
            $visited[count($visited)] = $group->getId();
            if (!empty($group->getParent()) && $group->getParent()->getId() != $group->getId()) {
                if(!empty($invite) && $group->getId() == $yearVal) {
                    $parentVal = $group->getParent()->getId();
                }
                $group = $group->getParent();
            }
        } while ($hasParent && !in_array($group->getId(), $visited));
    }
}

$yearStr = '';
$schoolStr = '';
$visited = [];
$codes = [];
foreach($publicGroups as $group) {
    /** @var Group $group */
    if(!empty($group->getParent()) && !$group->getParent()->getDeleted() && !in_array($group->getId(), $visited)) {

        $visited[count($visited)] = $group->getId();
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