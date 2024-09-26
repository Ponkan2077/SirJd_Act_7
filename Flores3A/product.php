<?php
session_start();
    if(isset($_SESSION['account'])){
        if(!($_SESSION['account']['is_staff'] || $_SESSION['account']['is_admin'] || $_SESSION['account']['is_customer'])){
            header('location: login.php');
        }
    } else {
        header('location: login.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product</title>
    <style>
        /* Styling for the search results */
        p.search {
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <!-- Link to a page where users can add a new product -->
    <a href="addproduct.php">Add Product</a>
    
    <?php
        // Include the Product class file
        require_once 'product.class.php';

        // Create an instance of the Product class to interact with the database
        $productObj = new Product();

        // Initialize keyword and category variables for filtering
        $keyword = $category = '';
        
        // Check if the form is submitted via POST method and 'search' button is clicked
        if($_SERVER['REQUEST_METHOD'] == 'POST'){
            // Sanitize input from the search form
            $keyword = htmlentities($_POST['keyword']);
            $category = htmlentities($_POST['category']);
        }

        // Retrieve the filtered list of products, even if no search is conducted
        $array = $productObj->showAll($keyword, $category);
    ?>

    <!-- Form for filtering products based on category and keyword -->
    <form action="" method="post">
        <!-- Category dropdown menu -->
        <label for="category">Category</label>
        <select name="category" id="category">
            <option value="">All</option>
            <!-- Retain selected value after form submission -->
            <?php
                $categoryList = $productObj->fetchCategory();
                foreach ($categoryList as $cat){
            ?>
                <option value="<?= $cat['id'] ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>><?= $cat['name'] ?></option>
            <?php
                }
            ?>
        </select>
        <!-- Search input field for keywords -->
        <label for="keyword">Search</label>
        <input type="text" name="keyword" id="keyword" value="<?= $keyword ?>">
        <!-- Submit button for search -->
        <input type="submit" value="Search" name="search" id="search">
    </form>

    <!-- Display the products in an HTML table -->
    <table border="1">
        <tr>
            <th>No.</th> <!-- Column for numbering the products -->
            <th>Code</th> <!-- Column for the product code -->
            <th>Name</th> <!-- Column for the product name -->
            <th>Category</th> <!-- Column for the product category -->
            <th>Price</th> <!-- Column for the product price -->
            <th>Total Stocks</th>
            <th>Available Stocks</th>
            <th>Action</th> <!-- Column for actions like editing or deleting the product -->
        </tr>
        
        <?php
        $i = 1; // Initialize a counter for numbering the rows
        // If no products are found, display a "No product found" message
        if (empty($array)) {
        ?>
            <tr>
                <td colspan="7"><p class="search">No product found.</p></td>
            </tr>
        <?php
        }
        // Loop through the array of products and display each product in a table row
        foreach ($array as $arr) {
            $available = $arr['stock_in'] - $arr['stock_out'];
        ?>
        <tr>
            <!-- Display the row number -->
            <td><?= $i ?></td>
            <td><?= $arr['code'] ?></td>
            <!-- Display the product name -->
            <td><?= $arr['name'] ?></td>
            <!-- Display the product category -->
            <td><?= $arr['category_name'] ?></td>
            <!-- Display the product price -->
            <td><?= $arr['price'] ?></td>
            <td><?= $arr['stock_in'] ?></td>
            <td><?= $available ?></td>
            <!-- Action links: Edit and Delete -->
            <td>
                <?php if ($_SESSION['account']['is_admin'] || $_SESSION['account']['is_staff']){?>
                <a href="stocks.php?id=<?= $arr['id'] ?>">Stock In/Out</a>
                <!-- Link to edit the product -->
                <a href="editproduct.php?id=<?= $arr['id'] ?>">Edit</a>
                <!-- Delete button with product name and ID as data attributes -->
               <?php
                } 
               ?>
                <?php
                    if ($_SESSION['account']['is_admin']){
                ?>
                <a href="#" class="deleteBtn" data-id="<?= $arr['id'] ?>" data-name="<?= $arr['name'] ?>" >Delete</a>
                <?php
                    }
                ?>
            </td>
        </tr>
        <?php
            $i++; // Increment the counter for the next row
        }
        ?>
    </table>
    
    <!-- Link the external JavaScript file that contains event handling for deleting products -->
    <script src="./product.js"></script>
</body>
</html>
