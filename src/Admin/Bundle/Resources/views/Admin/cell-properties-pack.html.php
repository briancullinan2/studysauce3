<?php
use StudySauce\Bundle\Entity\Pack;

/** @var Pack $pack */
?>
<div>
    <label class="input">
        <span>Keyboard type</span><br />
        <select name="keyboard">
            <option value="basic">Normal (default)</option>
            <option value="number" <?php print ($pack->getProperty('keyboard') == 'number' ? 'selected="selected"' : ''); ?>>Numbers only</option>
        </select>
    </label>
</div>