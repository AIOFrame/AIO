<?php
$includes = ['arrays','encrypt','form','code','translation/strings','data','setup'];
foreach( $includes as $inc ) {
    include_once(ROOTPATH . 'core/includes/' . $inc . '.php');
}
$c = new CODE();
$c->pre_html( '', '', 'bootstrap/css/bootstrap-grid,select2,aio', '', '', 'inputs,tabs,icons', 'builder,micro', 'jquery,select2', ['Lato','300,500'], ['Cairo','500'], 'MaterialIcons' );