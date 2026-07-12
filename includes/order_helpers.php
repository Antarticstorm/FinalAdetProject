<?php
// Shared helpers for the Sales and Order Management module.
// Include this AFTER includes/db.php (needs $conn) and after session_start().

// Returns the price the customer actually pays for a book, after
// any per-book promotional discount is applied.
function getEffectivePrice($price, $discountPercent)
{
    $price = (float)$price;
    $discountPercent = (float)$discountPercent;

    if ($discountPercent <= 0) {
        return $price;
    }

    return $price - ($price * ($discountPercent / 100));
}

// Reads $_SESSION['cart'] (book_id => quantity), pulls fresh book data
// from the database, and returns the line items plus a subtotal.
// Books that were deleted or are now out of stock are dropped from the
// returned items but left in the session so the cart page can flag them.
function getCartDetails($conn)
{
    $items = [];
    $subtotal = 0;

    if (empty($_SESSION['cart'])) {
        return ["items" => $items, "subtotal" => 0];
    }

    foreach ($_SESSION['cart'] as $bookId => $qty) {

        $stmt = $conn->prepare("SELECT id, title, author, cover, price, discount_percent, stock FROM books WHERE id = ?");
        $stmt->bind_param("i", $bookId);
        $stmt->execute();
        $book = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$book) {
            continue; // book no longer exists
        }

        $qty = max(1, (int) $qty);
        $unitPrice = getEffectivePrice($book['price'], $book['discount_percent']);
        $lineTotal = round($unitPrice * $qty, 2);

        $items[] = [
            "book_id"    => $book['id'],
            "title"      => $book['title'],
            "author"     => $book['author'],
            "cover"      => $book['cover'],
            "unit_price" => $unitPrice,
            "quantity"   => $qty,
            "stock"      => (int) $book['stock'],
            "line_total" => $lineTotal,
        ];

        $subtotal += $lineTotal;
    }

    return ["items" => $items, "subtotal" => round($subtotal, 2)];
}

// Looks up an active, unexpired promo code and returns its row, or null.
function getActivePromo($conn, $code)
{
    if (empty($code)) {
        return null;
    }

    $stmt = $conn->prepare("
        SELECT * FROM promo_codes
        WHERE code = ?
          AND is_active = 1
          AND (expires_at IS NULL OR expires_at >= CURDATE())
    ");
    $stmt->bind_param("s", $code);
    $stmt->execute();
    $promo = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    return $promo ?: null;
}

// Given a subtotal and a promo row, returns the peso discount amount.
function calculatePromoDiscount($subtotal, $promo)
{
    if (!$promo) {
        return 0;
    }

    if (!empty($promo['discount_percent'])) {
        $discount = $subtotal * ((float) $promo['discount_percent'] / 100);
    } else {
        $discount = (float) $promo['discount_amount'];
    }

    // Discount can never exceed the subtotal.
    return round(min($discount, $subtotal), 2);
}

// Generates a short, unique, human-friendly order number like ORD-7F3K9Q2A.
function generateOrderNumber($conn)
{
    do {
        $candidate = "ORD-" . strtoupper(substr(bin2hex(random_bytes(5)), 0, 8));
        $stmt = $conn->prepare("SELECT id FROM orders WHERE order_number = ?");
        $stmt->bind_param("s", $candidate);
        $stmt->execute();
        $exists = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } while ($exists);

    return $candidate;
}

function statusBadgeClass($status)
{
    switch ($status) {
        case 'delivered':
            return 'status-delivered';
        case 'shipped':
            return 'status-shipped';
        case 'confirmed':
            return 'status-confirmed';
        case 'cancelled':
            return 'status-cancelled';
        default:
            return 'status-pending';
    }
}
