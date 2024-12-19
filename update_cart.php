<?php
session_start();
include('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['action'])) {
        $action = $_POST['action'];
        $product_id = $_POST['product_id'];
        
        switch ($action) {
            case 'update_quantity':
                $delta = $_POST['delta'];
                foreach ($_SESSION['cart'] as &$item) {
                    if ($item['id'] == $product_id) {
                        $item['quantity'] += $delta;
                        if ($item['quantity'] < 1) {
                            $item['quantity'] = 1;
                        }
                        break;
                    }
                }
                echo json_encode($_SESSION['cart']);
                break;
            
            case 'remove_item':
                foreach ($_SESSION['cart'] as $index => $item) {
                    if ($item['id'] == $product_id) {
                        unset($_SESSION['cart'][$index]);
                        break;
                    }
                }
                echo json_encode($_SESSION['cart']);
                break;
        }
    }
}
?>
