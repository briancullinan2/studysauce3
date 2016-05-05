<?php
use StudySauce\Bundle\Entity\Pack;

/** @var Pack $pack */
?>
<div>
    <label class="input">
        <select name="properties[keyboard]">
            <option value="basic">Normal (default)</option>
            <option value="number" <?php print ($pack->getProperty('keyboard') == 'number' ? 'selected="selected"' : ''); ?>>Numbers only</option>
        </select>
    </label>
</div>