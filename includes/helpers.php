<?php

function url($path)
{
    return BASE_URL . ltrim($path, '/');
}

function redirect($path)
{
    header("Location: " . url($path));
    exit();
}

function asset($path)
{
    return BASE_URL . "assets/" . ltrim($path, '/');
}