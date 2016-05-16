<?php
use StudySauce\Bundle\Entity\Group;
/** @var Group $ss_group */
?>
<label>
    <select name="parent">
        <option value="<?php print ($ss_group->getId()); ?>" <?php print (empty($ss_group->getParent()) ? 'selected="selected"' : ''); ?>>No parent</option>
        <?php
        $topGroups = [];
        foreach($results['allGroups'] as $g) {
            /** @var Group $g */
            if(empty($g->getParent())) {
                $topGroups[count($topGroups)] = $g;
            }
        }
        print ($view->render('AdminBundle:Admin:cell-parentOptions-ss_group.html.php', ['groups' => $topGroups, 'ss_group' => $ss_group])); ?>
    </select>
</label>
