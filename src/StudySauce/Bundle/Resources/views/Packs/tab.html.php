<?php
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\User;
use StudySauce\Bundle\Entity\UserPack;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;

/** @var GlobalVariables $app */
/** @var $view TimedPhpEngine */
/** @var $user User */
/** @var Pack $pack */

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/packs.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url):?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/packs.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="packs">
        <div class="pane-content">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#create-pack" data-target="#create-pack" data-toggle="tab">Create pack</a></li>
                <li><a href="#all-packs" data-target="#all-packs" data-toggle="tab">Packs</a></li>
                <li><a href="#membership" data-target="#membership" data-toggle="tab">Membership</a></li>
            </ul>
            <div class="tab-content">
                <div id="create-pack" class="tab-pane active">
                    <form action="<?php print $view['router']->generate('packs_create'); ?>" method="post">
                        <div class="pane-top">
                            <h2>Create a pack</h2>
                            <label class="input title">
                                <input name="title" placeholder="Title your pack" value="<?php print (!empty($pack) ? $pack->getTitle() : ''); ?>"/>
                            </label>
                            <label class="input creator read-only">
                                <input name="creator" placeholder="Creator name"
                                       value="<?php print (!empty($pack) ? $pack->getCreator() : (!empty($app->getUser()) ? ($app->getUser()->getFirst() . ' ' . $app->getUser()->getLast()) : '')); ?>"/>
                                <small>* If the pack is public this is the name others will see.</small>
                            </label>
                            <label class="input group">
                                <select name="group">
                                    <option value="">Group</option>
                                    <?php foreach ($groups as $i => $g) {
                                        /** @var Group $g */
                                        ?>
                                        <option value="<?php print $g->getId(); ?>" <?php print (!empty($pack) && !empty($pack->getGroup()) && $pack->getGroup() == $g ? 'selected="selected"' : ''); ?>><?php print $g->getName(); ?></option><?php
                                    } ?>
                                </select>
                            </label>
                            <label class="input status">
                                <select name="status">
                                    <option value="">Status</option>
                                    <option value="UNPUBLISHED" <?php print (!empty($pack) && $pack->getStatus() == 'UNPUBLISHED' ? 'selected="selected"' : ''); ?>>Unpublished</option>
                                    <option value="PUBLIC" <?php print (!empty($pack) && $pack->getStatus() == 'PUBLIC' ? 'selected="selected"' : ''); ?>>Public</option>
                                    <option value="GROUP" <?php print (!empty($pack) && $pack->getStatus() == 'GROUP' ? 'selected="selected"' : ''); ?>>Group-only</option>
                                    <option value="UNLISTED" <?php print (!empty($pack) && $pack->getStatus() == 'UNLISTED' ? 'selected="selected"' : ''); ?>>Unlisted</option>
                                    <option value="DELETED" <?php print (!empty($pack) && $pack->getStatus() == 'DELETED' ? 'selected="selected"' : ''); ?>>Deleted</option>
                                </select>
                            </label>
                            <h3>Add questions and answers by pasting from excel to the space below (make sure you have questions
                                in
                                column 1 and responses in column 2).</h3>
                        </div>

                        <div class="results">
                            <header>
                                <label>Type</label>
                                <label>Prompt/Question</label>
                                <label>Correct/Response</label>
                                <label>Hint/Context</label>
                                <label class="checkbox"><input type="checkbox" name="selected"><i></i></label>
                            </header>
                            <?php
                            $cards = !empty($pack) ? $pack->getCards() : array_map(function () {return new Card();}, range(0, 5));
                            $i = 0;
                            foreach ($cards as $c) {
                                /** @var Card $c */
                                if($c->getDeleted()) {
                                    continue;
                                }
                                ?>
                                <div class="card-row edit <?php
                                print (!empty($c->getId()) ? (' card-id-' . $c->getId()) : '');
                                print (empty($c->getResponseType()) ? '' : (' type-' . $c->getResponseType())); ?>">
                                    <label class="input type">
                                        <select name="type">
                                            <option value="" <?php print (empty($c->getResponseType()) ? 'selected="selected"' : ''); ?> data-text="Flash card (default)">Type</option>
                                            <option value="mc" <?php print ($c->getResponseType() == 'mc' ? 'selected="selected"' : ''); ?> data-text="Multiple choice">MC</option>
                                            <option value="tf" <?php print ($c->getResponseType() == 'tf' ? 'selected="selected"' : ''); ?> data-text="True/False">TF</option>
                                            <option value="sa" <?php print ($c->getResponseType() == 'sa' ? 'selected="selected"' : ''); ?> data-text="Short answer">SA</option>
                                        </select>
                                    </label>
                                    <label class="input content">
                                        <input type="text" name="content" placeholder="Prompt" value="<?php print $view->escape($c->getContent()); ?>"/>
                                    </label>
                                    <label class="input correct">
                                        <input type="text" name="correct" placeholder="for display only" value="<?php print (!empty($c->getCorrect()) ? $view->escape($c->getCorrect()->getValue()) : ''); ?>" />
                                    </label>
                                    <label class="input correct type-mc">
                                        <select><?php foreach($c->getAnswers()->toArray() as $a) {
                                                /** @var Answer $a */
                                                ?>
                                                <option value="<?php print $a->getValue(); ?>" <?php print ($a->getCorrect() ? 'selected="selected"' : ''); ?>><?php print $a->getValue(); ?></option>
                                            <?php } ?></select>
                                    </label>
                                    <label class="input correct type-sa">
                                        <select>
                                            <option value="exactly" <?php print (!empty($c->getCorrect()) && substr($c->getCorrect()->getValue(), 0, 1) == '^' ? 'selected="selected"' : ''); ?>>Matches exactly</option>
                                            <option value="contains" <?php print (!empty($c->getCorrect()) && substr($c->getCorrect()->getValue(), 0, 1) != '^' ? 'selected="selected"' : ''); ?>>Contains</option>
                                        </select>
                                    </label>
                                    <label class="radio correct type-tf">
                                        <span>True</span>
                                        <input type="radio" name="correct-<?php print $i; ?>" value="true" <?php print (!empty($c->getCorrect()) && preg_match('/t/i', $c->getCorrect()->getValue()) ? 'checked="checked"' : ''); ?> />
                                        <i></i>
                                    </label>
                                    <label class="radio correct type-tf">
                                        <input type="radio" name="correct-<?php print $i; ?>" value="false" <?php print (!empty($c->getCorrect()) && preg_match('/f/i', $c->getCorrect()->getValue()) ? 'checked="checked"' : ''); ?> />
                                        <i></i>
                                        <span>False</span>
                                    </label>
                                    <label class="input answers type-mc">
                                        <textarea name="answers" placeholder="one per line"><?php print implode("\n", $c->getAnswers()->map(function (Answer $a) {return $a->getValue();})->toArray()); ?></textarea>
                                    </label>
                                    <label class="input answers type-sa">
                                        <input type="text" name="answers" placeholder="fill in the blank" value="<?php print (!empty($c->getCorrect()) ? trim($c->getCorrect()->getValue(), '$^') : ''); ?>" />
                                    </label>
                                    <label class="input response">
                                        <input type="text" name="response" placeholder="Description" value="<?php print $view->escape($c->getResponseContent()); ?>"/>
                                    </label>
                                    <div class="highlighted-link">
                                        <a title="Remove card" href="#remove-card"></a>
                                        <label class="checkbox"><input type="checkbox" name="selected"><i></i></label>
                                    </div>
                                </div>
                            <?php $i++; } ?>
                        </div>
                        <div class="highlighted-link form-actions invalid">
                            <a href="#add-card" class="big-add">Add <span>+</span> card</a>
                            <a href="#create-new" class="more">Save Pack</a>
                        </div>
                    </form>
                </div>
                <div id="all-packs" class="tab-pane">
                    <table class="results expandable">
                        <thead>
                        <tr>
                            <th><label><span>Title</span><br/>
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
                                    <span>GH: 0</span><br/>
                                    <select name="group">
                                        <option value="">Group</option>
                                        <option value="_ascending">Ascending (A-Z)</option>
                                        <option value="_descending">Descending (Z-A)</option>
                                        <?php foreach ($groups as $i => $g) {
                                            /** @var Group $g */
                                            ?>
                                            <option
                                            value="<?php print $g->getId(); ?>"><?php print $g->getName(); ?></option><?php
                                        } ?>
                                        <option value="nogroup">No Groups</option>
                                    </select></label></th>
                            <th><label class="input"><span>Last Modified</span><br/>
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
                            <th><label>Status:</label></th>
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
                                    <?php foreach ($groups as $g) {
                                        /** @var Group $g */
                                        ?>
                                        <label class="checkbox"><input type="checkbox" name="groups"
                                                                       value="<?php print $g->getId(); ?>" <?php print (!empty($p->getGroup()) && $p->getGroup() == $g ? 'checked="checked"' : ''); ?> /><i></i><span><?php print $g->getName(); ?></span></label>
                                    <?php } ?>
                                </td>
                                <td><?php print (!empty($p->getModified()) ? $p->getModified()->format('j M') : $p->getCreated()->format('j M')); ?></td>
                                <td><?php print $p->getUserPacks()->count(); ?></td>
                                <td class="highlighted-link">
                                    <?php print (!empty($p->getStatus()) ? $p->getStatus() : 'Not Set'); ?>&nbsp;&nbsp;
                                    <a title="Edit pack" href="<?php print $view['router']->generate('packs_edit', ['pack' => $p->getId()]); ?>" target="_blank"></a>
                                    <a title="Remove pack" href="#confirm-remove-pack" data-toggle="modal"></a>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <table class="results">
                                        <tbody>
                                        <?php
                                        if ($p->getCards()->count() > 0) {
                                            foreach ($p->getCards()->toArray() as $c) {
                                                /** @var Card $c */
                                                if($c->getDeleted()) {
                                                    continue;
                                                }
                                                ?>
                                                <tr>
                                                    <td><?php print $c->getContent(); ?></td>
                                                    <td><?php print (!empty($c->getCorrect()) ? ($c->getCorrect()->getValue() . '<br />') : ''); ?><?php print $c->getResponseContent(); ?></td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr class="empty">
                                                No cards yet.
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
                <div id="membership" class="tab-pane">
                    <table class="results expandable">
                        <thead>
                        <tr>
                            <th><label><span>Title</span><br/>
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
                                    <span>GH: 0</span><br/>
                                    <select name="group">
                                        <option value="">Group</option>
                                        <option value="_ascending">Ascending (A-Z)</option>
                                        <option value="_descending">Descending (Z-A)</option>
                                        <?php foreach ($groups as $i => $g) {
                                            /** @var Group $g */
                                            ?>
                                            <option
                                            value="<?php print $g->getId(); ?>"><?php print $g->getName(); ?></option><?php
                                        } ?>
                                        <option value="nogroup">No Groups</option>
                                    </select></label></th>
                            <th><label class="input"><span>Last Modified</span><br/>
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
                            <th></th>
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
                                    <?php foreach ($groups as $g) {
                                        /** @var Group $g */
                                        ?>
                                        <label class="checkbox"><input type="checkbox" name="groups"
                                                                       value="<?php print $g->getId(); ?>" <?php print (!empty($p->getGroup()) && $p->getGroup() == $g ? 'checked="checked"' : ''); ?> /><i></i><span><?php print $g->getName(); ?></span></label>
                                    <?php } ?>
                                </td>
                                <td><?php print (!empty($p->getModified()) ? $p->getModified()->format('j M') : $p->getCreated()->format('j M')); ?></td>
                                <td><?php print $p->getUserPacks()->count(); ?></td>
                            </tr>
                            <tr>
                                <td colspan="6">
                                    <table class="results">
                                        <tbody>
                                        <?php
                                        if ($p->getUserPacks()->count() > 0) {
                                            foreach ($p->getUserPacks()->toArray() as $c) {
                                                /** @var UserPack $c */
                                                ?>
                                                <tr>
                                                    <td><?php print $c->getUser()->getFirst() . ' ' . $c->getUser()->getLast(); ?></td>
                                                    <td>
                                                        <a title="Edit pack" href="<?php print $view['router']->generate('packs_edit', ['pack' => $p->getId()]); ?>" target="_blank"></a>
                                                        <a title="Remove pack" href="#confirm-remove-pack" data-toggle="modal"></a>
                                                    </td>
                                                </tr>
                                            <?php }
                                        } else { ?>
                                            <tr class="empty">
                                                No downloads yet.
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
<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('sincludes');
print $this->render('StudySauceBundle:Dialogs:confirm-remove-pack.html.php', ['id' => 'confirm-remove-pack']);

$view['slots']->stop();

