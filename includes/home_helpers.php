<?php
function getFeaturedBooks(mysqli $conn)
{
    return mysqli_query(
        $conn,
        "
        SELECT *
        FROM books
        ORDER BY created_at DESC
        LIMIT 4
        "
    );
}
function getHomepageGenres(mysqli $conn)
{
    return mysqli_query(
        $conn,
        "
        SELECT
            genre,
            COUNT(*) AS total_books
        FROM books
        WHERE genre IS NOT NULL
        AND genre != ''
        GROUP BY genre
        ORDER BY total_books DESC
        "
    );
}
function getTotalBooks(mysqli $conn): int
{
    return (int) mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT COUNT(*) AS total FROM books"
        )
    )["total"];
}
function getTotalGenres(mysqli $conn): int
{
    return (int) mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT COUNT(DISTINCT genre) AS total FROM books"
        )
    )["total"];
}
function getTotalWishlists(mysqli $conn): int
{
    return (int) mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT COUNT(*) AS total FROM wishlist"
        )
    )["total"];
}

function getTotalOrders(mysqli $conn): int
{
    return (int) mysqli_fetch_assoc(
        mysqli_query(
            $conn,
            "SELECT COUNT(*) AS total FROM orders"
        )
    )["total"];
}
function getGenreIcon(string $genre): string
{
    $icons = [

        "fantasy"         => "🐉",
        "mystery"         => "🕵️",
        "science fiction" => "🚀",
        "romance"         => "❤️",
        "history"         => "🏛️",
        "programming"     => "💻",
        "business"        => "💼",
        "horror"          => "👻",
        "thriller"        => "🔪",
        "adventure"       => "🗺️",
        "children"        => "🧸",
        "biography"       => "👤",
        "self-help"       => "🌱",
        "poetry"          => "✒️",
        "comics"          => "🎨"

    ];

    return
        $icons[
            strtolower(trim($genre))
        ] ?? "📚";
}