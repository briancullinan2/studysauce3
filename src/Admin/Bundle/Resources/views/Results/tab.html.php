<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\User;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets'); ?>
<?php foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/menu.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/results.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@Course1Bundle/Resources/public/css/course1.css'],[],['output' => 'bundles/course1/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@Course2Bundle/Resources/public/css/course2.css'],[],['output' => 'bundles/course2/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
foreach ($view['assetic']->stylesheets(['@Course3Bundle/Resources/public/css/course3.css'],[],['output' => 'bundles/course3/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts'); ?>
<?php foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/results.js'],[],['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="results">
        <div class="pane-content">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#individual" data-target="#individual" data-toggle="tab">Individual</a></li>
                <li><a href="#aggregate" data-target="#aggregate" data-toggle="tab">Aggregate</a></li>
            </ul>
            <p>&nbsp;</p>

            <div class="tab-content">
                <div id="individual" class="tab-pane active">
                    <div class="search"><label class="input"><input name="search" type="text" value=""
                                                                    placeholder="Search"/></label></div>
                    <div class="paginate">
                        <a href="?page=first">&lt;&lt;</a> <a href="?page=prev">&lt;</a>
                        <label class="input"><input name="page" type="text" value="1"/> / <span
                                id="page-total"><?php print ceil(
                                    $total / 25
                                ); ?></span></label>
                        <a href="?page=next">&gt;</a> <a href="?page=last">&gt;&gt;</a>
                    </div>
                    <table class="results">
                        <thead>
                        <tr>
                            <th><label><span>Parents: <?php print $parents; ?></span><br/>
                                    <span>Partners: <?php print $partners; ?></span><br/>
                                    <span>Students: <?php print $students; ?></span><br/>
                                    <span>Advisers: <?php print $advisers; ?></span><br/>
                                    <select name="role">
                                        <option value="">Role</option>
                                        <option value="_ascending">Ascending (A-Z)</option>
                                        <option value="_descending">Descending (Z-A)</option>
                                        <option value="ROLE_PAID">PAID</option>
                                        <option value="ROLE_ADMIN">ADMIN</option>
                                        <option value="ROLE_PARENT">PARENT</option>
                                        <option value="ROLE_PARTNER">PARTNER</option>
                                        <option value="ROLE_ADVISER">ADVISER</option>
                                        <option value="ROLE_MASTER_ADVISER">MASTER_ADVISER</option>
                                        <option value="ROLE_STUDENT">STUDENT</option>
                                        <option value="ROLE_GUEST">GUEST</option>
                                        <option value="ROLE_DEMO">DEMO</option>
                                    </select></label></th>
                            <th><label>
                                    <span>TAL: <?php print $torch; ?></span><br/>
                                    <span>CSA: <?php print $csa; ?></span><br/>
                                    <select name="group">
                                        <option value="">Group</option>
                                        <option value="_ascending">Ascending (A-Z)</option>
                                        <option value="_descending">Descending (Z-A)</option>
                                        <?php foreach ($groups as $i => $g) {
                                            /** @var Group $g */
                                            ?>
                                            <option value="<?php print $g->getId(); ?>"><?php print $g->getName(
                                            ); ?></option><?php
                                        } ?>
                                        <option value="nogroup">No Groups</option>
                                    </select></label></th>
                            <th><label><span>Total: <?php print $total; ?></span><br/>
                                    <select name="last">
                                        <option value="">Student</option>
                                        <option value="_ascending">Ascending (A-Z)</option>
                                        <option value="_descending">Descending (Z-A)</option>
                                        <option value="A%">A</option>
                                        <option value="B%">B</option>
                                        <option value="C%">C</option>
                                        <option value="D%">D</option>
                                        <option value="E%">E</option>
                                        <option value="F%">F</option>
                                        <option value="G%">G</option>
                                        <option value="H%">H</option>
                                        <option value="I%">I</option>
                                        <option value="J%">J</option>
                                        <option value="K%">K</option>
                                        <option value="L%">L</option>
                                        <option value="M%">M</option>
                                        <option value="N%">N</option>
                                        <option value="O%">O</option>
                                        <option value="P%">P</option>
                                        <option value="Q%">Q</option>
                                        <option value="R%">R</option>
                                        <option value="S%">S</option>
                                        <option value="T%">T</option>
                                        <option value="U%">U</option>
                                        <option value="V%">V</option>
                                        <option value="W%">W</option>
                                        <option value="X%">X</option>
                                        <option value="Y%">Y</option>
                                        <option value="Z%">Z</option>
                                    </select></label></th>
                            <th><label><span>Finished: <?php print $completed; ?></span><br/>
                                    <select name="completed">
                                        <option value="">Completed</option>
                                        <option value="_ascending">Ascending (0-100)</option>
                                        <option value="_descending">Descending (100-0)</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="!1">Not 1</option>
                                        <option value="!2">Not 2</option>
                                        <option value="!3">Not 3</option>
                                        <option value="1,2">1 &amp; 2</option>
                                        <option value="1,3">1 &amp; 3</option>
                                        <option value="2,3">2 &amp; 3</option>
                                        <option value="!1,!2">Not 1 &amp; 2</option>
                                        <option value="!1,!3">Not 1 &amp; 3</option>
                                        <option value="!2,!3">Not 2 &amp; 3</option>
                                        <option value="1,2,3">Completed</option>
                                        <option value="!1,!2,!3">Not Completed</option>
                                    </select></label></th>
                            <th><label><span>Paid: <?php print $paid; ?></span><br/>
                                    <select name="hasPaid">
                                        <option value="">Paid</option>
                                        <option value="yes">Y</option>
                                        <option value="no">N</option>
                                    </select></label></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $i => $u) {
                            /** @var User $u */

                            ?>
                            <tr class="user-id-<?php print $u->getId(
                            ); ?> read-only status_<?php print ($u->getProperty(
                                'adviser_status'
                            ) ?: 'green'); ?>">
                                <td>
                                    <label class="checkbox"><input type="checkbox" name="roles"
                                                                   value="ROLE_PAID" <?php print ($u->hasRole('ROLE_PAID') ? 'checked="checked"' : ''); ?> /><i></i><span>PAID</span></label>
                                    <label class="checkbox"><input type="checkbox" name="roles"
                                                                   value="ROLE_ADMIN" <?php print ($u->hasRole('ROLE_ADMIN') ? 'checked="checked"' : ''); ?> /><i></i><span>ADMIN</span></label>
                                    <label class="checkbox"><input type="checkbox" name="roles"
                                                                   value="ROLE_PARENT" <?php print ($u->hasRole('ROLE_PARENT') ? 'checked="checked"' : ''); ?> /><i></i><span>PARENT</span></label>
                                    <label class="checkbox"><input type="checkbox" name="roles"
                                                                   value="ROLE_PARTNER" <?php print ($u->hasRole('ROLE_PARTNER') ? 'checked="checked"' : ''); ?> /><i></i><span>PARTNER</span></label>
                                    <label class="checkbox"><input type="checkbox" name="roles"
                                                                   value="ROLE_ADVISER" <?php print ($u->hasRole('ROLE_ADVISER') ? 'checked="checked"' : ''); ?> /><i></i><span>ADVISER</span></label>
                                    <label class="checkbox"><input type="checkbox" name="roles"
                                                                   value="ROLE_MASTER_ADVISER" <?php print ($u->hasRole('ROLE_MASTER_ADVISER') ? 'checked="checked"' : ''); ?> /><i></i><span>MASTER_ADVISER</span></label>
                                    <label class="checkbox"><input type="checkbox" name="roles"
                                                                   value="ROLE_DEMO" <?php print ($u->hasRole('ROLE_DEMO') ? 'checked="checked"' : ''); ?> /><i></i><span>DEMO</span></label>
                                    <label class="checkbox"><input type="checkbox" name="roles"
                                                                   value="ROLE_GUEST" <?php print ($u->hasRole('ROLE_GUEST') ? 'checked="checked"' : ''); ?> /><i></i><span>GUEST</span></label>
                                </td>
                                <td>
                                    <?php foreach ($groups as $i => $g) { ?>
                                        <label class="checkbox"><input type="checkbox" name="groups"
                                                                       value="<?php print $g->getId(); ?>" <?php print ($u->hasGroup($g->getName()) ? 'checked="checked"' : ''); ?> /><i></i><span><?php print $g->getName(); ?></span></label>
                                    <?php } ?>
                                </td>
                                <td>
                                    <label class="input"><input type="text" name="first-name"
                                                                value="<?php print $u->getFirst(); ?>"
                                                                placeholder="First name"/></label>
                                    <label class="input"><input type="text" name="last-name"
                                                                value="<?php print $u->getLast(); ?>"
                                                                placeholder="Last name"/></label>
                                    <label class="input"><input type="text" name="email"
                                                                value="<?php print $u->getEmail(); ?>"
                                                                placeholder="Email"/></label>
                                </td>
                                <td><?php print $u->getCompleted(); ?>%</td>
                                <td><?php print ($u->hasRole('ROLE_PAID') ? 'Y' : 'N'); ?></td>
                            </tr>
                            <tr class="loading">
                                <td colspan="5">
                                    <div>Loading...</div>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div id="aggregate" class="tab-pane">
                    <table class="results">
                        <tbody>
                            <tr>
                                <td>
                                    <table>
                                        <tr>
                                            <td><h3>Course 1</h3></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td><h4>Introduction</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['quiz1']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Settings goals</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['quiz2']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Distractions</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['quiz4']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Procrastination</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['quiz3']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Study environment</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['quiz5']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Partners<h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['quiz6']; ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><h3>Course 2</h3></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td><h4>Study metrics</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['studyMetrics']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Study plans</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['studyPlan']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Interleaving</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['interleaving']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Studying for tests</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['studyTests']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Test taking</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['testTaking']; ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><h3>Course 3</h3></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <table>
                                                    <tr>
                                                        <td><h4>Intro to strategies</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['strategies']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Group study</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['groupStudy']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Teach to learn</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['teaching']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Active reading</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['activeReading']; ?>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td><h4>Spaced repetition</h4></td>
                                                    </tr>
                                                    <tr>
                                                        <td class="read-only">
                                                            <?php print $aggregate['spacedRepetition']; ?>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><h3>Feedback</h3></td>
                                        </tr>
                                        <tr><td>
                                                <div class="panel-pane course1 step2" id="course1_introduction-step4">
                                                    <div class="pane-content">
                                                        <h3>Why do you want to become better at studying?</h3>
                                                        <label class="input">
                                                            <?php print $aggregate['whyStudy']; ?>
                                                        </label>
                                                        <h3>Do you have any feedback?</h3>
                                                        <label class="input">
                                                            <?php print $aggregate['feedback']; ?>
                                                        </label>
                                                        <h3>Score</h3>
                                                        <label class="radio">
                                                            <?php print $aggregate['net-promoter']; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </td></tr>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $view['slots']->stop();


$view['slots']->start('sincludes'); ?>

<?php $view['slots']->stop();