<label class="input type">
    <span><?php print $card->getIndex() + 1; ?></span>
    <select name="type">
        <option value="" <?php print (empty($card->getResponseType()) ? 'selected="selected"' : ''); ?> data-text="Flash card (default)">Type</option>
        <option value="mc" <?php print ($card->getResponseType() == 'mc' ? 'selected="selected"' : ''); ?> data-text="Multiple choice">MC</option>
        <option value="tf" <?php print ($card->getResponseType() == 'tf' ? 'selected="selected"' : ''); ?> data-text="True/False">TF</option>
        <option value="sa" <?php print ($card->getResponseType() == 'sa' ? 'selected="selected"' : ''); ?> data-text="Short answer">SA</option>
    </select>
</label>