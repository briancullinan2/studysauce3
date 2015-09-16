<?php
use StudySauce\Bundle\Entity\User;

/** @var User $user */
$user = $app->getUser();

$view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
    Did you study outside Study Sauce?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
    <p>No problem. Add your hours below.
        <br />
        <br /></p>
    <form action="<?php print $view['router']->generate('checkin_update'); ?>" method="post">
        <div class="class-name">
            <label class="input"><span>Class</span>
                <select>
                    <option value="">- Select -</option>
            </select></label>
        </div>
        <div class="date">
            <label class="input"><span>Date</span>
                <input type="text" value=""></label>
        </div>
        <div class="time">
            <label class="input"><span>Time (min)</span>
                <select>
                    <option value="">- Select -</option>
                    <option value="30">30 min</option>
                    <option value="45">45 min</option>
                    <option value="60">60 min</option>
                </select></label>
        </div>
        <div class="highlighted-link invalid clearfix">
            <br/>
            <label class="checkbox"><input type="checkbox"><i></i>Add another</label>
            <button type="submit" value="#submit-checkin" class="more">Save</button>
            <br />
            <br />
            <br />
            <div style="float:left; text-align: left;">* Research shows you shouldn't study > 60 minutes without a break.</div>
        </div>
    </form>
<?php $view['slots']->stop();
