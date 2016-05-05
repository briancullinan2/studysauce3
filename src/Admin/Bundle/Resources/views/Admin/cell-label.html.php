<?php
foreach($fields as $f => $field) { ?>
<label>
    <span><?php print ($view->escape($field)); ?></span>
</label>
<?php }