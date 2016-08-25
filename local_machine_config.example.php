<?php
/*

local_machine_config.php / local_machine_config.example.php
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

This file contains information specific for this machine. 
For example - the email address to send error messages to, 
or whether this is a development environment or production environment.

PLEASE NOTE: For settings which should be the same for all environments (such as default language or other default settings),
please use the file application/config/settings.php

In order for the system to work, you must copy this file  from local_machine_config.example.php 
to local_machine_config.php - it will be automatically excluded from the version control thanks to git settings.

If you add more constatnts to this file, please update local_machine_config.example.php too and commit 
local_machine_config.example.php to the version control. DO NOT submit local_machine_config.php to the version control!  

All constants here begin with LMC.
*/

// A flag so index.php will know this file was found and loaded!
define ('LMC_WAS_LOADED', true);

// See index.php - can be "production" / "testing" / "development" / "local"
define ('LMC_ENVIRONMENT', 'development');

// Settings for the DB
define ('LMC_DB_HOSTNAME', 'localhost');
define ('LMC_DB_USERNAME', 'db_user');
define ('LMC_DB_PASSWORD', 'db_pass');
define ('LMC_DB_NAME', 'db_name');