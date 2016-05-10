<header class="pack">
    <label>Study pack</label>
    <label>Members</label>
    <label>Cards</label>
    <a href="#create-pack" data-target="#create-entity" data-toggle="modal" data-action="<?php
    print ($view['router']->generate('save_group', ['ss_group' => ['id' => $searchRequest['ss_group-id']]])); ?>" class="big-add">Add
        <span>+</span> new pack</a>
</header>
