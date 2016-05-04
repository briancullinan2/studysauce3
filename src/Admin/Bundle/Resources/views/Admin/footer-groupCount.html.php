<?php
list($users, $packs, $groups) = $results['ss_group-1'][0]->getUsersPacksGroupsRecursively();
?>
<footer class="ss_group"><label>Total in this Group</label><label><?php print count($users); ?></label><label><?php print count($packs); ?></label></footer>
