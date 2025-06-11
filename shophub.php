<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

$products = [
    1 => ["name" => "Laptop", "price" => 799.99, "image" => "https://images.pexels.com/photos/205421/pexels-photo-205421.jpeg?auto=compress&cs=tinysrgb&w=300"],
    2 => ["name" => "Smartphone", "price" => 599.99, "image" => "https://images.pexels.com/photos/788946/pexels-photo-788946.jpeg?auto=compress&cs=tinysrgb&w=300"],
    3 => ["name" => "Headphones", "price" => 199.99, "image" => "https://images.pexels.com/photos/3394650/pexels-photo-3394650.jpeg?auto=compress&cs=tinysrgb&w=300"],
    4 => ["name" => "Watch", "price" => 299.99, "image" => "https://images.pexels.com/photos/393047/pexels-photo-393047.jpeg?auto=compress&cs=tinysrgb&w=300"],
    5 => ["name" => "Camera", "price" => 899.99, "image" => "https://images.pexels.com/photos/90946/pexels-photo-90946.jpeg?auto=compress&cs=tinysrgb&w=300"],
    6 => ["name" => "Speaker", "price" => 149.99, "image" => "https://images.pexels.com/photos/1649771/pexels-photo-1649771.jpeg?auto=compress&cs=tinysrgb&w=300"]
];

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$checkoutSuccess = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    if ($action == "add" && isset($products[$id])) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$id] = [
                "name" => $products[$id]['name'],
                "price" => $products[$id]['price'],
                "quantity" => 1
            ];
        }
    } elseif ($action == "update" && isset($_SESSION['cart'][$id])) {
        $change = (int)$_POST['change'];
        $_SESSION['cart'][$id]['quantity'] += $change;
        if ($_SESSION['cart'][$id]['quantity'] <= 0) {
            unset($_SESSION['cart'][$id]);
        }
    } elseif ($action == "remove") {
        unset($_SESSION['cart'][$id]);
    } elseif ($action == "checkout") {
        $_SESSION['cart'] = [];
        $checkoutSuccess = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Shopping Cart</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 20px; }
        .container { max-width: 1000px; margin: auto; background: white; padding: 20px; border-radius: 5px; }
        .header { text-align: center; margin-bottom: 30px; color: #333; }
        .products { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .product { border: 1px solid #ddd; padding: 15px; border-radius: 5px; text-align: center; }
        .product img { width: 100%; height: 150px; object-fit: cover; margin-bottom: 10px; }
        .product h3 { margin-bottom: 5px; color: #333; }
        .product .price { font-size: 18px; font-weight: bold; color: #e74c3c; margin-bottom: 10px; }
        .add-btn { background-color: #3498db; color: white; border: none; padding: 8px 16px; border-radius: 3px; cursor: pointer; }
        .add-btn:hover { background-color: #2980b9; }
        .cart { border-top: 2px solid #ddd; padding-top: 20px; }
        .cart h2 { margin-bottom: 15px; color: #333; }
        .cart-item { display: flex; justify-content: space-between; align-items: center; padding: 10px; border-bottom: 1px solid #eee; }
        .quantity-controls { display: flex; align-items: center; gap: 10px; }
        .qty-btn, .remove-btn { background-color: #95a5a6; color: white; border: none; padding: 5px 10px; border-radius: 3px; cursor: pointer; }
        .qty-btn:hover, .remove-btn:hover { background-color: #7f8c8d; }
        .remove-btn { background-color: #e74c3c; }
        .remove-btn:hover { background-color: #c0392b; }
        .total { text-align: right; margin-top: 20px; font-size: 20px; font-weight: bold; color: #2c3e50; }
        .checkout-btn { background-color: #27ae60; color: white; border: none; padding: 12px 24px; border-radius: 5px; cursor: pointer; font-size: 16px; margin-top: 15px; float: right; }
        .checkout-btn:hover { background-color: #229954; }
        .empty-cart { text-align: center; color: #7f8c8d; font-style: italic; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Simple Shopping Cart</h1>
        </div>

        <?php if ($checkoutSuccess): ?>
            <p style="color: green; text-align: center; font-weight: bold;">Checkout complete. Thank you for your purchase!</p>
        <?php endif; ?>

        <div class="products">
            <?php foreach ($products as $id => $product): ?>
                <div class="product">
                    <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>">
                    <h3><?= $product['name'] ?></h3>
                    <div class="price">$<?= number_format($product['price'], 2) ?></div>
                    <form method="post">
                        <input type="hidden" name="id" value="<?= $id ?>">
                        <input type="hidden" name="action" value="add">
                        <button class="add-btn" type="submit">Add to Cart</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart">
            <h2>Shopping Cart</h2>
            <?php if (empty($_SESSION['cart'])): ?>
                <div class="empty-cart">Your cart is empty</div>
            <?php else: ?>
                <?php $total = 0; ?>
                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                    <?php $itemTotal = $item['price'] * $item['quantity']; $total += $itemTotal; ?>
                    <div class="cart-item">
                        <div>
                            <strong><?= $item['name'] ?></strong><br>
                            $<?= number_format($item['price'], 2) ?> each
                        </div>
                        <div class="quantity-controls">
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="change" value="-1">
                                <button class="qty-btn" type="submit">-</button>
                            </form>
                            <span><?= $item['quantity'] ?></span>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <input type="hidden" name="action" value="update">
                                <input type="hidden" name="change" value="1">
                                <button class="qty-btn" type="submit">+</button>
                            </form>
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $id ?>">
                                <input type="hidden" name="action" value="remove">
                                <button class="remove-btn" type="submit">Remove</button>
                            </form>
                        </div>
                        <div><strong>$<?= number_format($itemTotal, 2) ?></strong></div>
                    </div>
                <?php endforeach; ?>
                <div class="total">Total: $<?= number_format($total, 2) ?></div>
                <form method="post">
                    <input type="hidden" name="action" value="checkout">
                    <button class="checkout-btn" type="submit">Checkout</button>
                </form>
            <?php endif; ?>
            <div style="clear: both;"></div>
        </div>
    </div>
</body>
</html>

