<?php
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;

/** @var Card $card */
?>
<label class="input correct">
    <input type="text" name="correct" placeholder="for display only" value="<?php print (!empty($card->getCorrect()) ? $view->escape($card->getCorrect()->getValue()) : ''); ?>" />
</label>
<label class="input correct type-mc">
    <select><?php foreach($card->getAnswers()->toArray() as $a) {
            /** @var Answer $a */
            ?>
            <option value="<?php print $a->getValue(); ?>" <?php print ($a->getCorrect() ? 'selected="selected"' : ''); ?>><?php print $a->getValue(); ?></option>
        <?php } ?></select>
</label>
<label class="radio correct type-tf">
    <span>True</span>
    <input type="radio" name="correct-<?php print $card->getId(); ?>" value="true" <?php print (!empty($card->getCorrect()) && preg_match('/t/i', $card->getCorrect()->getValue()) ? 'checked="checked"' : ''); ?> />
    <i></i>
</label>
<label class="radio correct type-tf">
    <input type="radio" name="correct-<?php print $card->getId(); ?>" value="false" <?php print (!empty($card->getCorrect()) && preg_match('/f/i', $card->getCorrect()->getValue()) ? 'checked="checked"' : ''); ?> />
    <i></i>
    <span>False</span>
</label>
<label class="input correct type-sa">
    <input type="text" name="correct" placeholder="fill in the blank" value="<?php print (!empty($card->getCorrect()) ? trim($card->getCorrect()->getValue(), '$^') : ''); ?>" />
</label>

