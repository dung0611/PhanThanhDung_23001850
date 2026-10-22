<?php

// Mảng giá của 5 sản phẩm (đơn vị: đồng).
$prices = [150000, 250000, 120000, 350000, 180000,123456];

// Tính tổng giá trị của tất cả sản phẩm trong mảng.
function calculateTotal(array $prices): float
{
    $total = 0;
    foreach ($prices as $price) {
        $total += $price;
    }
    return $total;
}

// Tìm giá sản phẩm cao nhất trong mảng.
function findMaxPrice(array $prices): float
{
    if (count($prices) === 0) {
        return 0;
    }

    $maxPrice = $prices[0];
    foreach ($prices as $price) {
        if ($price > $maxPrice) {
            $maxPrice = $price;
        }
    }
    return $maxPrice;
}

// Gọi hai hàm và hiển thị kết quả.
$total = calculateTotal($prices);
$maxPrice = findMaxPrice($prices);

echo "<meta charset='UTF-8'>";
echo "<b>Danh sách giá sản phẩm:</b><br>";
foreach ($prices as $index => $price) {
    echo "Sản phẩm " . ($index + 1) . ": " . number_format($price, 0, ',', '.') . " đồng<br>";
}

echo "<br>Tổng giá trị sản phẩm: " . number_format($total, 0, ',', '.') . " đồng<br>";
echo "Giá sản phẩm cao nhất: " . number_format($maxPrice, 0, ',', '.') . " đồng<br>";
