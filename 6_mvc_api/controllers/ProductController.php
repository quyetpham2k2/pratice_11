<?php
require_once 'Model/ProductModel.php';

class ProductController
{
    // 200 OK: Yêu cầu thành công.
    // 201 Created: Tạo tài nguyên mới thành công.
    // 400 Bad Request: Dữ liệu yêu cầu không hợp lệ.
    // 401 Unauthorized: Không có quyền truy cập.
    // 404 Not Found: Không tìm thấy tài nguyên.
    // 500 Internal Server Error: Lỗi server.

    private $productDB;

    public function __construct()
    {
        $this->productDB = new ProductModel();
    }
    public static function sendResponse($statusCode, $message, $data = null)
    {
        http_response_code($statusCode);
        $response = [
            'status' => $statusCode === 200 || $statusCode === 201 ? 'success' : 'error',
            'message' => $message,
            "backBtn" => "<a style='display:block;text-align:center;border:1px solid black;text-decoration:none;color:black;font-weight:bold;margin-top:16px;padding:8px;'href='javascript:history.back()'>Back</a>"
        ];

        if ($data !== null)
            $response['data'] = $data;

        echo json_encode($response, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit; // Đảm bảo dừng xử lý sau khi gửi phản hồi
    }


    // Lấy tất cả sản phẩm
    public function getProducts()
    {
        $ProductModel = ProductModel::getAllProducts();
        if ($ProductModel)
            self::sendResponse(200, 'Success to get all ProductModel', $ProductModel);
        else
            self::sendResponse(404, 'No ProductModel found');
    }
    public function searchProductsByName($name)
    {
        $ProductModel = ProductModel::searchProducts($name);
        if ($ProductModel)
            self::sendResponse(200, 'Success to get all ProductModel by name', $ProductModel);
        else
            self::sendResponse(404, "No ProductModel found by name \"$name\"");
    }

    public function createProduct($name, $price)
    {
        if (empty($name) || empty($price))
            throw new Exception("Missing required parameters: name, or price.");

        $product = new ProductModel();
        $product->name = $name;
        $product->price = $price;
        if ($product->createProduct())
            self::sendResponse(201, "Product created successfully");
        else
            self::sendResponse(500, "Failed to create product!");
    }
    public function editProduct($id, $name, $price)
    {
        if (empty($id) || empty($name) || empty($price)) {
            throw new Exception("Missing required parameters: id, name, or price.");
        }

        $product = ProductModel::find($id);
        $product->name = $name;
        $product->price = $price;
        if ($product->editProduct())
            self::sendResponse(200, "Product updated successfully");
        else
            self::sendResponse(500, "Failed to update product!");
    }

    public function deleteProduct($id)
    {
        if (empty($id))
            throw new Exception("Product ID is required");

        $product = ProductModel::find($id);
        if ($product->deleteProduct())
            self::sendResponse(200, "Product deleted successfully");
        else
            self::sendResponse(500, "Failed to delete product!");
    }
}
?>