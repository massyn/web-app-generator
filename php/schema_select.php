<?php
<%include file="shebang.mako"/>

// TODO - Improve scanTable to do some filtering, else this is going to be a very expensive call
function ${s}_select($db) {
    return $db->scanTable('${SCHEMA['table']}');
}

?>