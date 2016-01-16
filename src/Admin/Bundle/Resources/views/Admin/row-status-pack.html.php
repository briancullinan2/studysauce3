<div>
    <label class="input status">
        <select name="status">
            <option value="">Not Set</option>
            <option value="UNPUBLISHED" <?php print ($pack->getStatus() == 'UNPUBLISHED' ? 'selected="selected"' : ''); ?>>Unpublished</option>
            <option value="PUBLIC" <?php print ($pack->getStatus() == 'PUBLIC' ? 'selected="selected"' : ''); ?>>Public</option>
            <option value="GROUP" <?php print ($pack->getStatus() == 'GROUP' ? 'selected="selected"' : ''); ?>>Group-only</option>
            <option value="UNLISTED" <?php print ($pack->getStatus() == 'UNLISTED' ? 'selected="selected"' : ''); ?>>Unlisted</option>
            <option value="DELETED" <?php print ($pack->getStatus() == 'DELETED' ? 'selected="selected"' : ''); ?>>Deleted</option>
        </select>
    </label>
</div>