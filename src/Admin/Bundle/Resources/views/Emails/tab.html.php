<?php
use Doctrine\ORM\EntityRepository;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Mail;
use StudySauce\Bundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Session;

/** @var \Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables $app */
/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle:Shared:dashboard.html.php');

$view['slots']->start('stylesheets');
foreach ($view['assetic']->stylesheets(['@AdminBundle/Resources/public/css/emails.css'],[],['output' => 'bundles/admin/css/*.css']) as $url): ?>
    <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
<?php endforeach;
$view['slots']->stop();

$view['slots']->start('javascripts');
foreach ($view['assetic']->javascripts(['@AdminBundle/Resources/public/js/emails.js'],[],['output' => 'bundles/admin/js/*.js']) as $url): ?>
    <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
<?php endforeach; ?>
<script type="text/javascript">
    window.entities = JSON.parse('<?php print json_encode($entities); ?>');
</script>
<?php $view['slots']->stop();

$view['slots']->start('body'); ?>
    <div class="panel-pane" id="emails">
        <div class="pane-content">
            <ul class="nav nav-tabs">
                <li class="active"><a href="#history" data-target="#history" data-toggle="tab">Activity</a></li>
                <li><a href="#templates" data-target="#templates" data-toggle="tab">Templates</a></li>
                <li><a href="#send-email" data-target="#send-email" data-toggle="tab">New email</a></li>
            </ul>
            <div class="tab-content">
                <div id="history" class="tab-pane active">
                    <h2>Email activity</h2>
                    <div class="search">
                        <label class="input"><input name="search" type="text" value="" placeholder="Search"/></label>
                    </div>
                    <?php print $view->render('AdminBundle:Shared:paginate.html.php', ['total' => $total]); ?>
                    <table class="results history">
                        <thead>
                        <tr>
                            <th colspan="2"><label class="input">
                                    <span>Recent: <?php print $recent; ?></span><br/>
                                    <input type="text" name="created" value="" placeholder="Created"/>
                                </label>
                                <div></div>
                            </th>
                            <th><label class="input"><span>Recipient: </span><br/>
                                    <select name="recipient">
                                        <option>Recipient</option>
                                        <option>Ascending (A-Z)</option>
                                        <option>Descending (Z-A)</option>
                                    </select>
                                </label>
                            </th>
                            <th><label><span>Status: </span><br/>
                                    <select name="status">
                                        <option value="">Status</option>
                                        <option value="_ascending">Ascending (A-Z)</option>
                                        <option value="_descending">Descending (Z-A)</option>
                                        <?php foreach($status as $n => $c) { ?>
                                            <option value="<?php print $c; ?>"><?php print str_replace('STATUS_', '', $n); ?></option>
                                        <?php } ?>
                                    </select></label></th>
                            <th><label class="input"><span>Sender: </span><br/>
                                    <select name="sender">
                                        <option>Sender</option>
                                        <option>Ascending (A-Z)</option>
                                        <option>Descending (Z-A)</option>
                                    </select>
                                </label>
                            </th>
                            <th><label class="input">
                                    <span>Template: </span><br/>
                                    <select name="template">
                                        <option value="">Template</option>
                                        <?php /*foreach ($templates as $i => $email) {
                                        $name = ucwords(str_replace('-', ' ', $email['id']));
                                        ?>
                                        <option value="<?php print $email['id']; ?>"><?php print $name; ?> (<?php print $email['count']; ?>)</option>
                                        <?php }*/ ?>
                                        <optgroup label="New account">
                                            <option value="welcome-student">Student welcome (<?php print $templates['welcome-student']['count']; ?>)</option>
                                            <option value="welcome-partner">Partner welcome (<?php print $templates['welcome-partner']['count']; ?>)</option>
                                            <option>B2B student welcome</option>
                                            <option>B2B advisor welcome</option>
                                            <option>3 day marketing</option>
                                        </optgroup>
                                        <optgroup label="Confirmation">
                                            <option value="invoice">Student purchase (<?php print $templates['invoice']['count']; ?>)</option>
                                            <option>Parent purchase</option>
                                            <option>Partner joined</option>
                                            <option>Declined cc email</option>
                                        </optgroup>
                                        <optgroup label="Invitation">
                                            <option value="prepay">Prepaid notification (<?php print $templates['prepay']['count']; ?>)</option>
                                            <option value="parent-invite">Bill my parents invite (<?php print $templates['parent-invite']['count']; ?>)</option>
                                            <option value="partner-invite">Partner invite (<?php print $templates['partner-invite']['count']; ?>)</option>
                                            <option value="group-invite">B2B student invite (<?php print $templates['group-invite']['count']; ?>)</option>
                                        </optgroup>
                                        <optgroup label="Password request">
                                            <option value="reset-password">Password change (<?php print $templates['reset-password']['count']; ?>)</option>
                                        </optgroup>
                                        <optgroup label="Reminder">
                                            <option value="deadline-reminder">Deadline reminder (<?php print $templates['deadline-reminder']['count']; ?>)</option>
                                            <option>Student course completion</option>
                                            <option value="adviser-completion">Advisor course completion (<?php print $templates['adviser-completion']['count']; ?>)</option>
                                        </optgroup>
                                        <optgroup label="Newsletter">
                                            <option>August</option>
                                            <option>September</option>
                                            <option>October</option>
                                            <option>November</option>
                                            <option>December</option>
                                            <option>January</option>
                                            <option>February</option>
                                            <option>March</option>
                                            <option>April</option>
                                            <option>May</option>
                                            <option>June</option>
                                        </optgroup>
                                        <optgroup label="Administrator">
                                            <option value="administrator">Error email (<?php print $templates['administrator']['count']; ?>)</option>
                                            <option value="contact-message">Contact us (<?php print $templates['contact-message']['count']; ?>)</option>
                                        </optgroup>
                                        <optgroup label="Inactivity">
                                            <option>Deadline reminder</option>
                                            <option>Study notes</option>
                                            <option>Procrastination</option>
                                            <option>Grade calculator</option>
                                            <option>Distractions</option>
                                            <option>Studying for tests</option>
                                            <option>Test-taking</option>
                                            <option>Spaced repetition</option>
                                            <option>Check in/metrics</option>
                                            <option>Study environment</option>
                                            <option>Accountability partner</option>
                                            <option>Active reading</option>
                                            <option>Goals</option>
                                            <option>Interleaving</option>
                                            <option>Group study</option>
                                            <option>Teach to learn</option>
                                        </optgroup>
                                        <optgroup label="Digest">
                                            <option>Weekly digest (adviser)</option>
                                        </optgroup>
                                    </select>
                                </label>
                            </th>
                            <th><label><span>Actions</span><br/>
                                    <select name="actions">
                                        <option value="">Select All</option>
                                        <option value="delete">Delete All</option>
                                        <option value="cancel">Cancel All</option>
                                        <option value="email">Email All</option>
                                        <option value="export">Export All</option>
                                        <option value="export">Clear All</option>
                                    </select></label></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($emails as $i => $email)
                        /** @var Mail $email */
                        { ?>
                            <tr class="email-id-<?php print $email->getId(); ?> read-only ">
                                <td><?php print $email->getCreated()->format('j M'); ?></td>
                                <td><?php print $email->getCreated()->format('H:i'); ?></td>
                                <td><?php
                                    $recipient = $email->getRecipient();
                                    /** @var EntityRepository $repository */
                                    $u = $repository->matching(\Doctrine\Common\Collections\Criteria::create()
                                        ->andWhere(\Doctrine\Common\Collections\Criteria::expr()
                                            ->contains('email', explode('@', $recipient)[0]))
                                        ->andWhere(\Doctrine\Common\Collections\Criteria::expr()
                                            ->contains('email', explode('@', $recipient)[1])))->first();
                                    if(!empty($u)) { ?>
                                        <span class="input"><?php print $u->getFirst(); ?></span>
                                        <span class="input"><?php print $u->getLast(); ?></span>
                                        <span class="input"><?php print $u->getEmail(); ?></span>
                                    <?php }
                                    else
                                        print $recipient; ?></td>
                                <td><?php print str_replace('STATUS_', '', array_search($email->getStatus(), $status)); ?></td>
                                <td><?php
                                    $sender = $email->getSender();
                                    /** @var EntityRepository $repository */
                                    $u = $repository->matching(\Doctrine\Common\Collections\Criteria::create()
                                        ->andWhere(\Doctrine\Common\Collections\Criteria::expr()
                                            ->contains('email', explode('@', $sender)[0]))
                                        ->andWhere(\Doctrine\Common\Collections\Criteria::expr()
                                            ->contains('email', explode('@', $sender)[1])))->first();
                                    if(!empty($u)) { ?>
                                        <span class="input"><?php print $u->getFirst(); ?></span>
                                        <span class="input"><?php print $u->getLast(); ?></span>
                                        <span class="input"><?php print $u->getEmail(); ?></span>
                                    <?php }
                                    else
                                        print $sender; ?></td>
                                <td><?php
                                    print $email->getTemplate(); ?></td>
                                <td>
                                    <a href="#send-email" data-toggle="tab"></a>
                                    <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div id="templates" class="tab-pane">
                    <table class="results templates">
                        <thead>
                        <tr>
                            <th><label><span>Name: </span></label></th>
                            <th><label class="input"><span>Recent: <?php print $recent; ?></span></label></th>
                            <th><label><span>Actions</span></label></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($templates as $i => $email) {
                            ?>
                            <tr class="email-id-<?php print $email['id']; ?> read-only ">
                                <td><?php print $email['id']; ?></td>
                                <td><?php print $email['count']; ?></td>
                                <td>
                                    <a href="#send-email" data-toggle="tab"></a>
                                    <label class="checkbox"><input type="checkbox" name="selected"/><i></i></label>
                                </td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <div id="send-email" class="tab-pane">
                    <div class="pane-top save-template">
                        <label class="input"><span>Template</span>
                            <select name="template">
                                <option value="">Select email template</option>
                                <?php foreach ($templates as $i => $email) { ?>
                                    <option value="<?php print $email['id']; ?>"><?php print $email['id']; ?></option>
                                <?php } ?>
                            </select></label>
                        <label class="input save"><input type="text" name="template-name" placeholder="Template name" /></label> <a href="#save-template" class="more">Save</a>
                        <label class="input"><span>Subject</span><input type="text" name="subject"/></label>
                    </div>
                    <table class="results variables">
                        <thead>
                        <tr>
                            <th><label>User</label></th>
                            <th><label></label> <a href="#remove-field"></a></th>
                            <th><label></label> <a href="#remove-field"></a></th>
                            <th><a href="#add-field">+</a></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><label class="input"><input name="userFirst" placeholder="First" type="text"/></label></td>
                            <td><label class="input"><input name="userLast" placeholder="Last" type="text"/></label></td>
                            <td><label class="input"><input name="userEmail" placeholder="Email" type="text"/></label></td>
                            <td><a href="#remove-line"></a></td>
                        </tr>
                        </tbody>
                    </table>
                    <div class="highlighted-link">
                        <a href="#add-line" class="big-add">Add <span>+</span> line</a>
                        <a href="#send-confirm" class="more" data-toggle="modal">Send now</a>
                    </div>
                    <div class="pane-bottom">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#editor1">Preview</a></li>
                        <li><a href="#markdown">Source</a></li>
                        <li><a href="#headers">Headers</a></li>
                    </ul>
                    <div class="preview"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $view['slots']->stop();

$view['slots']->start('sincludes');
print $this->render('AdminBundle:Dialogs:send-confirm.html.php',['id' => 'send-confirm']);
$view['slots']->stop();
