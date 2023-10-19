<?php
<%include file="shebang.mako"/>

// crud.php is the main control template for crud-like applications.  

$currDir = dirname(__FILE__);
include "$currDir/application.php";
include "$currDir/${s}_form.php";
% if SCHEMA['can_add']:
include "$currDir/${s}_insert.php";
% endif
include "$currDir/${s}_list.php";
include "$currDir/${s}_select.php";
% if SCHEMA['can_delete']:
include "$currDir/${s}_delete.php";
% endif
% if SCHEMA['can_edit']:
include "$currDir/${s}_record.php";
include "$currDir/${s}_update.php";
% endif

$bs->h1("${S}");
if($_SESSION['csrf_valid']) {
    % if SCHEMA['can_add']:
    if(param('_func') == 'add_it') {
        if(${s}_insert($db)) {
            $bs->alert('success','Record inserted');
        } else {
            $bs->alert('danger','Something went wrong.');
        } 
    }
    if(param('_func') == 'add') {
        $bs->h2("Add");
        ${s}_form($bs,$db,'Add','add_it',[]);
    }
    % endif
    % if SCHEMA['can_delete']:
    if(param("_func") == 'deleteit') {
        if(${s}_delete($db,param('id'))) {
            $bs->alert('success','Record deleted');
        } else {
            $bs->alert('danger','Something went wrong.');
        }
    }
    % endif
    % if SCHEMA['can_edit']:
    if(param("_func") == 'edit') {
        $bs->h2("Edit");
        $data = ${s}_record($db,param('id'));
        ${s}_form($bs,$db,'Edit','edit_it',$data);
    }

    if(param("_func") == 'edit_it') {
        if(${s}_update($db,param('id'))) {
            $bs->alert('success','Record updated');
        } else {
            $bs->alert('danger','Something went wrong.');
        }
    }
    % endif
} else {
    $bs->alert('danger','CSRF token expired');
}

// main screen
if(param('_func') == '' || param('_func') == 'add_it' || param('_func') == 'edit_it'  || param("_func") == 'deleteit' || !$_SESSION['csrf_valid']) {
    % if SCHEMA['can_add']:
    $bs->button('Add','?_func=add&_csrf=' . $_SESSION['_csrf']);
    % endif
    $data = ${s}_select($db);
    ${s}_list($bs,$db,$data);
}

$bs->render();
?>