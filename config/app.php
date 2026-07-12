<?php

define("ROOT_PATH", dirname(__DIR__));

if ($_SERVER["HTTP_HOST"] === "localhost") {

    $scriptDir = dirname($_SERVER["SCRIPT_NAME"]);
    $scriptDir = str_replace("\\", "/", $scriptDir);

    $parts = explode("/", trim($scriptDir, "/"));

    $project = $parts[0] ?? "";

    define("BASE_URL", $project ? "/{$project}/" : "/");

} else {

    define("BASE_URL", "/");

}