<?php

define("ROOT_PATH", dirname(__DIR__));

if ($_SERVER["HTTP_HOST"] === "localhost") {

    define("BASE_URL", "/tx23/LibrarySystem/");

} else {

    define("BASE_URL", "/");

}