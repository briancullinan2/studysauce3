<?php
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;

/** @var Card $card */

?>
<label class="input answers type-mc">
    <textarea name="answers" placeholder="one per line"><?php print implode("\n", $card->getAnswers()->map(function (Answer $a) {return $a->getValue();})->toArray()); ?></textarea>
</label>
<label class="input answers type-sa">
    <input type="text" name="answers" placeholder="fill in the blank" value="<?php print (!empty($card->getCorrect()) ? trim($card->getCorrect()->getValue(), '$^') : ''); ?>" />
</label>