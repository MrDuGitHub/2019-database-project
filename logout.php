<?php

namespace orgName\xxSystem;

require_once($_SERVER['DOCUMENT_ROOT'] . '/include/autoload.php');

Session::destroy_session();
Session::require_non_ie();
Template::die_302('/login.php');
