<?php
<%include file="shebang.mako"/>

function ${s}_update($db,$id) {
    $data = [
        % for F in FIELDS:
            '${F['tag']}' => param('${F['tag']}'),
        % endfor
            'id' => $id
    ];

    return $db->modify('${SCHEMA['table']}',$data);
}
?>