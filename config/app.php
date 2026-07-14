<?php

define("ROOT_PATH", dirname(__DIR__));

if ($_SERVER["HTTP_HOST"] === "localhost") {

    // Detect the project folder automatically
    $scriptName = str_replace("\\", "/", dirname($_SERVER["SCRIPT_NAME"]));

    // If we're inside /auth, /admin, or /customer, go back one level
    if (
        basename($scriptName) === "auth" ||
        basename($scriptName) === "admin" ||
        basename($scriptName) === "customer" ||
        basename($scriptName) === "orders" ||
         basename($scriptName) === "footer_links"
    ) {
        $scriptName = dirname($scriptName);
    }

    define("BASE_URL", rtrim($scriptName, "/") . "/");

} else {

    define("BASE_URL", "/");

}