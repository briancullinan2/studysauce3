<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Group;
use StudySauce\Bundle\Entity\Invite;use StudySauce\Bundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Bundle\FrameworkBundle\Templating\TimedPhpEngine;
use Symfony\Component\HttpKernel\Controller\ControllerReference;

/** @var GlobalVariables $app */
/** @var $view TimedPhpEngine */
/** @var $user User */
/** @var string $code */
$context = !empty($context) ? $context : jQuery($this);
$tab = $context->filter('.panel-pane');

$httpRequest = $app->getRequest();
$cookie = $httpRequest->cookies->get('hasChild');
$hasChild = !empty($cookie) && $cookie == 'true';

if(isset($invites)) {
    $publicGroups = [];
    $inviteObj = [];
    $visited = [];
    $groupStr = '';
    foreach ($invites as $invite) {
        /** @var Invite $invite */
        $group = $invite->getGroup();
        $inviteObj[count($inviteObj)] = AdminController::toFirewalledEntityArray($invite, ['ss_group' => AdminController::$defaultTables['ss_group'], 'invite' => AdminController::$defaultTables['invite']]);
        do {
            $publicGroups[count($publicGroups)] = AdminController::toFirewalledEntityArray($group, ['ss_group' => AdminController::$defaultTables['ss_group']], 1);
            $hasParent = true;
            if (empty($group->getParent()) || $group->getParent()->getId() == $group->getId()) {
                $hasParent = false;
                if (!$group->getDeleted() && !in_array($group->getId(), $visited)) {
                    $groupStr = implode('', [$groupStr, '<option value="' , $group->getId() , '">' , $group->getName() , '</option>']);
                }
            }
            $visited[count($visited)] = $group->getId();
            if (!empty($group->getParent()) && $group->getParent()->getId() != $group->getId()) {
                $group = $group->getParent();
            }
        } while ($hasParent && !in_array($group->getId(), $visited));
    }
}

// update existing tab
if($tab->length > 0) {
    $tab->find('[type="submit"]')->text($hasChild ? 'Next' : 'Done');

    $parentVal = $tab->find('.parent select')->val();
    $year = $tab->find('.year select');
    $yearVal = $year->val();
    $school = $tab->find('.school select');
    $schoolVal = $school->val();

    $publicGroups = $tab->data('groups');
    $invites = $tab->data('invites');
    $yearStr = '';
    $schoolStr = '';
    $visited = [];
    foreach($publicGroups as $g) {
        $group = applyEntityObj($g);
        /** @var Group $group */
        if(!empty($group->getParent()) && !$group->getParent()->getDeleted() && !in_array($group->getParent()->getId(), $visited)) {

            $visited[count($visited)] = $group->getParent()->getId();
            if($group->getParent()->getId() == $parentVal) {
                $yearStr = implode('', [$yearStr, '<option value="' , $group->getId() , '">' , $group->getName() , '</option>']);
            }
            if($group->getParent()->getId() == $yearVal) {
                $schoolStr = implode('', [$schoolStr, '<option value="' , $group->getId() , '">' , $group->getName() , '</option>']);
            }
        }
    }
    foreach($invites as $i) {
        $invite = applyEntityObj($i);
        /** @var Invite $invite */
        if($invite->getGroup()->getId() == $yearVal) {
            $schoolStr = implode('', [$schoolStr, '<option value="' , $invite->getCode() , '">' , $invite->getGroup()->getName() , '</option>']);
        }
    }

    // update list of groups
    $year->find('option:not(:first-of-type)')->remove();
    $year->append($yearStr)->val($yearVal);

    $school->find('option:not(:first-of-type)')->remove();
    $school->append($schoolStr)->val($schoolVal);
}
else {

    $view->extend('StudySauceBundle:Shared:dashboard.html.php');

    $view['slots']->start('stylesheets');
    foreach ($view['assetic']->stylesheets(['@StudySauceBundle/Resources/public/css/account.css'], [], ['output' => 'bundles/studysauce/css/*.css']) as $url) { ?>
        <link type="text/css" rel="stylesheet" href="<?php echo($view->escape($url)); ?>"/>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('javascripts');
    foreach ($view['assetic']->javascripts(['@StudySauceBundle/Resources/public/js/register.js'], [], ['output' => 'bundles/studysauce/js/*.js']) as $url) { ?>
        <script type="text/javascript" src="<?php print ($view->escape($url)); ?>"></script>
    <?php }
    $view['slots']->stop();

    $view['slots']->start('body'); ?>

    <div class="panel-pane" id="register_child" data-groups="<?php print ($view->escape(json_encode($publicGroups))); ?>" data-invites="<?php print ($view->escape(json_encode($inviteObj))); ?>">
        <div class="pane-content">
            <h2>Register a child</h2>
            <form action="<?php print ($view['router']->generate('account_create')); ?>" method="post">
                <?php if (!empty($code)) { ?>
                    <input type="hidden" name="_code" value="<?php print ($code); ?>"/>
                <?php } ?>
                <input type="hidden" name="_remember_me" value="on"/>
                <label class="input first-name"><input type="text" name="childFirst" placeholder="Child first name"
                                                       value="<?php print (isset($first) ? $first : ''); ?>"></label>
                <label class="input last-name"><input type="text" name="childLast" placeholder="Child last name"
                                                      value="<?php print (isset($last) ? $last : ''); ?>"></label>
                <input type="hidden" name="csrf_token" value="<?php print ($csrf_token); ?>"/>
                <label class="input parent"><select name="parent">
                        <option value="">- Select child&rsquo;s school system -</option>
                        <?php print ($groupStr); ?>
                    </select></label>
                <label class="input year"><select name="year">
                        <option value="">- Select child&rsquo;s school year -</option>
                    </select></label>
                <label class="input school"><select name="school">
                        <option value="">- Select child&rsquo;s school name -</option>
                    </select>
                </label>
                <div class="form-actions highlighted-link invalid">
                    <label class="checkbox hasChild"><input name="hasChild" type="checkbox" value="true" <?php print ($hasChild ? 'checked="checked"' : ''); ?>><i></i><span>Register another child</span></label>
                    <div class="invalid-only">You must complete all fields before moving on.</div>
                    <a href="<?php print ($view['router']->generate('home')); ?>">Cancel</a>
                    <button type="submit" value="#save-user"
                            class="more"><?php print ($hasChild ? 'Next' : 'Done'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <?php $view['slots']->stop();
}