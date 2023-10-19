<?php
<%include file="shebang.mako"/>

$currDir = dirname(__FILE__);
include "$currDir/application.php";

$bs->content($config['index']);

% for Y in X['schema']:
$bs->tile('${Y}','${X['schema'][Y]['tag']}.php?_csrf=' . $_SESSION['_csrf']);
% endfor

$bs->render();
?>