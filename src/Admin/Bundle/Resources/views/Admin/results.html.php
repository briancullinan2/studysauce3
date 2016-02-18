<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;

?>
<style>
    <?php foreach($tables as $table => $t) { ?>
    .showing-<?php print $table; ?> header > .<?php print $table; ?> {
        display: inline-block;
        opacity: 1;
        visibility: visible;
    }
    .showing-<?php print $table; ?> header > h2.<?php print $table; ?> {
        display: block;
        opacity: 1;
        visibility: visible;
    }
    .results.collapsible > h2.<?php print $table; ?>.collapsed ~ .highlighted-link.<?php print $table; ?>,
    .results.collapsible > h2.<?php print $table; ?>.collapsed ~ .<?php print $table; ?>-row {
        display:none;
    }
    <?php } ?>
</style>
<div class="results collapsible">
    <header class="pane-top">
        <div class="search">
            <form action="<?php print $view['router']->generate('command'); ?>" method="post">
                <div class="class-names">
                    <?php foreach($tables as $table => $t) { ?>
                        <label class="checkbox">
                            <input type="checkbox" name="tables" value="<?php print $table; ?>" checked="checked" />
                            <i></i> <a href="#<?php print $table; ?>"><?php print ucfirst(str_replace('ss_', '', $table)); ?>s</a></label>
                    <?php }
                    foreach(array_diff($allTables, array_keys($tables)) as $table) { ?>
                        <label class="checkbox">
                            <input type="checkbox" name="tables" value="<?php print $table; ?>" />
                            <i></i> <a href="#<?php print $table; ?>"><?php print ucfirst(str_replace('ss_', '', $table)); ?>s</a></label>
                    <?php } ?>
                </div>
                <label class="input"><input name="search" type="text" value="" placeholder="Search" /></label>
            </form>
        </div>

        <?php foreach($tables as $table => $t) {
            $tableTotal = $table . '_total';
            ?><div class="<?php print $table; ?> paginate"><?php
                print $view->render('AdminBundle:Shared:paginate.html.php', ['total' => $$tableTotal]);
            ?></div><?php
        } ?>

        <?php
        $max = max(array_map(function ($t) {
            return count($t);
        }, $tables));
        $templates = []; // template name => classes
        for ($i = 0; $i < $max; $i++) {
            // TODO: build backwards so its right aligned when there are different field counts
            foreach ($tables as $table => $t) {
                $field = is_array(array_values($t)[$i]) ? array_keys($t)[$i] : array_values($t)[$i];
                if ($view->exists('AdminBundle:Admin:heading-' . $field . '-' . $table . '.html.php')) {
                    $viewName = 'AdminBundle:Admin:heading-' . $field . '-' . $table . '.html.php';
                }
                else {
                    $viewName = 'AdminBundle:Admin:heading-' . $field . '.html.php';
                }
                if (isset($templates[$viewName])) {
                    $templates[$viewName][] = $table;
                } else {
                    $templates[$viewName] = [$table];
                }
            }
        }

        foreach($tables as $table => $t) {
            ?><h2 class="<?php print $table; ?>"><?php print ucfirst(str_replace('ss_', '', $table)); ?>s <a href="#add-<?php print $table; ?>">+</a></h2><?php
        }

        foreach ($templates as $k => $classes) {
            $field = explode('.', explode('-', $k)[1])[0] . ' ' . implode(' ', $classes);
            ?>
            <div class="<?php print $field; ?>">
                <?php
                if ($view->exists($k)) {
                    print $view->render($k, ['groups' => $allGroups, 'field' => $field]);
                }
                else {
                    print $view->render('AdminBundle:Admin:heading.html.php', ['groups' => $allGroups, 'field' => $field, 'table' => $table]);
                }
                ?>
            </div>
        <?php } ?>
        <label class="checkbox"><input type="checkbox" name="select-all"/><i></i></label>
    </header>
    <?php foreach ($tables as $table => $t) {
        if(count($$table) > 0) {
            ?>
            <h2 class="<?php print $table; ?>"><a
                    name="<?php print $table; ?>"><?php print ucfirst(str_replace('ss_', '', $table)); ?>s</a> <a
                    href="#add-<?php print $table; ?>">+</a></h2>
            <?php
        }
        foreach ($$table as $e) {
            /** @var User|Group $e */
            $rowId = $table . '-id-' . $e->getId();
            ?>
            <div class="<?php print $table; ?>-row <?php print $rowId; ?> read-only">
                <?php
                foreach ($tables[$table] as $f => $fields) {
                    $field = is_array($fields) ? $f : $fields;
                    ?>
                    <div class="<?php print $field; ?>">
                    <?php
                    if ($view->exists('AdminBundle:Admin:row-' . $field . '-' . $table . '.html.php')) {
                        print $view->render('AdminBundle:Admin:row-' . $field . '-' . $table . '.html.php', [$table => $e, 'groups' => $allGroups, 'table' => $table]);
                    } else {
                        print $view->render('AdminBundle:Admin:row-' . $field . '.html.php', ['entity' => $e, 'groups' => $allGroups, 'table' => $table]);
                    }
                    ?></div><?php
                }
                ?>
                <label class="checkbox"><input type="checkbox" name="selected" /><i></i></label>
            </div>
        <?php }

        $class = AdminController::$allTables[$table]->name;
        $entity = new $class();
        ?>
        <div class="<?php print $table; ?>-row <?php print $table . '-id-'; ?> read-only template empty">
            <?php
            foreach ($tables[$table] as $f => $fields) {
                $field = is_array($fields) ? $f : $fields;
                ?>
            <div class="<?php print $field; ?>">
                <?php
                if ($view->exists('AdminBundle:Admin:row-' . $field . '-' . $table . '.html.php')) {
                    print $view->render('AdminBundle:Admin:row-' . $field . '-' . $table . '.html.php', [$table => $entity, 'groups' => $allGroups, 'table' => $table]);
                } else {
                    print $view->render('AdminBundle:Admin:row-' . $field . '.html.php', ['entity' => $entity, 'groups' => $allGroups, 'table' => $table]);
                }
                ?></div><?php
            }
            ?>
            <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>
        </div>
        <div class="highlighted-link form-actions invalid <?php print $table; ?>">
            <a href="#add-<?php print $table; ?>" class="big-add">Add <span>+</span> <?php print str_replace('ss_', '', $table); ?></a>
            <a href="#save-<?php print $table; ?>" class="more">Save <?php print str_replace('ss_', '', $table); ?></a>
        </div>
    <?php } ?>
</div>