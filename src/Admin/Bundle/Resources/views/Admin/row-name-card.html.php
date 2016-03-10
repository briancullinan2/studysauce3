<?php
use StudySauce\Bundle\Entity\Card;

/** @var Card $card */

$content = $card->getContent();
$content = preg_replace('/\\\\n(\\\\r)?/i', "\n", $content);
if (($hasUrl = preg_match('/https:\/\/.*/i', $content, $matches)) > 0) {
    $url = trim($matches[0]);
    $isImage = substr($url, -4) == '.jpg' || substr($url, -4) == '.jpeg' || substr($url, -4) == '.gif' || substr($url, -4) == '.png';
    $isAudio = substr($url, -4) == '.mp3' || substr($url, -4) == '.m4a';
    $content = preg_replace('/\s*\n\r?/i', '\n', trim(str_replace($url, '', $content)));
}
?>

<label class="input content">
    <input type="text" name="content" placeholder="Prompt" value="<?php print $view->escape($content); ?>"/>
</label>
