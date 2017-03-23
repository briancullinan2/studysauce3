<html>
<head>

</head>
<body style="overflow: hidden; margin: 0;">
<div id="preview">
    <?php foreach ($view['assetic']->stylesheets(
        [
            '@AdminBundle/Resources/public/css/ionicons.css',
            '@AdminBundle/Resources/public/css/codemirror.css',
            '@AdminBundle/Resources/public/css/emails.css',
            '@AdminBundle/Resources/public/js/addon/fold/foldgutter.css',
            '@AdminBundle/Resources/public/js/addon/display/fullscreen.css',
            '@AdminBundle/Resources/public/js/addon/hint/show-hint.css',
        ],
        [],
        ['output' => 'bundles/admin/css/*.css']) as $url): ?>
        <link type="text/css" rel="stylesheet" href="<?php echo $view->escape($url) ?>"/>
    <?php endforeach; ?>
    <div id="editor1" contenteditable="true"><?php print $template; ?></div>
    <textarea id="markdown" class="full-height" placeholder="Write Markdown"><?php print $view->escape($template); ?></textarea>
    <pre class="headers"><?php print $headers; ?></pre>
    <script type="text/javascript">
        CKEDITOR_BASEPATH = '<?php print $view['router']->generate('_welcome'); ?>bundles/admin/js/ckeditor/';
    </script>
    <?php foreach ($view['assetic']->javascripts(
        [
            '@StudySauceBundle/Resources/public/js/jquery-2.1.4.js',
            '@AdminBundle/Resources/public/js/ckeditor/ckeditor.js',
            '@AdminBundle/Resources/public/js/codemirror.js',
            '@AdminBundle/Resources/public/js/addon/fold/foldcode.js',
            '@AdminBundle/Resources/public/js/addon/fold/foldgutter.js',
            '@AdminBundle/Resources/public/js/addon/fold/brace-fold.js',
            '@AdminBundle/Resources/public/js/addon/fold/xml-fold.js',
            '@AdminBundle/Resources/public/js/addon/fold/markdown-fold.js',
            '@AdminBundle/Resources/public/js/addon/fold/comment-fold.js',
            '@AdminBundle/Resources/public/js/addon/display/fullscreen.js',
            '@AdminBundle/Resources/public/js/addon/hint/show-hint.js',
            '@AdminBundle/Resources/public/js/addon/hint/css-hint.js',
            '@AdminBundle/Resources/public/js/addon/hint/xml-hint.js',
            '@AdminBundle/Resources/public/js/addon/hint/html-hint.js',
            '@AdminBundle/Resources/public/js/mode/xml/xml.js',
            '@AdminBundle/Resources/public/js/mode/javascript/javascript.js',
            '@AdminBundle/Resources/public/js/mode/css/css.js',
            '@AdminBundle/Resources/public/js/mode/htmlmixed/htmlmixed.js',
            '@AdminBundle/Resources/public/js/init.js'
        ],
        [],
        ['output' => 'bundles/admin/js/*.js']) as $url): ?>
        <script type="text/javascript" src="<?php echo $view->escape($url) ?>"></script>
    <?php endforeach; ?>
</div>
<div class="subject"><?php print $subject; ?></div>
<table class="variables results">
    <thead>
    <tr>
        <?php
        $labeled = [];
        $first = true;
        foreach($params as $k => $p) {
            if(!in_array($k, Admin\Bundle\Controller\EmailsController::$templateVars))
                continue; ?>
            <th><label><?php print (!in_array($p['name'], $labeled) ? $p['name'] : ''); ?></label>
            <?php if(!$first) { ?><a href="#remove-field"></a><?php } ?></th>
            <?php $first = false; $labeled[] = $p['name']; } ?>
        <th><a href="#add-field">+</a></th>
    </tr>
    </thead>
<tbody>
<tr>
    <?php
    foreach($params as $k => $p) {
        if(!in_array($k, Admin\Bundle\Controller\EmailsController::$templateVars))
            continue; ?>
        <td><label class="input"><span></span><input placeholder="<?php print $p['prop']; ?>" name="<?php print $k; ?>" type="text" /></label></td>
    <?php } ?>
    <td><a href="#remove-line"></a></td>
</tr>
</tbody>
</table>

</body>
</html>
