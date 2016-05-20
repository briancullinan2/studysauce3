<?php
use Admin\Bundle\Controller\AdminController;
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;

AdminController::$radioCounter++;
/** @var Card $card */

$answers = [];
foreach($card->getAnswers()->toArray() as $a) {
    /** @var Answer $a */
    if(!$a->getDeleted() && !in_array($a->getValue(), $answers)) {
        $answers[count($answers)] = $a->getValue();
    }
}
if(empty($answers)) {
    $answers = [''];
}

?>
<label class="input correct">
    <textarea name="correct" placeholder="Answer"><?php print ($view->escape(!empty($card->getCorrect()) ? $view->escape($card->getCorrect()->getValue()) : trim($card->getResponseContent()))); ?></textarea>
</label>
<div class="correct type-mc">
    <div class="radios">
        <?php foreach($answers as $a) { ?>
            <label class="radio"><input type="radio" name="correct-mc-<?php print (!empty($card->getId()) ? $card->getId() : AdminController::$radioCounter); ?>" value="<?php print ($view->escape($a)); ?>" <?php print (!empty($card->getCorrect()) && $a == $card->getCorrect()->getValue() ? 'checked="checked"' : ''); ?> /><i></i><span><?php print ($view->escape($a)); ?></span></label>
        <?php } ?>
    </div>
    <label class="input">
        <textarea name="answers" data-delimiter="\s*\n\s*|\s*\\n\s*" placeholder="Answers"><?php print (implode("\n", $answers)); ?></textarea>
    </label>
</div>
<label class="radio correct type-tf">
    <input type="radio" name="correct-<?php print (!empty($card->getId()) ? $card->getId() : AdminController::$radioCounter); ?>" value="true" <?php print (!empty($card->getCorrect()) && preg_match('/t/i', $card->getCorrect()->getValue()) ? 'checked="checked"' : ''); ?> />
    <i></i>
    <span>True</span>
</label>
<label class="radio correct type-tf">
    <input type="radio" name="correct-<?php print (!empty($card->getId()) ? $card->getId() : AdminController::$radioCounter); ?>" value="false" <?php print (!empty($card->getCorrect()) && preg_match('/f/i', $card->getCorrect()->getValue()) ? 'checked="checked"' : ''); ?> />
    <i></i>
    <span>False</span>
</label>
<label class="input correct type-sa">
    <textarea name="correct" placeholder="Answer"><?php print ($view->escape(!empty($card->getCorrect()) ? trim($card->getCorrect()->getValue(), '$^') : '')); ?></textarea>
</label>
