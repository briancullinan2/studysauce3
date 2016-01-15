<?php
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use StudySauce\Bundle\Entity\Course;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\PartnerInvite;
use StudySauce\Bundle\Entity\Schedule;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\Visit;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$tables = [
    'ss_user' => ['id', 'name', 'groups', 'packs', 'roles', 'actions'],
    'ss_group' => ['id', 'name', 'users', 'packs', 'roles', 'actions'],
    'pack' => ['id', 'name', 'users', 'creator', 'status', 'actions']
];

//$times = array_map(function($e) {
/** @var User|Group $e */
//    return $e->getCreated()->getTimestamp();
//}, $entities);
//array_multisort($times, SORT_NUMERIC, SORT_DESC, $entities);

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/admin.css'], [], ['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
?>
    <style>
        #command.ss_user-row header > .ss_user {
            display: inline-block;
        }

        #command.ss_group-row header > .ss_group {
            display: inline-block;
        }

        #command.pack-row header > .pack {
            display: inline-block;
        }
    </style>
<?php
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/admin.js'], [], ['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="command">
        <div class="pane-content">
            <div class="results">
                <header class="pane-top">
                    <div class="search">
                        <form action="<?php print $view['router']->generate('save_user'); ?>" method="post">
                            <label class="checkbox"><input type="checkbox" name="packs" value="ss_user" checked="checked" /><i></i> Users</label>
                            <label class="checkbox"><input type="checkbox" name="packs" value="ss_group" checked="checked" /><i></i> Groups</label>
                            <label class="checkbox"><input type="checkbox" name="packs" value="pack" checked="checked" /><i></i> Packs</label>
                            <label class="input"><input name="search" type="text" value=""
                                                        placeholder="Search"/></label>
                        </form>
                    </div>
                    <?php print $view->render('AdminBundle:Shared:paginate.html.php', ['total' => $total]); ?>
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

                    foreach ($templates as $k => $classes) { ?>
                        <div class="<?php print explode('.', explode('-', $k)[1])[0] . ' ' . implode(' ', $classes); ?>">
                            <?php print $view->render($k, ['groups' => $ss_group]); ?>
                        </div>
                    <?php } ?>
                    <label class="checkbox"><input type="checkbox" name="select-all"/><i></i></label>
                </header>
                <?php foreach ($tables as $table => $t) { ?>
                    <h2><a name="#<?php print $table; ?>"><?php print ucfirst(str_replace('ss_', '', $table)); ?>s</a></h2>
                    <?php
                    foreach ($$table as $e) {
                        /** @var User|Group $e */
                        $data = $orm->getMetadataFactory()->getMetadataFor(get_class($e));
                        $table = $data->table['name'];
                        $rowId = $table . '-id-' . $e->getId();
                        ?>
                        <div class="<?php print $table; ?>-row <?php print $rowId; ?> read-only">
                            <?php
                            foreach ($tables[$table] as $field) {
                                if ($view->exists('AdminBundle:Admin:row-' . $field . '-' . $table . '.html.php')) {
                                    print $view->render('AdminBundle:Admin:row-' . $field . '-' . $table . '.html.php', [$table => $e]);
                                } else {
                                    print $view->render('AdminBundle:Admin:row-' . $field . '.html.php', ['entity' => $e, 'groups' => $ss_group]);
                                }
                            }
                            ?>
                            <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>
                        </div>
                    <?php }
                } ?>
            </div>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
print $this->render('AdminBundle:Dialogs:confirm-remove-user.html.php', ['id' => 'confirm-remove-user']);
print $this->render('AdminBundle:Dialogs:confirm-password-reset.html.php', ['id' => 'confirm-password-reset']);
print $this->render('AdminBundle:Dialogs:confirm-cancel-user.html.php', ['id' => 'confirm-cancel-user']);
print $this->render('AdminBundle:Dialogs:add-user.html.php', ['id' => 'add-user']);
$view['slots']->stop();
