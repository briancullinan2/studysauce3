<label>
    <select name="group">
        <option value="">Group</option>
        <option value="_ascending">Ascending (A-Z)</option>
        <option value="_descending">Descending (Z-A)</option>
        <?php foreach ($groups as $i => $g) {
            /** @var Group $g */
            ?>
            <option
            value="<?php print $g->getId(); ?>"><?php print $g->getName(); ?></option><?php
        } ?>
        <option value="nogroup">No Groups</option>
    </select></label>