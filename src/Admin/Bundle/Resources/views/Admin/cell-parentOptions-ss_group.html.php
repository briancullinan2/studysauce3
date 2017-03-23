<?php
use StudySauce\Bundle\Entity\Group;
/** @var Group $ss_group */


foreach ($groups as $g) {
    /** @var Group $g */
    ?>
    <option
        value="<?php print ($g->getId()); ?>" <?php print (!empty($ss_group->getParent()) && $g->getId() == $ss_group->getParent()->getId() ? 'selected="selected"' : ''); ?>><?php print ($view->escape($g->getName())); ?></option>
    <?php
    $topGroups = [];
    foreach($results['allGroups'] as $sub) {
        /** @var Group $sub */
        if(!$sub->getDeleted() && !empty($sub->getParent()) && $sub->getParent()->getId() == $g->getId()) {
            $topGroups[count($topGroups)] = $sub;
        }
    }
    if (count($topGroups) > 0) { ?>
        <optgroup label="<?php print ($view->escape($g->getName())); ?> Group">
            <?php print ($view->render('AdminBundle:Admin:cell-parentOptions-ss_group.html.php', ['groups' => $topGroups, 'ss_group' => $ss_group, 'results' => $results])); ?>
        </optgroup>
        <?php
    }
}
