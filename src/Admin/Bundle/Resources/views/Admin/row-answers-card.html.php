<?php
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;

/** @var Card $card */

?>
<label class="input answers type-mc">
    <textarea name="answers" placeholder="one per line"><?php print implode("\n", $card->getAnswers()->filter(function(Answer $a) {return !$a->getDeleted();})->map(function (Answer $a) {return $a->getValue();})->toArray()); ?></textarea>
</label>
