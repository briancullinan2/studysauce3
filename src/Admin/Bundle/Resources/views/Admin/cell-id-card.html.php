<?php
use StudySauce\Bundle\Entity\Card;

/** @var Card $card */

$isContains = !empty($card->getCorrect()) && strlen($card->getCorrect()->getValue()) > strlen(trim($card->getCorrect()->getValue(), '$^'));
$content = $card->getContent();
$content = preg_replace('/\\\\n(\\\\r)?/i', "\n", $content);
if (($hasUrl = preg_match('/https:\\/\\/.*/i', $content, $matches)) > 0) {
    $url = trim($matches[0]);
    $isImage = substr($url, -4) == '.jpg' || substr($url, -4) == '.jpeg' || substr($url, -4) == '.gif' || substr($url, -4) == '.png';
    $isAudio = substr($url, -4) == '.mp3' || substr($url, -4) == '.m4a';

}
?>

<label class="input type">
    <span><?php print ($card->getIndex() + 1); ?></span>
    <select name="responseType">
        <option value="" <?php print (empty($card->getResponseType()) ? 'selected="selected"' : ''); ?>>Flash card</option>
        <option value="mc" <?php print ($card->getResponseType() == 'mc' ? 'selected="selected"' : ''); ?>>Multiple choice</option>
        <option value="tf" <?php print ($card->getResponseType() == 'tf' ? 'selected="selected"' : ''); ?>>True/False</option>
        <option value="sa contains" <?php print ($card->getResponseType() == 'sa' && $isContains ? 'selected="selected"' : ''); ?>>Short answer (contains)</option>
        <option value="sa exactly" <?php print ($card->getResponseType() == 'sa' && !$isContains ? 'selected="selected"' : ''); ?>>Short answer (exact match)</option>
    </select>
</label>
<input name="upload" value="<?php print (!empty($url) ? $url : ''); ?>" type="hidden" />
<a href="#upload-image" class="<?php print (!empty($isImage) ? 'active' : ''); ?>" data-target="#upload-file" data-toggle="modal"> </a>
<a href="#upload-audio" class="<?php print (!empty($isAudio) ? 'active' : ''); ?>" data-target="#upload-file" data-toggle="modal"> </a>
<a href="#upload-video" data-target="#upload-file" data-toggle="modal"> </a>