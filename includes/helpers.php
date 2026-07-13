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
function timeAgo($datetime)
{
    $time = strtotime($datetime);
    $difference = time() - $time;

    if ($difference < 60)
        return "Just now";

    if ($difference < 3600)
        return floor($difference / 60) . " min ago";

    if ($difference < 86400)
        return floor($difference / 3600) . " hrs ago";

    if ($difference < 172800)
        return "Yesterday";

    if ($difference < 604800)
        return floor($difference / 86400) . " days ago";

    if ($difference < 2592000)
        return floor($difference / 604800) . " weeks ago";

    return date("M d, Y", $time);
}