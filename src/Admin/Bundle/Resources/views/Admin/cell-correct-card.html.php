<?php
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;

/** @var Card $card */

$answers = array_unique($card->getAnswers()->filter(function(Answer $a) {return !$a->getDeleted();})->map(function (Answer $a) {return $a->getValue();})->toArray());
if(empty($answers)) {
    $answers = [''];
}

?>
<label class="input correct">
    <textarea name="correct" placeholder="Answer"><?php print $view->escape(!empty($card->getCorrect()) ? $view->escape($card->getCorrect()->getValue()) : trim($card->getResponseContent())); ?></textarea>
</label>
<div class="correct type-mc">
    <div class="radios">
        <?php foreach($answers as $a) { ?>
            <label class="radio"><input type="radio" name="correct-mc-<?php print $card->getId(); ?>" value="<?php print $view->escape($a); ?>" <?php print (!empty($card->getCorrect()) && $a == $card->getCorrect()->getValue() ? 'checked="checked"' : ''); ?> /><i></i><span><?php print $view->escape($a); ?></span></label>
        <?php } ?>
    </div>
    <label class="input">
        <textarea name="answers" placeholder="Answers"><?php print implode("\n", $answers); ?></textarea>
    </label>
</div>
<label class="radio correct type-tf">
    <input type="radio" name="correct-<?php print $card->getId(); ?>" value="true" <?php print (!empty($card->getCorrect()) && preg_match('/t/i', $card->getCorrect()->getValue()) ? 'checked="checked"' : ''); ?> />
    <i></i>
    <span>True</span>
</label>
<label class="radio correct type-tf">
    <input type="radio" name="correct-<?php print $card->getId(); ?>" value="false" <?php print (!empty($card->getCorrect()) && preg_match('/f/i', $card->getCorrect()->getValue()) ? 'checked="checked"' : ''); ?> />
    <i></i>
    <span>False</span>
</label>
<label class="input correct type-sa">
    <textarea name="correct" placeholder="Answer"><?php print $view->escape(!empty($card->getCorrect()) ? trim($card->getCorrect()->getValue(), '$^') : ''); ?></textarea>
</label>
