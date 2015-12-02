<?php

use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/import.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/import.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="import">
        <div class="pane-content">
            <form action="<?php print $view['router']->generate('import_save'); ?>" method="post">
                <div class="pane-top">
                <h2>Invite students to Study Sauce</h2>

                <h3>Paste your comma separated list, one invite per line. Enter their first name, last name, and email
                    below to invite them to Study Sauce.</h3>
                </div>
                <div class="results">
                    <header>
                        <label>First name</label>
                        <label>Last name</label>
                        <label>Email</label>
                        <label>Group</label>
                    </header>
                    <?php for($i = 0; $i < 5; $i++) { ?>
                    <div class="import-row edit invalid">
                        <label class="input first-name">
                            <input type="text" placeholder="First name"/>
                        </label>
                        <label class="input last-name">
                            <input type="text" placeholder="Last name"/>
                        </label>
                        <label class="input email">
                            <input type="text" placeholder="Email"/>
                        </label>
                        <label class="input group">
                            <select>
                                <option value="">Group</option>
                                <?php foreach ($groups as $group) {
                                    /** @var Group $group */ ?>
                                    <option
                                        value="<?php print $group->getId(); ?>"><?php print $group->getName(); ?></option>
                                <?php } ?>
                            </select>
                        </label>
                    </div>
                    <?php } ?>
                </div>
                <div class="highlighted-link form-actions invalid">
                    <a href="#add-user" class="big-add">Add <span>+</span> user</a>
                    <a href="#save-group" class="more">Import</a>
                </div>
            </form>
            <div class="pane-bottom results">
                <header>
                    <label>First name</label>
                    <label>Last name</label>
                    <label>Email</label>
                    <label>Group</label>
                    <label></label>
                </header>
                <?php foreach ($user->getInvites()->toArray() as $g) {
                    /** @var Invite $g */
                    ?>
                    <div class="import-row invite-id-<?php print $g->getId(); ?> read-only invalid">
                        <label class="input first-name">
                            <input type="text" placeholder="First name" value="<?php print $g->getFirst(); ?>"/>
                        </label>
                        <label class="input last-name">
                            <input type="text" placeholder="Last name" value="<?php print $g->getLast(); ?>"/>
                        </label>
                        <label class="input email">
                            <input type="text" placeholder="Email" value="<?php print $g->getEmail(); ?>"/>
                        </label>
                        <label class="input group">
                            <select>
                                <option value="">None</option>
                                <?php foreach ($groups as $group) {
                                    /** @var Group $group */ ?>
                                    <option
                                        value="<?php print $group->getId(); ?>" <?php print ($g->getGroup() == $group ? 'selected="selected"' : ''); ?>><?php print $group->getName(); ?></option>
                                <?php } ?>
                            </select>
                        </label>
                        <a href="<?php print $view['router']->generate('register', ['_code' => $g->getCode()], UrlGeneratorInterface::ABSOLUTE_URL); ?>"><?php print $g->getCode(); ?></a>
                    </div>
                <?php } ?>
            </div>

        </div>
    </div>
<?php $view['slots']->stop();
