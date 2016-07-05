<?php
use StudySauce\Bundle\Entity\Card;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var Card $card */

// check if we need to update or create template
$row = !empty($context) ? $context : jQuery($this);

// TODO: how to get data from object or from view in the same way?
// TODO: use applyFields and gatherFields here too?  at the row level?
$type = $card->getResponseType();
$content = $card->getContent();
$content = preg_replace('/\\\\n(\\\\r)?/i', "\n", $content);
$correct = !empty($card->getCorrect()) ? preg_replace('/\\\\n(\\\\r)?/i', "\n", $card->getCorrect()->getContent()) : '';
$matches = (array)(new stdClass());
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
    ? $row->find('.preview-card:not([class*="type-"])')
    : $row->find(implode('', ['.preview-card.type-' , $type]));
// switch templates if needed
if (2 != $template->length) {
    $row->children()->remove();

    // this is all prompt content
    $view['slots']->start('card-preview-prompt'); ?>
    <?php if (!empty($isImage)) { ?><img src="<?php print ($url); ?>" class="centerized" /><?php } ?>
    <?php if (!empty($isAudio)) { ?><div class="preview-progress centerized"></div><div class="preview-play"><a href="<?php print ($url); ?>" class="play centerized"></a><a href="#pause" class="pause centerized"></a></div><?php } ?>
    <?php if (empty($isImage) && empty($isAudio)) { ?>
        <div class="preview-content"><div class="centerized"><?php print ($view->escape($content)); ?></div></div>
    <?php } ?>
    <?php $view['slots']->stop();

    // re-render preview completely because type has changed
    $view['slots']->start('card-preview'); ?>
    <?php if (empty($type)) { ?>
        <div class="preview-card preview-answer">
            <div class="preview-prompt">
                <?php $view['slots']->output('card-preview-prompt'); ?>
            </div>
            <div class="preview-inner">
                <div class="preview-correct">Correct answer:</div>
                <div class="preview-content"><div class="centerized"></div></div>
            </div>
            <div class="preview-footer">
                <a href="#wrong" class="preview-wrong">✘</a>
                <div class="preview-guess">Did you guess correctly?</div>
                <a href="#right" class="preview-right">&#x2714;︎</a>
                <?php print ($view->render('AdminBundle:Admin:cell-cardFooter-card.html.php', ['results' => $results, 'request' => $request])); ?>
            </div>
        </div>
    <?php }
    else { ?>
        <div class="preview-card type-mc type-tf type-sa preview-answer">
            <div class="preview-prompt">
                <?php $view['slots']->output('card-preview-prompt'); ?>
            </div>
            <div class="preview-inner">
                <div class="preview-correct">Correct answer:</div>
                <div class="preview-content"><div class="centerized"></div></div>
            </div>
            <div class="preview-footer">
                <div class="preview-guess">Click to continue</div>
                <?php print ($view->render('AdminBundle:Admin:cell-cardFooter-card.html.php', ['results' => $results, 'request' => $request])); ?>
            </div>
        </div>
    <?php }
    $view['slots']->stop();

    $row->append($view['slots']->get('card-preview'));
}

//$packTitle = !empty($card->getPack()) ? $card->getPack()->getTitle() : '';
//$cardCount = !empty($card->getPack()) ? ($card->getIndex() + 1 . ' of ' . count($card->getPack()->getCards()->toArray())) : '1 or 10';

// replace with image
if($isImage && isset($url)) {
    // TODO: change this if we need to support image and text at the same time not using entry box
    $row->find('.preview-card:not(.preview-answer) .preview-inner img, .preview-answer .preview-prompt img, .preview-card:not(.preview-answer) .preview-inner .preview-content, .preview-answer .preview-prompt .preview-content')
        ->replaceWith(implode('', ['<img src="', $url, '" />']));
}

$row->find('.preview-content div')->text($content);

$row->find('.preview-answer .preview-inner .preview-content div')->text($correct);

print ($row->html());