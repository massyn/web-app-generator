<?php
<%include file="shebang.mako"/>
% if SCHEMA['can_delete']:
function ${s}_delete($db,$id) {
    return $db->delete('${SCHEMA['table']}',$id);
}
% endif

?>