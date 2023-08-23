<?php
$f = new FORM();
$f->text('name','Name your Web App','Ex: Food Delivery, Events, CRM, '.ucfirst( APPDIR ).' App, '.ucfirst( APPDIR ).' etc.','','',12);
$f->slide('force_ssl','Do you want to force SSL ?','Off','On',1,'','',4);
$f->slide('debug','Do you prefer debug mode ?','Off','On',1,'','',4);
$f->slide('git_ignore','Create a default .gitignore ?','Off','On',1,'','',4);
$f->text('name','Set a key for basic encryption','Ex: AwesomeApp etc.','','',12);