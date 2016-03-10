<?php
use StudySauce\Bundle\Entity\Pack;

/** @var Pack $pack */
?>
<div class="<?php print strtolower($pack->getStatus()); ?>">
    <label class="input status">
        <select name="keyboard">
            <option value="">Normal (default)</option>
            <option value="number" <?php print ($pack->getProperty('keyboard') == 'number' ? 'selected="selected"' : ''); ?>>Numbers only</option>
        </select>
    </label>
</div>