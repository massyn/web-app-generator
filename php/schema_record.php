<?php
<%include file="shebang.mako"/>

function ${s}_record($db,$id) {
    return $db->read('${SCHEMA['table']}',$id);
}
?>