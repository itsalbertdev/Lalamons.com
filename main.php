<?php
include('db_connection.php');
session_start();

error_log("Session user_id in main.php: " . $_SESSION['user_id']); // Log the user ID for debugging

// Fetch products
$query = "SELECT * FROM products WHERE status = 'available'";
$result = $conn->query($query);

$products = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

    // Handle add to cart
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
        $product_id = $_POST['product_id'];
        $quantity = intval($_POST['quantity']) > 0 ? intval($_POST['quantity']) : 1;

        $query = "SELECT * FROM products WHERE product_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param('i', $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        if ($product) {
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }

            // Update quantity if product already exists in cart
            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $product_id,
                    'name' => $product['product_name'],
                    'price' => $product['price'],
                    'quantity' => $quantity,
                    'image_url' => $product['image_url']
                ];
            }

            header("Location: cart.php");
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lalamons</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f8f8;
        }
        header {
            background-color: #b22222;
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        header h1 {
            margin: 0;
            font-size: 24px;
            padding: 5px 0;
        }

        header nav a {
            color: white;
            text-decoration: none;
            margin-left: 15px;
        }

        header nav a:hover {
            text-decoration: underline;
        }

        #filter-bar {
            background-color: #f4cccc;
            padding: 10px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        #filter-bar input,
        #filter-bar select {
            padding: 8px;
            border: 1px solid #b22222;
            border-radius: 4px;
        }

        #products-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px;
            justify-items: center;
        }

        .product-card {
            border: 1px solid #b22222;
            border-radius: 8px;
            overflow: hidden;
            width: 100%;
            max-width: 250px;
            text-align: center;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 16px;
        }

        .product-card img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            margin-bottom: 8px;
        }

        .product-card h3 {
            margin: 10px 0;
            font-size: 18px;
            color: #b22222;
            padding: 5px 0;
        }

        .product-card p {
            margin: 8px 0;
            font-size: 14px;
            color: #666;
            padding: 4px 0;
        }

        .button-container {
            display: flex;
            justify-content: space-around;
            gap: 10px;
            margin-top: 10px;
        }

        .button-container button {
            padding: 10px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .like-button {
            background-color: #b22222;
            color: white;
        }

        .cart-button {
            background-color: #ff6347;
            color: white;
        }

        .buy-button {
            background-color: #dc143c;
            color: white;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .pagination button {
            padding: 10px 16px;
            border: 1px solid #b22222;
            background-color: #f4cccc;
            cursor: pointer;
            margin: 0 5px;
            border-radius: 4px;
        }

        .pagination button:disabled {
            background-color: #e9ecef;
            cursor: not-allowed;
        }

        #product-preview {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid #b22222;
            border-radius: 8px;
            width: 300px;
            padding: 20px;
            z-index: 1000;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #product-preview h2 {
            margin: 0 0 10px;
            font-size: 22px;
            color: #b22222;
            padding: 5px 0;
        }

        #product-preview img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            margin-bottom: 10px;
        }

        #product-preview p {
            margin: 10px 0;
            font-size: 14px;
            color: #666;
            padding: 4px 0;
        }

        #product-preview .close-button {
            background-color: #b22222;
            color: white;
            padding: 8px;
            border: none;
            cursor: pointer;
            border-radius: 4px;
            float: right;
        }

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        h1, h2, h3, h4, h5, h6 {
            margin: 10px 0;
            padding: 5px 0;
        }

        p {
            margin: 8px 0;
            padding: 4px 0;
        }

        #cart,
        #favorites {
            padding: 20px;
        }

        section {
            padding: 20px;
            margin: 0 auto;
        }

        #home {
            padding: 20px;
            margin: 0 auto;
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .product-buttons {
    margin-top: 10px;
    display: flex;
    gap: 10px;
}

.product-buttons button {
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
}

.like-button {
    color: #ff6347; /* Default color */
    background-color: transparent;
    border: 1px solid #ff6347;
    border-radius: 50%;
    width: 40px;
    height: 40px;
}

.like-button.liked {
    color: #b22222; /* Highlighted color */
}

.add-to-cart-button {
    background-color: #4caf50;
    color: white;
}

.buy-now-button {
    background-color: #008cba;
    color: white;
}

.filter-bar {
    display: flex;
    gap: 10px;
    margin-bottom: 20px;
}

#search-box {
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

#category-filter {
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
}

.products-list {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}


    </style>

</head>
<body>
<header>
    <h1>LALAMONS</h1>
    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! You are logged in.</p>
    <?php else: ?>
        <p>You are not logged in. Please log in to access your cart.</p>
    <?php endif; ?>
    
    <h1>LALAMONS</h1>
    <nav>
        <a href="main.php" onclick="showSection('home')">Home</a>
        <a href="cart.php" onclick="showSection('cart')">Cart</a>
        <a href="favorites.php" onclick="showSection('favorites')">Favorites</a>
        <a href="profile.php" onclick="showSection('profile')">Profile</a>
    </nav>
</header>

<div id="filter-bar" class="filter-bar">
    <input type="text" id="search-box" placeholder="Search products..." onkeyup="filterProducts()">
    <select id="category-filter" onchange="filterProducts()">
        <option value="all">All Categories</option>
        <option value="1">Food</option>
        <option value="2">Beverages</option>
        <option value="3">Desserts</option>
        <option value="4">Snacks</option>
    </select>
</div>

<main>
    <div id="products-list" class="products-list">
        <?php foreach ($products as $product): ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>">
                <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                <p>Price: PHP <?php echo htmlspecialchars($product['price']); ?></p>
                <div class="product-buttons">
                    <button class="like-button" data-product-id="<?php echo htmlspecialchars($product['product_id']); ?>" onclick="toggleFavorite(<?php echo htmlspecialchars($product['product_id']); ?>)">
                        ♡
                    </button>
                    <button class="add-to-cart-button" onclick="addToCart(<?php echo htmlspecialchars($product['product_id']); ?>)">
                        Add to Cart
                    </button>
                    <button class="buy-now-button" onclick="buyNow(<?php echo htmlspecialchars($product['product_id']); ?>)">
                        Buy Now
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="pagination">
        <button id="prev-btn" onclick="prevPage()" disabled>Prev</button>
        <button id="next-btn" onclick="nextPage()">Next</button>
    </div>

    <section id="favorites" style="display:none;">
        <h2>Your Favorites</h2>
        <p>No favorites yet. Add products to your favorites by clicking the heart icon.</p>
    </section>

    <section id="profile" style="display:none;">
        <h2>Your Profile</h2>
        <p>Profile information and settings.</p>
    </section>
</main>

<div id="product-preview">
    <h2>Product Preview</h2>
    <img id="preview-img" src="" alt="Product Image">
    <h3 id="preview-name"></h3>
    <p id="preview-price"></p>
    <button class="close-button" onclick="closePreview()">Close</button>
</div>

<div id="overlay"></div>

<script>
    let currentPage = 1;
    const itemsPerPage = 15;

    document.addEventListener('DOMContentLoaded', () => {
        fetchProducts();
        document.getElementById("search-box").addEventListener("input", filterProducts);
        document.getElementById("category-filter").addEventListener("change", filterProducts);
    });

    async function fetchProducts() {
        try {
            const response = await fetch('fetch_products.php');
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const data = await response.json();
            products = data.map(product => ({
                ...product,
                image_url: product.image_url.startsWith('http')
                    ? product.image_url
                    : `./images/${product.image_url}`
            }));
            displayProducts();
        } catch (error) {
            console.error('Failed to fetch products:', error);
        }
    }

    const products = <?php echo json_encode($products); ?>;

    function displayProducts() {
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        const visibleProducts = products.slice(startIndex, endIndex);

        const productContainer = document.getElementById('products-list');
        productContainer.innerHTML = '';

        visibleProducts.forEach(product => {
            const productCard = document.createElement('div');
            productCard.classList.add('product-card');
            productCard.innerHTML = `
                <img src="${product.image_url}" alt="${product.product_name}">
                <h3>${product.product_name}</h3>
                <p>Price: PHP ${product.price}</p>
                <div class="product-buttons">
                    <button class="like-button" onclick="toggleFavorite(${product.product_id})">♡</button>
                    <button class="add-to-cart-button" onclick="addToCart(${product.product_id})">Add to Cart</button>
                    <button class="buy-now-button" onclick="buyNow(${product.product_id})">Buy Now</button>
                </div>
            `;
            productContainer.appendChild(productCard);
        });

        updatePaginationButtons();
    }

    function filterProducts() {
        const searchQuery = document.getElementById('search-box').value.toLowerCase();
        const categoryFilter = document.getElementById('category-filter').value;

        fetch(`filter_products.php?search=${encodeURIComponent(searchQuery)}&category_id=${encodeURIComponent(categoryFilter)}`)
            .then(response => response.json())
            .then(filteredResults => {
                products = filteredResults.map(product => ({
                    ...product,
                    image_url: product.image_url.startsWith('http')
                        ? product.image_url
                        : `./images/${product.image_url}`
                }));
                currentPage = 1;
                displayProducts();
            })
            .catch(error => console.error('Error fetching filtered products:', error));
    } 

    function showPreview(product) {
        document.getElementById('product-id').value = product.id;
        document.getElementById('preview-img').src = product.image_url;
        document.getElementById('preview-name').textContent = product.name;
        document.getElementById('preview-price').textContent = product.price;
        document.getElementById('product-preview').style.display = 'block';
        document.getElementById('overlay').style.display = 'block';
    }

    function closePreview() {
        document.getElementById('product-preview').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }

    function updatePaginationButtons() {
        document.getElementById('prev-btn').disabled = currentPage === 1;
        document.getElementById('next-btn').disabled = currentPage * itemsPerPage >= products.length;
    }

    function nextPage() {
        if (currentPage * itemsPerPage < products.length) {
            currentPage++;
            displayProducts();
        }
    }

    function prevPage() {
        if (currentPage > 1) {
            currentPage--;
            displayProducts();
        }
    }

    let favoriteProducts = new Set();

    document.addEventListener('DOMContentLoaded', () => {
        const savedFavorites = localStorage.getItem('favorites');
        if (savedFavorites) {
            favoriteProducts = new Set(JSON.parse(savedFavorites));
        }
        updateFavoriteButtons();
    });

    function toggleFavorite(productId) {
        fetch('update_favorites.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ product_id: productId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                updateFavoriteButtons();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to update favorites.');
        });
    }

    function updateFavoriteButtons() {
        const favoriteButtons = document.querySelectorAll('.like-button');
        favoriteButtons.forEach(button => {
            const productId = button.getAttribute('data-product-id');
            if (favoriteProducts.has(parseInt(productId))) {
                button.textContent = '♥';
            } else {
                button.textContent = '♡';
                button.style.backgroundColor = '#fffff';
            }
        });
    }

    function addToCart(productId) {
        const quantity = 1; 
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append('quantity', quantity);

        fetch('add_to_cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                window.location.href = 'cart.php';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to add product to cart.');
        });
    }

    function buyNow(productId) {
        window.location.href = 'pay.php?product_id=' + productId; // Redirect to pay.php with product ID
    }

</script>
</body>
</html>