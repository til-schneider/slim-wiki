<nav class="breadcrumbs"><div class="main-column"><?php
if ($data['showCreateUserButton']) {
    ?><a class="btn btn-default btn-xs pull-right" href="<?php echo $data['requestPath']; ?>?createUser"><?php echo $i18n['button.createUser']; ?></a><?php
}
if ($mode == 'view' || $mode == 'noSuchArticle') {
    ?><a class="btn btn-default btn-xs pull-right" href="<?php echo $data['requestPath']; ?>?edit"><?php echo $i18n['button.edit']; ?></a><?php
}

$isFirst = true;
foreach ($data['breadcrumbs'] as $item) {
    if (! $isFirst) {
        echo ' / ';
    }
    if ($item['active'] || is_null($item['path'])) {
        echo '<span>' . $item['name'] . '</span>';
    } else {
        ?><a href="<?php echo $data['basePath'] . $item['path'] . (($mode == 'edit') ? '?edit' : ''); ?>"><?php echo $item['name']; ?></a><?php
    }
    $isFirst = false;
}
?></div></nav>
