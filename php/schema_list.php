<?php
<%include file="shebang.mako"/>

function ${s}_list($bs,$db,$data) {
    $options = [];
    % for F in FIELDS:
    % if F['type'] == 'lookup':
    $options['${F['tag']}'] = $db->listOfFields($db->scanTable('${F['options'][0]}'),${F['options'][1:]});
    % endif
    % endfor

    // replace the id in the data field for the lookup fields with the values from the foreign table
    $result = [];
    foreach ($data as $record) {
        foreach ($options as $option => $value) {
            foreach ($value as $v) {
                if($v[0] == $record[$option]) {
                    $record[$option] = $v[1] ?? '';
                }
            }
        }
        array_push($result,$record);
    }
    
    $bs->table([
        % for F in FIELDS:
            % if F['on_list']:
            '${F['desc']}' => '${F['tag']}',
            % endif
        % endfor
        ] , 
        $result, 
        [
            % if SCHEMA['can_edit']:
            [
                'text'  => '&#x1F589;',
                'btn'   => 'btn-success',
                'link'  => '?_func=edit'
            ],
            % endif
            % if SCHEMA['can_delete']:
            [
                'text'  => '&#x1F5D1;',
                'btn'   => 'btn-danger',
                'link'  => '?_func=deleteit'
            ],
            % endif
        ]
    );
}
?>