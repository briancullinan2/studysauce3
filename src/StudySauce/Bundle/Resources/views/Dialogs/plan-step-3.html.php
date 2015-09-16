<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
    Step 3 - Would you like calendar alerts?
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
    <form action="<?php print $view['router']->generate('profile_update'); ?>" method="post">
        <br/><br/>
        <header>
            <label>Yes</label>
            <label>No</label>
            <label>Alert</label>
        </header>
        <h4>Classes</h4>
        <?php $alerts = $schedule->getAlerts(); ?>
        <label class="radio"><span>Yes</span><input name="event-type-c" type="radio"
                                                    value="1" <?php print (!isset($alerts['c']) || $alerts['c'] !== 0 ? 'checked="checked"' : ''); ?>><i></i></label>
        <label class="radio"><span>No</span><input name="event-type-c" type="radio"
                                                   value="0" <?php print (isset($alerts['c']) && $alerts['c'] === 0 ? 'checked="checked"' : ''); ?>><i></i></label>
        <label class="input" <?php print (isset($alerts['c']) && $alerts['c'] === 0 ? 'style="visibility:hidden;"' : ''); ?>><span>Alert</span><select name="event-type-c">
                <option value="15" <?php print (isset($alerts['c']) && $alerts['c'] === 15 ? 'selected="selected"' : ''); ?>>15 min</option>
                <option value="30" <?php print (isset($alerts['c']) && $alerts['c'] === 30 ? 'selected="selected"' : ''); ?>>30 min</option>
                <option value="60" <?php print (isset($alerts['c']) && $alerts['c'] === 60 ? 'selected="selected"' : ''); ?>>1 hour</option>
            </select></label>
        <br />
        <h4>Prework</h4>
        <label class="radio"><span>Yes</span><input name="event-type-p" type="radio"
                                                    value="1" <?php print (!isset($alerts['p']) || $alerts['p'] !== 0 ? 'checked="checked"' : ''); ?>><i></i></label>
        <label class="radio"><span>No</span><input name="event-type-p" type="radio"
                                                   value="0" <?php print (isset($alerts['p']) && $alerts['p'] === 0 ? 'checked="checked"' : ''); ?>><i></i></label>
        <label class="input" <?php print (isset($alerts['p']) && $alerts['p'] === 0 ? 'style="visibility:hidden;"' : ''); ?>><span>Alert</span><select name="event-type-p">
                <option value="15" <?php print (isset($alerts['p']) && $alerts['p'] === 15 ? 'selected="selected"' : ''); ?>>15 min</option>
                <option value="30" <?php print (isset($alerts['p']) && $alerts['p'] === 30 ? 'selected="selected"' : ''); ?>>30 min</option>
                <option value="60" <?php print (isset($alerts['p']) && $alerts['p'] === 60 ? 'selected="selected"' : ''); ?>>1 hour</option>
            </select></label>
        <br />
        <h4>Spaced repetition</h4>
        <label class="radio"><span>Yes</span><input name="event-type-sr" type="radio"
                                                    value="1" <?php print (!isset($alerts['sr']) || $alerts['sr'] !== 0 ? 'checked="checked"' : ''); ?>><i></i></label>
        <label class="radio"><span>No</span><input name="event-type-sr" type="radio"
                                                   value="0" <?php print (isset($alerts['sr']) && $alerts['sr'] === 0 ? 'checked="checked"' : ''); ?>><i></i></label>
        <label class="input" <?php print (isset($alerts['sr']) && $alerts['sr'] === 0 ? 'style="visibility:hidden;"' : ''); ?>><span>Alert</span><select name="event-type-sr">
                <option value="15" <?php print (isset($alerts['sr']) && $alerts['sr'] === 15 ? 'selected="selected"' : ''); ?>>15 min</option>
                <option value="30" <?php print (isset($alerts['sr']) && $alerts['sr'] === 30 ? 'selected="selected"' : ''); ?>>30 min</option>
                <option value="60" <?php print (isset($alerts['sr']) && $alerts['sr'] === 60 ? 'selected="selected"' : ''); ?>>1 hour</option>
            </select></label>
        <br />
        <h4>Free study</h4>
        <label class="radio"><span>Yes</span><input name="event-type-f" type="radio"
                                                    value="1" <?php print (!isset($alerts['f']) || $alerts['f'] !== 0 ? 'checked="checked"' : ''); ?>><i></i></label>
        <label class="radio"><span>No</span><input name="event-type-f" type="radio"
                                                   value="0" <?php print (isset($alerts['f']) && $alerts['f'] === 0 ? 'checked="checked"' : ''); ?>><i></i></label>
        <label class="input" <?php print (isset($alerts['f']) && $alerts['f'] === 0 ? 'style="visibility:hidden;"' : ''); ?>><span>Alert</span><select name="event-type-f">
                <option value="15" <?php print (isset($alerts['f']) && $alerts['f'] === 15 ? 'selected="selected"' : ''); ?>>15 min</option>
                <option value="30" <?php print (isset($alerts['f']) && $alerts['f'] === 30 ? 'selected="selected"' : ''); ?>>30 min</option>
                <option value="60" <?php print (isset($alerts['f']) && $alerts['f'] === 60 ? 'selected="selected"' : ''); ?>>1 hour</option>
            </select></label>
        <br />
        <h4>Non-academic events</h4>
        <label class="radio"><span>Yes</span><input name="event-type-o" type="radio"
                                                    value="1" <?php print (!isset($alerts['o']) || $alerts['o'] !== 0 ? 'checked="checked"' : ''); ?>><i></i></label>
        <label class="radio"><span>No</span><input name="event-type-o" type="radio"
                                                   value="0" <?php print (isset($alerts['o']) && $alerts['o'] === 0 ? 'checked="checked"' : ''); ?>><i></i></label>
        <label class="input" <?php print (isset($alerts['o']) && $alerts['o'] === 0 ? 'style="visibility:hidden;"' : ''); ?>><span>Alert</span><select name="event-type-o">
                <option value="15" <?php print (isset($alerts['o']) && $alerts['o'] === 15 ? 'selected="selected"' : ''); ?>>15 min</option>
                <option value="30" <?php print (isset($alerts['o']) && $alerts['o'] === 30 ? 'selected="selected"' : ''); ?>>30 min</option>
                <option value="60" <?php print (isset($alerts['o']) && $alerts['o'] === 60 ? 'selected="selected"' : ''); ?>>1 hour</option>
            </select></label>
        <br/><br/><br/><br/>

        <div class="highlighted-link setup-mode">
            <ul class="dialog-tracker">
                <li>&bullet;</li>
                <li>&bullet;</li>
                <li>&bullet;</li>
                <li>&bullet;</li>
                <li>&bullet;</li>
                <li>&bullet;</li>
            </ul>
            <button type="submit" class="more">Next</button>
        </div>
        <div class="highlighted-link">
            <ul class="dialog-tracker">
                <li><a href="#plan-step-1" title="Class difficulty" data-toggle="modal">&bullet;</a></li>
                <li><a href="#plan-step-3" title="Notifications" data-toggle="modal">&bullet;</a></li>
                <li><a href="#plan-step-4" title="Class type" data-toggle="modal">&bullet;</a></li>
            </ul>
            <button type="submit" class="more">Next</button>
        </div>
    </form>
<?php $view['slots']->stop();
