<?php
require_once 'controllers/ProductController.php';

$controller = new ProductController();
$request = isset($_SERVER['PATH_INFO']) ? explode('/', trim($_SERVER['PATH_INFO'], '/')) : [""];

switch ($_SERVER['REQUEST_METHOD']) {
    // r-me: GET
    case 'GET':
        if ($request[0] === '')
            include_once 'views/product_list.php';
        elseif ($request[0] === 'products' && !isset($_GET["search_term"]))
            $controller->getProducts();
        elseif (isset($_GET["search_term"]))
            $controller->searchProductsByName($_GET['search_term']);
        else
            ProductController::sendResponse(404, "Endpoint not found");
        break;

    // r-me: POST
    case 'POST':
        if ($request[0] === 'products') {
            $name = $_POST['name'] ?? '';
            $price = $_POST['price'] ?? '';

            $jsonData = file_get_contents('php://input');
            $data = json_decode($jsonData, true);
            if ($data !== null) {
                $name = $data['name'];
                $price = $data['price'];
            }

            if (!$name || !$price)
                ProductController::sendResponse(400, "Name and price are required");
            else
                $controller->createProduct($name, $price);
        } else
            ProductController::sendResponse(404, "Endpoint not found");
        break;

    // r-me: PUT
    case 'PUT':
        if ($request[0] === 'edit-product') {
            $putData = json_decode(file_get_contents("php://input"), true);

            if (!$putData || !isset($putData['name']) || !isset($putData['price'])) {
                ProductController::sendResponse(400, "Invalid data. Both 'name' and 'price' are required.");
                return;
            }
            if (isset($request[1]))
                $id = $request[1];
            else {
                ProductController::sendResponse(400, "Product ID is missing");
                return;
            }
            $name = $putData['name'];
            $price = $putData['price'];

            try {
                $controller->editProduct($id, $name, $price);
            } catch (Exception $e) {
                ProductController::sendResponse(500, "Failed to update product: " . $e->getMessage());
            }
        } else
            ProductController::sendResponse(404, "Endpoint not found");
        break;

    // r-me: DELETE
    case 'DELETE':
        if ($request[0] === 'delete-product') {

            if (isset($request[1]))
                $id = $request[1];
            else {
                ProductController::sendResponse(400, "Product ID is missing");
                return;
            }

            $controller->deleteProduct($id);
        } else
            ProductController::sendResponse(404, "Endpoint not found");
        break;

    // r-me: Default
    default:
        ProductController::sendResponse(405, "Method Not Allowed");
        break;
}
?>