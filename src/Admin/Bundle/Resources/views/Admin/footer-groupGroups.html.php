<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;

?><div class="highlighted-link form-actions <?php print ($table); ?>">
    <a href="#edit-<?php print ($table); ?>" class="btn">Edit <?php print (ucfirst(str_replace('ss_', '', $table))); ?></a>
    <a href="<?php print ($view['router']->generate('groups')); ?>" class="btn cancel-edit">Close</a>
    <a href="#save-<?php print ($table); ?>" class="more">Save</a>
</div>