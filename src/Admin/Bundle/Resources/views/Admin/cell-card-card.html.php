<?php
use StudySauce\Bundle\Entity\Answer;
use StudySauce\Bundle\Entity\Card;
use StudySauce\Bundle\Entity\UserPack;
use DateTime as Date;
use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;

/** @var GlobalVariables $app */
$request = $app->getRequest();

/** @var Card $card */

// check if we need to update or create template
$row = !empty($context) ? $context : jQuery($this);

$total = isset($results['user_pack'][0]) ? 0 : $card->getPack()->getCards()->count();
$index = 1;
$retention = isset($results['user_pack'][0]) ? [$results['user_pack'][0]] : [];
if(isset($results['user_pack'][0]) && $request->cookies->get('retention_shuffle')) {
    // TODO: count all cards
    $retention = $results['user_pack'][0]->getUser()->getUserPacks()->toArray();
}
foreach($retention as $up) {
    /** @var UserPack $up */
    if($up->getRemoved() || $up->getPack()->getStatus() == 'DELETED' || $up->getPack()->getStatus() == 'UNPUBLISHED') {
        continue;
    }
    foreach($up->getRetention() as $id => $r) {
        if($r[2]) {
            $total += 1;
        }
        if(!empty($r[3]) && new Date($r[3]) > new Date($request->cookies->get('retention'))) {
            $index += 1;
        }
    }
}

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
        <div class="preview-card">
            <div class="preview-inner">
                <?php $view['slots']->output('card-preview-prompt'); ?>
            </div>
            <div class="preview-tap">Tap to see answer</div>
            <div class="preview-footer">
                <div class="preview-count"><?php print ($index); ?> of <?php print ($total); ?></div>
            </div>
        </div>
    <?php } ?>
    <?php if ($type == 'mc') { ?>
        <div class="preview-card type-mc">
            <div class="preview-inner">
                <?php $view['slots']->output('card-preview-prompt'); ?>
            </div>
            <div class="preview-footer">
                <a href="" class="preview-response"><div class="centerized"></div></a>
                <a href="" class="preview-response"><div class="centerized"></div></a>
                <a href="" class="preview-response"><div class="centerized"></div></a>
                <a href="" class="preview-response"><div class="centerized"></div></a>
                <div class="preview-count"><?php print ($index); ?> of <?php print ($total); ?></div>
            </div>
        </div>
    <?php } ?>
    <?php if ($type == 'tf') { ?>
        <div class="preview-card type-tf">
            <div class="preview-inner">
                <?php $view['slots']->output('card-preview-prompt'); ?>
            </div>
            <div class="preview-footer">
                <a href="#false" class="preview-false">False</a>
                <div class="preview-guess"> </div>
                <a href="#true" class="preview-true">True</a>
                <div class="preview-count"><?php print ($index); ?> of <?php print ($total); ?></div>
            </div>
        </div>
    <?php } ?>
    <?php if ($type == 'sa') { ?>
        <div class="preview-card type-sa">
            <div class="preview-inner">
                <?php $view['slots']->output('card-preview-prompt'); ?>
            </div>
            <label class="input"><input type="text" value="" data-disclaimer="if you are reading this you should be a hacker ;)" data-correct="<?php print ($card->getCorrect()->getValue()); ?>" /></label>
            <a href="#done" class="btn">Done</a>
            <div class="preview-footer">
                <div class="preview-count"><?php print ($index); ?> of <?php print ($total); ?></div>
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

if(($isImage || $isAudio) && isset($url)) {
    // TODO: if type-sa?
    if(!empty($content)) {
        $row->find('[type="text"]')->attr('placeholder', $content);
    }
    else {
        $row->find('[type="text"]')->attr('placeholder', 'Type your answer');
    }
}
else {
    $row->find('[type="text"]')->attr('placeholder', 'Type your answer');
}

$row->find('.preview-content div')->text($content);

$answersUnique = [];
foreach($card->getAnswers()->toArray() as $answer) {
    /** @var Answer $answer */
    if (!$answer->getDeleted() && !in_array($answer->getContent(), $answersUnique)) {
        $resp = $row->find('.preview-response')->eq(count($answersUnique));
        $resp->attr('href', implode('', ['#', $answer->getValue()]));
        $resp->find('div')->text($answer->getContent());
        $resp->addClass(implode('', ['answer-id-', $answer->getId()]));
        if($answer->getCorrect()) {
            $resp->addClass('correct');
        }
        $answersUnique[count($answersUnique)] = $answer->getContent();
    }
}

if($card->getResponseType() == 'tf') {
    if(strpos(strtolower($card->getCorrect()->getValue()), 'true') !== false) {
        $resp = $row->find('.preview-true')->addClass('correct');
        $resp->addClass(implode('', ['answer-id-', $card->getCorrect()->getId()]));
    }
    if(strpos(strtolower($card->getCorrect()->getValue()), 'false') !== false) {
        $resp = $row->find('.preview-false')->addClass('correct');
        $resp->addClass(implode('', ['answer-id-', $card->getCorrect()->getId()]));
    }
}

print ($row->html());