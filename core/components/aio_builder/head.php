<?php
$includes = ['arrays','encrypt','form','code','translation/strings','data','builder'];
foreach( $includes as $inc ) {
    include_once(ROOTPATH . 'core/includes/' . $inc . '.php');
}
pre_html( '', '', 'bootstrap/css/bootstrap-grid,select2,iro,aio', '', '', 'inputs,tabs,icons,buttons,color,tips', 'builder,micro', 'jquery', ['Lato','300,500'], ['Cairo','500'], 'MaterialIcons' );
pre( '', '', 'header' );
    div( 'logo' );
    div( 'options_toggle' );
    _d( 'options' );
        div( 'ico', __div( 'mico', 'language' ) . __el( 'i', 'tip', 'Change Language' ), '', 'data-on=".languages"' );
        div( 'ico dark', __div( 'mico', 'dark_mode' ) . __el( 'i', 'tip', 'Toggle Dark Mode' ), '', 'data-dark' );
    d_();
post( 'header' );