<?php
<%include file="shebang.mako"/>

function ${s}_form($bs,$db,$txt,$func,$values = []) {
    $options = [];
    % for F in FIELDS:
    % if F['type'] == 'dropdown':
    // dropdown options for ${F['tag']}
    $options['${F['tag']}'] = ${F['options']};
    % endif
    % if F['type'] == 'lookup':
    $options['${F['tag']}'] = $db->listOfFields($db->scanTable('${F['options'][0]}'),${F['options'][1:]});
    % endif
    % endfor

    $bs->form(
        [
            'action' => '${s}.php',
            'func'   => $func,
            'submit' => $txt,
            'id'     => $values['id'] ?? ''
        ],
% for F in FIELDS:
        $bs->form_field('${F['tag']}','${F['type'] if F['type'] != 'lookup' else 'dropdown'}','${F['desc']}',${F['required']},$values['${F['tag']}'] ?? '',$options['${F['tag']}'] ?? [] ,'${F['helptext']}') . 
% endfor
        ''
    );
}

?>
