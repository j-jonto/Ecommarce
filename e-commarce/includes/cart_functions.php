<?php
// Shopping cart functions

/**
 * Initialize shopping cart
 */
function initCart() {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
}

/**
 * Add product to cart
 * @param int $productId Product ID
 * @param int $quantity Quantity
 * @param array $productDetails Product details
 * @return bool Success status
 */
function addToCart($productId, $quantity, $productDetails) {
    initCart();
    
    // Check if product already exists in cart
    if (isset($_SESSION['cart'][$productId])) {
        // Update quantity
        $_SESSION['cart'][$productId]['quantity'] += $quantity;
    } else {
        // Add new product to cart
        $_SESSION['cart'][$productId] = [
            'id' => $productId,
            'name' => $productDetails['name'],
            'price' => $productDetails['price'],
            'quantity' => $quantity,
            'image' => $productDetails['image_path']
        ];
    }
    
    return true;
}

/**
 * Update cart item quantity
 * @param int $productId Product ID
 * @param int $quantity New quantity
 * @return bool Success status
 */
function updateCartItem($productId, $quantity) {
    if (isset($_SESSION['cart'][$productId])) {
        if ($quantity <= 0) {
            // Remove item if quantity is zero or negative
            removeCartItem($productId);
        } else {
            // Update quantity
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
        return true;
    }
    return false;
}

/**
 * Remove item from cart
 * @param int $productId Product ID
 * @return bool Success status
 */
function removeCartItem($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
        return true;
    }
    return false;
}

/**
 * Get cart contents
 * @return array Cart items
 */
function getCartItems() {
    initCart();
    return $_SESSION['cart'];
}

/**
 * Get cart total
 * @return float Cart total
 */
function getCartTotal() {
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

/**
 * Get cart item count
 * @return int Number of items in cart
 */
function getCartItemCount() {
    $count = 0;
    if (isset($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += $item['quantity'];
        }
    }
    return $count;
}

/**
 * Clear the shopping cart
 */
function clearCart() {
    $_SESSION['cart'] = [];
}
?>