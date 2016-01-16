<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\User;

?>
<div class="results collapsible">
    <header class="pane-top">
        <div class="search">
            <form action="<?php print $view['router']->generate('save_user'); ?>" method="post">
                <div class="class-names">
                    <?php foreach($tables as $table => $t) { ?>
                        <label class="checkbox"><input type="checkbox" name="packs" value="<?php print $table; ?>" checked="checked" /><i></i> <a href="#<?php print $table; ?>"><?php print ucfirst(str_replace('ss_', '', $table)); ?>s</a></label>
                    <?php } ?>
                </div>
                <label class="input"><input name="search" type="text" value=""
                                            placeholder="Search"/></label>
            </form>
        </div>

        <?php foreach($tables as $table => $t) {
            $tableTotal = $table . '_total';
            ?><div class="<?php print $table; ?>"><?php
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
                $viewName = $view->exists('AdminBundle:Admin:heading-' . $t[$i] . '-' . $table . '.html.php')
                    ? 'AdminBundle:Admin:heading-' . $t[$i] . '-' . $table . '.html.php'
                    : 'AdminBundle:Admin:heading-' . $t[$i] . '.html.php';
                if (isset($templates[$viewName])) {
                    $templates[$viewName][] = $table;
                } else {
                    $templates[$viewName] = [$table];
                }
            }
        }

        foreach($tables as $table => $t) {
            ?><h2 class="<?php print $table; ?>"><?php print ucfirst(str_replace('ss_', '', $table)); ?>s <a href="#add-entity">+</a></h2><?php
        }

        foreach ($templates as $k => $classes) { ?>
            <div class="<?php print explode('.', explode('-', $k)[1])[0] . ' ' . implode(' ', $classes); ?>">
                <?php print $view->render($k, ['groups' => $ss_group]); ?>
            </div>
        <?php } ?>
        <label class="checkbox"><input type="checkbox" name="select-all"/><i></i></label>
    </header>
    <?php foreach ($tables as $table => $t) { ?>
        <h2 class="<?php print $table; ?>"><a name="<?php print $table; ?>"><?php print ucfirst(str_replace('ss_', '', $table)); ?>s</a> <a href="#add-entity">+</a></h2>
        <?php
        foreach ($$table as $e) {
            /** @var User|Group $e */
            $rowId = $table . '-id-' . $e->getId();
            ?>
            <div class="<?php print $table; ?>-row <?php print $rowId; ?> read-only">
                <?php
                foreach ($tables[$table] as $field) { ?>
                <div class="<?php print $field; ?>">
                    <?php
                    if ($view->exists('AdminBundle:Admin:row-' . $field . '-' . $table . '.html.php')) {
                        print $view->render('AdminBundle:Admin:row-' . $field . '-' . $table . '.html.php', [$table => $e]);
                    } else {
                        print $view->render('AdminBundle:Admin:row-' . $field . '.html.php', ['entity' => $e, 'groups' => $ss_group]);
                    }
                    ?></div><?php
                }
                ?>
                <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>
            </div>
        <?php }
    } ?>
</div>