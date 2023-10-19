<?php

function create_schema($db) {
    % for Y in X['schema']:
    // ${Y} = ${X['schema'][Y]['table']}
    $db->create_table('${X['schema'][Y]['table']}');
    % for F in X['schema'][Y]['fields']:
    // -- ${F['desc']}
    $db->create_field('${X['schema'][Y]['table']}','${F['tag']}','${F['type']}');

    % endfor

    % endfor
}

?>