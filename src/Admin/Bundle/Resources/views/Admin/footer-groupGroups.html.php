<?php
use StudySauce\Bundle\Entity\Pack;

?><div class="highlighted-link form-actions <?php print $table; ?>">
    <a href="#edit-<?php print $table; ?>" class="btn">Edit <?php print ucfirst(str_replace('ss_', '', $table)); ?></a>
    <a href="#cancel-edit" class="btn">Close</a>
    <a href="#save-<?php print $table; ?>" class="more">Save</a>
    <div class="packs">
        <a href="<?php print $view['router']->generate('packs_new'); ?>" class="big-add">Create
            <span>+</span> new pack</a><br/>
        <?php
        if (isset($searchRequest['ss_group-id']) && isset(${$table}[0])) {
            $packIds = ${$table}[0]->getGroupPacks()->filter(function (Pack $p) {return $p->getStatus() != 'DELETED';})->map(function (Pack $p) {return 'pack-' . $p->getId();})->toArray();

            print $this->render('AdminBundle:Admin:cell-collection.html.php', ['tables' => ['pack' => ['title','userCountStr','cardCountStr', 'id', 'status']], 'entityIds' => $packIds]);
        }
        else {
            print $this->render('AdminBundle:Admin:cell-collection.html.php', ['tables' => ['pack' => ['title','userCountStr','cardCountStr', 'id', 'status']]]);
        }
        ?>
    </div>
</div>