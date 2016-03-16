<?php
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;

/** @var Card $card */
$packTitle = !empty($card->getPack()) ? $card->getPack()->getTitle() : '';
$cardCount = !empty($card->getPack()) ? ($card->getIndex() + 1 . ' of ' . $card->getPack()->getCards()->count()) : '1 or 10';
$content = $card->getContent();
$content = preg_replace('/\\\\n(\\\\r)?/i', "\n", $content);
/** @var Answer[] $answers */
$answers = $card->getAnswers()->filter(function (Answer $a) {return !$a->getDeleted();})->toArray();
if (($hasUrl = preg_match('/https:\/\/.*/i', $content, $matches)) > 0) {
    $url = trim($matches[0]);
    $isImage = substr($url, -4) == '.jpg' || substr($url, -4) == '.jpeg' || substr($url, -4) == '.gif' || substr($url, -4) == '.png';
    $isAudio = substr($url, -4) == '.mp3' || substr($url, -4) == '.m4a';
    $content = preg_replace('/\s*\n\r?/i', '\n', trim(str_replace($url, '', $content)));
}
?>
<h3>Preview: </h3>
<?php if (empty($card->getResponseType()) || empty($card->getId())) { ?>
    <div class="preview-card">
        <div class="preview-inner">
            <?php if (!empty($isImage)) { ?><img src="<?php print $url; ?>" /><?php } ?>
            <?php if (empty($isImage) && empty($isAudio)) { ?>
                <div class="preview-content"><?php print $view->escape($content); ?></div>
            <?php } ?>
        </div>
        <div class="preview-tap">Tap to see answer</div>
    </div>
    <div class="preview-card preview-answer">
        <div class="preview-prompt">
            <?php if (!empty($isImage)) { ?><img src="<?php print $url; ?>" /><?php } ?>
            <?php if (empty($isImage) && empty($isAudio)) { ?>
                <div class="preview-content"><?php print $view->escape($content); ?></div>
            <?php } ?>
        </div>
        <div class="preview-inner">
            <div class="preview-correct">Correct answer:</div>
            <div class="preview-content"><?php print (!empty($card->getCorrect()) ? $card->getCorrect()->getContent() : ''); ?></div>
        </div>
        <div class="preview-wrong">✘</div>
        <div class="preview-guess">Did you guess correctly?</div>
        <div class="preview-right">✔︎</div>
    </div>
<?php } ?>
<?php if ($card->getResponseType() == 'mc' || empty($card->getId())) { ?>
    <div class="preview-card type-mc">
        <div class="preview-inner">
            <?php if (!empty($isImage)) { ?><img src="<?php print $url; ?>" /><?php } ?>
            <?php if (empty($isImage) && empty($isAudio)) { ?>
                <div class="preview-content"><?php print $view->escape($content); ?></div>
            <?php } ?>
        </div>
        <div class="preview-response"><?php print (count($answers) > 0 ? $view->escape($answers[0]->getContent()) : ''); ?></div>
        <div class="preview-response"><?php print (count($answers) > 1 ? $view->escape($answers[1]->getContent()) : ''); ?></div>
        <div class="preview-response"><?php print (count($answers) > 2 ? $view->escape($answers[2]->getContent()) : ''); ?></div>
        <div class="preview-response"><?php print (count($answers) > 3 ? $view->escape($answers[3]->getContent()) : ''); ?></div>
    </div>
<?php } ?>
<?php if ($card->getResponseType() == 'tf' || empty($card->getId())) { ?>
    <div class="preview-card type-tf">
        <div class="preview-inner">
            <?php if (!empty($isImage)) { ?><img src="<?php print $url; ?>" /><?php } ?>
            <?php if (empty($isImage) && empty($isAudio)) { ?>
                <div class="preview-content"><?php print $view->escape($content); ?></div>
            <?php } ?>
        </div>
        <div class="preview-false">False</div>
        <div class="preview-guess"> </div>
        <div class="preview-true">True</div>
    </div>
<?php } ?>
<?php if ($card->getResponseType() == 'sa' || empty($card->getId())) { ?>
    <div class="preview-card type-sa">
        <div class="preview-inner">
            <?php if (!empty($isImage)) { ?><img src="<?php print $url; ?>" /><?php } ?>
            <?php if (empty($isImage) && empty($isAudio)) { ?>
                <div class="preview-content"><?php print $view->escape($content); ?></div>
            <?php } ?>
        </div>
        <label class="input"><input type="text" value="<?php print (!empty($isImage) || !empty($isAudio) ? $view->escape($content) : 'Type your answer'); ?>"/></label>
    </div>
<?php } ?>
<?php if (!empty($card->getResponseType()) || empty($card->getId())) { ?>
    <div class="preview-card type-mc type-tf type-sa preview-answer">
        <div class="preview-prompt">
            <?php if (!empty($isImage)) { ?><img src="<?php print $url; ?>" /><?php } ?>
            <?php if (empty($isImage) && empty($isAudio)) { ?>
                <div class="preview-content"><?php print $view->escape($content); ?></div>
            <?php } ?>
        </div>
        <div class="preview-inner">
            <div class="preview-correct">Correct answer:</div>
            <div class="preview-content"><?php print (!empty($card->getCorrect()) ? $card->getCorrect()->getContent() : ''); ?></div>
        </div>
    </div>
<?php } ?>
