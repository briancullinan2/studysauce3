<?php
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Pack;
use StudySauce\Bundle\Entity\Payment;
use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var GlobalVariables $app */
/** @var $view TimedPhpEngine */
/** @var $user User */
/** @var Payment $payment */

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
            <form action="<?php print $view['router']->generate('packs_create'); ?>" method="post">
                <div class="pane-top">
                    <h2>Create a pack</h2>
                    <label class="input title">
                        <input name="title" placeholder="Title your pack" value=""/>
                    </label>
                    <label class="input creator">
                        <input name="creator" placeholder="Creator name"
                               value="<?php print $app->getUser()->getFirst(); ?> <?php print $app->getUser()->getLast(); ?>"/>
                        <small>* If the pack is public this is the name others will see.</small>
                    </label>

                    <h3>Add questions and answers by pasting from excel to the space below (make sure you have questions in
                        column 1 and responses in column 2).</h3>

                </div>

                <div class="results">
                <header>
                    <label>Type</label>
                    <label>Prompt</label>
                    <label>Response</label>
                    <label>Answer/Context</label>
                    <label></label>
                </header>
                <?php for($i = 0; $i < 5; $i++) { ?>
                    <div class="card-row edit">
                        <label class="input type">
                            <select name="type">
                                <option value="">Default</option>
                                <option value="mc" data-text="Multiple choice">MC</option>
                                <option value="tf" data-text="True/False">TF</option>
                            </select>
                        </label>
                        <label class="input content">
                            <input type="text" name="content" placeholder="Prompt" value="" />
                        </label>
                        <label class="input response">
                            <span>X</span>
                            <input type="text" name="response" placeholder="Response" value="" />
                            <span>C</span>
                            <input type="text" name="response" placeholder="Response" value="" />
                        </label>
                        <label class="input response type-mc">
                            <textarea name="response" placeholder="one per line"></textarea>
                        </label>
                        <label class="input response type-tf">
                            <span>T</span>
                            <input type="text" name="response" placeholder="True" value="" />
                            <span>F</span>
                            <input type="text" name="response" placeholder="False" value="" />
                        </label>
                        <label class="input answer">
                            <input type="text" name="answer" placeholder="Description" value="" />
                        </label>
                        <div class="highlighted-link">
                            <a title="Remove card" href="#remove-card" data-toggle="modal"></a>
                            <label class="checkbox"><input type="checkbox" name="selected"><i></i></label>
                        </div>
                    </div>
                <?php } ?>
                </div>
                <div class="highlighted-link form-actions invalid">
                    <a href="#add-card" class="big-add">Add <span>+</span> card</a>
                    <a href="#create-new" class="more">Create Pack</a>
                </div>
            </form>
            <div class="pane-bottom">
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
                        <td colspan="5">
                            <table class="results">
                                <tbody>
                                <?php
                                if ($p->getCards()->count() > 0) {
                                    foreach ($p->getCards()->toArray() as $c) {
                                        /** @var Card $c */
                                        ?>
                                        <tr>
                                            <td><?php print $c->getContent(); ?></td>
                                            <td><?php print $c->getResponseContent(); ?></td>
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
        </div>
    </div>
<?php $view['slots']->stop(); ?>

<?php $view['slots']->start('sincludes');

$view['slots']->stop();

