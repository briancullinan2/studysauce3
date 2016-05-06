<?php

use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;

?>
<div class="highlighted-link form-actions <?php print $table; ?>">
    <?php if(empty($results['pack'])) { ?>
        <div class="empty-packs">No packs in this group or all subgroups</div>
        <?php
    } ?>
</div>

