<?php
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\Partner;
use StudySauce\Bundle\Entity\Response;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/results.css'], [], ['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts'); ?>
<?php foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/results.js'], [], ['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="results">
        <div class="pane-content">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#individual" data-target="#individual" data-toggle="tab">By User</a></li>
                <li><a href="#aggregate" data-target="#aggregate" data-toggle="tab">By Pack</a></li>
            </ul>
            <div class="search"><label class="input"><input name="search" type="text" value=""
                                                            placeholder="Search"/></label></div>
            <?php print $view->render('AdminBundle:Shared:paginate.html.php', ['total' => $total]); ?>
            <div class="tab-content">
                <div id="individual" class="tab-pane active">
                    <table class="results expandable">
                        <thead>
                        <tr>
                            <th><label><span>Add Connection</span><br/>
                                    <select name="last">
                                        <option value="">User</option>
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
                            <th><label><span>Total: <?php print $total; ?></span><br/>
                                    <select name="email">
                                        <option value="">Email</option>
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
                            <th><label>
                                    <span>GH: <?php print $torch; ?></span><br />
                                    <select name="group">
                                        <option value="">Group</option>
                                        <option value="_ascending">Ascending (A-Z)</option>
                                        <option value="_descending">Descending (Z-A)</option>
                                        <?php foreach ($groups as $i => $g) {
                                            /** @var Group $g */
                                            ?>
                                            <option value="<?php print $g->getId(); ?>"><?php print $g->getName(); ?></option><?php
                                        } ?>
                                        <option value="nogroup">No Groups</option>
                                    </select></label></th>
                            <th><label class="input"><span>: </span><br/>
                                    <input type="text" name="modified" value="" placeholder="Last seen"/>
                                </label>

                                <div></div>
                            </th>
                            <th><label><span>Downloaded: </span><br/>
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
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $i => $u) {
                            /** @var User $u */
                            ?>
                            <tr class="user-id-<?php print $u->getId(); ?> read-only status_<?php print ($u->getProperty('adviser_status') ?: 'green'); ?>">
                                <td><?php print $u->getFirst(); ?> <?php print $u->getLast(); ?></td>
                                <td><?php print $u->getEmail(); ?><td>
                                    <?php foreach ($groups as $i => $g) { ?>
                                        <label class="checkbox"><input type="checkbox" name="groups"
                                                                       value="<?php print $g->getId(); ?>" <?php print ($u->hasGroup($g->getName()) ? 'checked="checked"' : ''); ?> /><i></i><span><?php print $g->getName(); ?></span></label>
                                    <?php } ?>
                                </td>
                                <td><?php print (!empty($u->getLastVisit()) ? $u->getLastVisit()->format('j M') : $u->getCreated()->format('j M')); ?></td>
                                <td><?php print $u->getUserPacks()->count(); ?></td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <table class="results expandable">
                                        <tbody>
                                        <?php
                                        if ($u->getUserPacks()->count() > 0) {
                                            foreach ($u->getUserPacks()->toArray() as $up) {
                                                /** @var UserPack $up */
                                                $responses = $up->getResponses($correct);
                                                ?>
                                                <tr>
                                                    <td colspan="3"><?php print $up->getPack()->getTitle(); ?></td>
                                                    <td><?php print (!empty($up->getDownloaded()) ? $up->getDownloaded()->format('j M') : $up->getCreated()->format('j M')); ?></td>
                                                    <td><?php print round($correct * 100.0 / $up->getPack()->getCards()->count()); ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5">
                                                        <table class="results">
                                                            <tbody>
                                                            <?php
                                                            if (count($responses) > 0) {
                                                                foreach ($responses as $r) {
                                                                    /** @var Response $r */ ?>
                                                                    <tr class="<?php print ($r->getCorrect() ? 'correct' : 'wrong'); ?>">
                                                                        <td colspan="3"><?php print $r->getCard()->getContent(); ?></td>
                                                                        <td><?php print $r->getCreated()->format('j M'); ?></td>
                                                                        <td><?php print ($r->getCorrect() ? 'Correct' : 'Wrong'); ?></td>
                                                                    </tr class="<?php print ($r->getCorrect() ? 'correct' : 'wrong'); ?>">
                                                                <?php }
                                                            } else { ?>
                                                                <tr class="empty">
                                                                    No recorded responses yet.
                                                                </tr>
                                                            <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr class="empty">
                                                No downloaded packs yet.
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div id="aggregate" class="tab-pane">
                    <table class="results expandable">
                        <thead>
                        <tr>
                            <th><label><span>Add Pack</span><br/>
                                    <select name="pack">
                                        <option value="">Pack</option>
                                        <option value="_ascending">Ascending (A-Z)</option>
                                        <option value="_descending">Descending (Z-A)</option>
                                    </select></label></th>
                            <th><label><span>Total: <?php print $total; ?></span><br/>
                                    <select name="last">
                                        <option value="">User</option>
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
                            <th><label>
                                    <span>GH: <?php print $torch; ?></span><br />
                                    <select name="group">
                                        <option value="">Group</option>
                                        <option value="_ascending">Ascending (A-Z)</option>
                                        <option value="_descending">Descending (Z-A)</option>
                                        <?php foreach ($groups as $i => $g) {
                                            /** @var Group $g */
                                            ?>
                                            <option value="<?php print $g->getId(); ?>"><?php print $g->getName(); ?></option><?php
                                        } ?>
                                        <option value="nogroup">No Groups</option>
                                    </select></label></th>
                            <th><label class="input"><span>: </span><br/>
                                    <input type="text" name="modified" value="" placeholder="Last seen"/>
                                </label>

                                <div></div>
                            </th>
                            <th><label><span>Downloads: </span><br/>
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
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($packs as $i => $p) {
                            /** @var Pack $p */

                            ?>
                            <tr class="pack-id-<?php print $p->getId(); ?> read-only">
                                <td><?php print $p->getTitle(); ?></td>
                                <td><?php print $p->getCreator(); ?></td>
                                <td>
                                    <?php foreach ($groups as $g) { ?>
                                        <label class="checkbox"><input type="checkbox" name="groups"
                                                                       value="<?php print $g->getId(); ?>" <?php print (!empty($p->getGroup()) && $p->getGroup() == $g ? 'checked="checked"' : ''); ?> /><i></i><span><?php print $g->getName(); ?></span></label>
                                    <?php } ?>
                                </td>
                                <td><?php print (!empty($p->getModified()) ? $p->getModified()->format('j M') : $p->getCreated()->format('j M')); ?></td>
                                <td><?php print $p->getUserPacks()->count(); ?></td>
                            </tr>
                            <tr>
                                <td colspan="5">
                                    <table class="results expandable">
                                        <tbody>
                                        <?php
                                        if ($p->getUserPacks()->count() > 0) {
                                            foreach ($p->getUserPacks()->toArray() as $up) {
                                                /** @var UserPack $up */
                                                $responses = $up->getResponses($correct);
                                                ?>
                                                <tr>
                                                    <td colspan="3"><?php print $up->getUser()->getFirst(); ?> <?php print $up->getUser()->getLast(); ?></td>
                                                    <td><?php print (!empty($up->getDownloaded()) ? $up->getDownloaded()->format('j M') : $up->getCreated()->format('j M')); ?></td>
                                                    <td><?php print round($correct * 100.0 / $up->getPack()->getCards()->count()); ?>%</td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5">
                                                        <table class="results">
                                                            <tbody>
                                                            <?php
                                                            if (count($responses)) {
                                                                foreach ($responses as $r) {
                                                                    /** @var Response $r */ ?>
                                                                    <tr class="<?php print ($r->getCorrect() ? 'correct' : 'wrong'); ?>">
                                                                        <td colspan="3"><?php print $r->getCard()->getContent(); ?></td>
                                                                        <td><?php print $r->getCreated()->format('j M'); ?></td>
                                                                        <td><?php print ($r->getCorrect() ? 'Correct' : 'Wrong') ?></td>
                                                                    </tr>
                                                                <?php }
                                                            } else { ?>
                                                                <tr class="empty">
                                                                    No recorded responses yet.
                                                                </tr>
                                                            <?php } ?>
                                                            </tbody>
                                                        </table>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr class="empty">
                                                No user downloads yet.
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php $view['slots']->stop();


$view['slots']->start('sincludes'); ?>

<?php $view['slots']->stop();