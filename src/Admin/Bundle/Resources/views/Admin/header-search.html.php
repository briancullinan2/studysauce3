<header class="pane-top">
    <div class="search">
        <form action="<?php print ($view['router']->generate('command')); ?>" method="post">
            <div class="class-names">
                <?php foreach ($tables as $table => $t) { ?>
                    <label class="checkbox">
                        <input type="checkbox" name="tables" value="<?php print ($table); ?>" checked="checked"/>
                        <i></i> <a
                            href="#<?php print ($table); ?>"><?php print (ucfirst(str_replace('ss_', '', $table))); ?>s</a></label>
                <?php } ?>
            </div>
            <label class="input"><input name="search" type="text" value="" placeholder="Search"/></label>
        </form>
    </div>

    <?php foreach ($tables as $table => $t) {
        ?>
        <div class="<?php print ($table); ?> paginate"><?php
        print ($view->render('AdminBundle:Shared:paginate.html.php', ['total' => $results[implode('', [$table , '_total'])]]));
        ?></div><?php
    } ?>

    <?php
    $templates = []; // template name => classes
    // TODO: build backwards so its right aligned when there are different field counts
    foreach ($tables as $table => $t) {
        for ($i = 0; $i < count($t); $i++) {
            $field = is_array(array_values($t)[$i]) ? array_keys($t)[$i] : array_values($t)[$i];
            // skip search only fields
            if(is_numeric($field)) {
                continue;
            }
            if ($view->exists(implode('', ['AdminBundle:Admin:heading-' , $field , '-' , $table , '.html.php']))) {
                $viewName = implode('', ['AdminBundle:Admin:heading-' , $field , '-' , $table , '.html.php']);
            } else {
                $viewName = implode('', ['AdminBundle:Admin:heading-' , $field , '.html.php']);
            }
            if (isset($templates[$viewName])) {
                $templates[$viewName][count($templates[$viewName])] = $table;
            } else {
                $templates[$viewName] = [$table];
            }
        }
    }

    foreach ($tables as $table => $t) { ?>
        <h2 class="<?php print ($table); ?>"><?php print (ucfirst(str_replace('ss_', '', $table))); ?>s <a
            href="#add-<?php print ($table); ?>">+</a>
        <small>(<?php print ($results[implode('', [$table , '_total'])]); ?>)</small></h2><?php
    }

    foreach ($templates as $k => $classes) {
        $field = implode('', [explode('.', explode('-', $k)[1])[0] , ' ' , implode(' ', $classes)]);
        ?>
        <div class="<?php print ($field); ?>">
            <?php
            if ($view->exists($k)) {
                print ($view->render($k, ['groups' => $allGroups, 'field' => $field]));
            } else {
                print ($view->render('AdminBundle:Admin:heading.html.php', ['groups' => $allGroups, 'field' => $field]));
            }
            ?>
        </div>
    <?php } ?>
    <label class="checkbox"><input type="checkbox" name="select-all"/><i></i></label>
</header>