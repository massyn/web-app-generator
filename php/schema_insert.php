<?php
<%include file="shebang.mako"/>
% if SCHEMA['can_add']:
function ${s}_insert($db) {
    $data = [
    % for F in FIELDS:
        '${F['tag']}' => param('${F['tag']}'),
    % endfor
    ];

    return $db->insert('${SCHEMA['table']}',$data);
}
% endif

?>