<div class="highlighted-link form-actions <?php print $table; ?>">
    <a href="#add-<?php print $table; ?>" class="big-add">Add
        <span>+</span> <?php print str_replace('ss_', '', $table); ?></a>
    <div class="invalid-error">
        <span class="pack-error">The pack is missing a title</span>
        <span class="card-error">The list below has errors in it</span>
        <br />
        <a href="#goto-error">Click here to highlight next problem</a>
    </div>
    <a href="#edit-<?php print $table; ?>" class="btn">Edit <?php print ucfirst(str_replace('ss_', '', $table)); ?></a>
    <a href="<?php print $view['router']->generate('packs'); ?>" class="btn cancel-edit">Close</a>
    <a href="#save-<?php print $table; ?>" class="more">Save</a>
</div>