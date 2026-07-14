<?php

function getDashboardStats(mysqli $conn): array
{
    return [

        "books" => mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) total FROM books")
        )["total"],

        "customers" => mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) total FROM customers")
        )["total"],

        "orders" => mysqli_fetch_assoc(
            mysqli_query($conn, "SELECT COUNT(*) total FROM orders")
        )["total"],

        "revenue" => mysqli_fetch_assoc(
            mysqli_query(
                $conn,
                "
                SELECT
                    COALESCE(SUM(total_amount),0) total
                FROM orders
                WHERE status <> 'cancelled'
                "
            )
        )["total"]

    ];
}
function getRecentOrders(mysqli $conn)
{
    return mysqli_query(
        $conn,
        "
        SELECT
            order_number,
            shipping_fullname,
            total_amount,
            status
        FROM orders
        ORDER BY created_at DESC
        LIMIT 5
        "
    );
}
function getLowStockBooks(mysqli $conn)
{
    return mysqli_query(
        $conn,
        "
        SELECT
            title,
            stock
        FROM books
        WHERE stock <= 5
        ORDER BY stock ASC
        LIMIT 5
        "
    );
}
function getRecentCustomers(mysqli $conn)
{
    return mysqli_query(
        $conn,
        "
        SELECT
            fullname,
            created_at
        FROM customers
        ORDER BY created_at DESC
        LIMIT 5
        "
    );
}
function getBestSellingBooks(mysqli $conn)
{
    return mysqli_query(
        $conn,
        "
        SELECT
            title,
            SUM(quantity) AS sold
        FROM order_items
        GROUP BY title
        ORDER BY sold DESC
        LIMIT 5
        "
    );
}