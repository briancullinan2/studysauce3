<?php
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;

/** @var Card $card */

// check if we need to update or create template
$row = !empty($context) ? $context : jQuery($this);
$preview = $row->find('.preview');
// TODO: how to get data from object or from view in the same way?
// TODO: use applyFields and gatherFields here too?  at the row level?
if($preview->length == 0) {
    return;
}

$type = $card->getResponseType();
$content = $card->getContent();
$content = preg_replace('/\\\\n(\\\\r)?/i', "\n", $content);
$correct = !empty($card->getCorrect()) ? preg_replace('/\\\\n(\\\\r)?/i', "\n", $card->getCorrect()->getContent()) : '';
/** @var Answer[] $answers */
$answersUnique = [];
foreach($card->getAnswers()->toArray() as $answer) {
    /** @var Answer $answer */
    if(!$answer->getDeleted() && !in_array($answer->getContent(), $answersUnique)) {
        $answersUnique[count($answersUnique)] = $answer->getContent();
    }
}
if (($hasUrl = preg_match('/https:\\/\\/.*/i', $content, $matches)) > 0) {
    $url = trim($matches[0]);
}

$isImage = false;
$isAudio = false;
if(!empty($url)) {
    $isImage = substr($url, -4) == '.jpg' || substr($url, -4) == '.jpeg' || substr($url, -4) == '.gif' || substr($url, -4) == '.png';
    $isAudio = substr($url, -4) == '.mp3' || substr($url, -4) == '.m4a';
    $content = trim(str_replace($url, '', $content));
}

$template = $type == ''
    ? $preview->find('.preview-card:not([class*="type-"])')
    : $preview->find(implode('', ['.preview-card.type-' , $type]));
// switch templates if needed
if (2 != $template->length) {
    $preview->children()->remove();

    // this is all prompt content
    $view['slots']->start('card-preview-prompt'); ?>
    <?php if (!empty($isImage)) { ?><img src="<?php print ($url); ?>" class="centerized" /><?php } ?>
    <?php if (!empty($isAudio)) { ?><div class="preview-play"><a href="<?php print ($url); ?>" class="play centerized"></a><a href="#pause" class="pause centerized"></a></div><?php } ?>
    <?php if (empty($isImage) && empty($isAudio)) { ?>
        <div class="preview-content"><div class="centerized"><?php print ($view->escape($content)); ?></div></div>
    <?php } ?>
    <?php $view['slots']->stop();

    // re-render preview completely because type has changed
    $view['slots']->start('card-preview'); ?>
    <h3>Preview: </h3>
    <?php if (empty($type)) { ?>
        <div class="preview-card">
            <div class="preview-inner">
                <?php $view['slots']->output('card-preview-prompt'); ?>
            </div>
            <div class="preview-tap">Tap to see answer</div>
        </div>
        <div class="preview-card preview-answer">
            <div class="preview-prompt">
                <?php $view['slots']->output('card-preview-prompt'); ?>
            </div>
            <div class="preview-inner">
                <div class="preview-correct">Correct answer:</div>
                <div class="preview-content"><div class="centerized"></div></div>
            </div>
            <div class="preview-wrong">✘</div>
            <div class="preview-guess">Did you guess correctly?</div>
            <div class="preview-right">✔︎</div>
        </div>
    <?php } ?>
    <?php if ($type == 'mc') { ?>
        <div class="preview-card type-mc">
            <div class="preview-inner">
                <?php $view['slots']->output('card-preview-prompt'); ?>
            </div>
            <div class="preview-response"><div class="centerized"></div></div>
            <div class="preview-response"><div class="centerized"></div></div>
            <div class="preview-response"><div class="centerized"></div></div>
            <div class="preview-response"><div class="centerized"></div></div>
        </div>
    <?php } ?>
    <?php if ($type == 'tf') { ?>
        <div class="preview-card type-tf">
            <div class="preview-inner">
                <?php $view['slots']->output('card-preview-prompt'); ?>
            </div>
            <div class="preview-false">False</div>
            <div class="preview-guess"> </div>
            <div class="preview-true">True</div>
        </div>
    <?php } ?>
    <?php if ($type == 'sa') { ?>
        <div class="preview-card type-sa">
            <div class="preview-inner">
                <?php $view['slots']->output('card-preview-prompt'); ?>
            </div>
            <label class="input"><input type="text" value=""/></label>
        </div>
    <?php } ?>
    <?php if (!empty($type)) { ?>
        <div class="preview-card type-mc type-tf type-sa preview-answer">
            <div class="preview-prompt">
                <?php $view['slots']->output('card-preview-prompt'); ?>
            </div>
            <div class="preview-inner">
                <div class="preview-correct">Correct answer:</div>
                <div class="preview-content"><div class="centerized"></div></div>
            </div>
        </div>
    <?php }
    $view['slots']->stop();

    $preview->append($view['slots']->get('card-preview'));
}

//$packTitle = !empty($card->getPack()) ? $card->getPack()->getTitle() : '';
//$cardCount = !empty($card->getPack()) ? ($card->getIndex() + 1 . ' of ' . count($card->getPack()->getCards()->toArray())) : '1 or 10';

// replace with image
if($isImage && isset($url)) {
    // TODO: change this if we need to support image and text at the same time not using entry box
    $preview->find('.preview-card:not(.preview-answer) .preview-inner img, .preview-answer .preview-prompt img, .preview-card:not(.preview-answer) .preview-inner .preview-content, .preview-answer .preview-prompt .preview-content')
        ->replaceWith(implode('', ['<img src="', $url, '" />']));

    // TODO: if type-sa?
    if(!empty($content)) {
        $preview->find('[type="text"]')->val($content);
    }
    else {
        $preview->find('[type="text"]')->val('Type your answer');
    }
}
else {
    $preview->find('[type="text"]')->val('Type your answer');
}

$preview->find('.preview-content div')->text($content);

for ($ai = 0; $ai < count($answersUnique); $ai++) {
    $preview->find('.preview-response')->eq($ai)->find('div')->text($answersUnique[$ai]);
}

$preview->find('.preview-answer .preview-inner .preview-content div')->text($correct);

print ($row->html());