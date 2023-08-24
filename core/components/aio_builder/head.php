<?php
$includes = ['arrays','encrypt','form','code','translation/strings','data','builder'];
foreach( $includes as $inc ) {
    include_once(ROOTPATH . 'core/includes/' . $inc . '.php');
}
$c = new CODE();
$c->pre_html( '', '', 'bootstrap/css/bootstrap-grid,select2,iro,aio', '', '', 'inputs,tabs,icons,color', 'builder,micro', 'jquery', ['Lato','300,500'], ['Cairo','500'], 'MaterialIcons' );