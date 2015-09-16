<?php $view->extend('StudySauceBundle::Dialogs/dialog.html.php');

$view['slots']->start('modal-header') ?>
What if:
<?php $view['slots']->stop();

$view['slots']->start('modal-body') ?>
<ul class="nav nav-tabs">
    <li class="active"><a href="#class-grade" data-target="#class-grade" data-toggle="tab">Class grade</a></li>
    <li><a href="#term-gpa" data-target="#term-gpa" data-toggle="tab">Term GPA</a></li>
    <li><a href="#overall-gpa" data-target="#overall-gpa" data-toggle="tab">Overall GPA</a></li>
</ul>
<div class="tab-content">
    <div id="class-grade" class="tab-pane active">
        <label class="input">
            <select class="class-name"></select>
        </label><div class="description">Class name</div>
        <span class="current-grade">C-</span><div class="description">Current grade</div>
        <label class="input"><select>
                <option value="">-Select-</option>
                <?php for($i = 0; $i < count($scale); $i++) {
                    if (!empty($scale[$i]) && count($scale[$i]) == 4 && !empty($scale[$i][0])) { ?>
                        <option value="<?php print $scale[$i][0]; ?>"><?php print $scale[$i][0]; ?></option>
                    <?php }} ?>
            </select></label><div class="description">Grade I want</div>
        <span class="result">98%</span><div class="description">Grade I need on my remaining assignments.</div>
        <div class="calc-completed">* Class already 100% complete</div>
    </div>
    <div id="term-gpa" class="tab-pane">
        If I make these grades,
        <div class="class-row">
            <label class="input"><select>
                    <option value="">-Select-</option>
                    <?php for($i = 0; $i < count($scale); $i++) {
                        if (!empty($scale[$i]) && count($scale[$i]) == 4 && !empty($scale[$i][0])) { ?>
                            <option value="<?php print $scale[$i][0]; ?>"><?php print $scale[$i][0]; ?></option>
                        <?php }} ?>
                </select></label>
            <div class="class-name description"></div>
            <div class="hours"></div>
        </div>
        <span class="result">3.0</span><div class="description">This will be my GPA.</div>
    </div>
    <div id="overall-gpa" class="tab-pane">
        <span class="current-grade">3.00</span><div class="description">Current GPA</div>
        <label class="input"><select class="overall-gpa">
                <option value="">-Select-</option>
                <?php for($i = 40; $i >= 0; $i--) { ?>
                    <option value="<?php print round($i / 10, 1); ?>"><?php print number_format($i / 10, 1); ?></option>
                <?php } ?>
            </select></label><div class="description">Target overall GPA</div>
        <span class="result">4.0</span><div class="description">GPA needed this term</div>
    </div>
    <div class="calc-unk">* To calculate you must designate hours for each class</div>
</div>
<?php $view['slots']->stop();
