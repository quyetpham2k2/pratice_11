<?php
require_once 'BaseModel.php';

class ProductModel extends BaseModel
{
	protected $tableName = 'Products';
	protected $columns = ['name', 'price'];

	public static function searchProducts($name = null)
	{
		$model = new static();
		if ($name) {
			$stmt = $model->connect->prepare("SELECT products.id, products.name, products.price FROM products WHERE products.name COLLATE utf8mb4_unicode_ci LIKE :searchTerm");
			$stmt->execute([':searchTerm' => '%' . $name . '%']);
		} else {
			// Nếu không có tên tìm kiếm, lấy tất cả sản phẩm
			$stmt = $model->connect->prepare("SELECT products.id, products.name, products.price FROM products");
			$stmt->execute();
		}
		// Fetch kết quả dưới dạng đối tượng của lớp Products
		try {
			$result = $stmt->fetchAll(PDO::FETCH_CLASS, get_class($model));
			return $result;
		} catch (Exception $ex) {
			return null;
		}
	}
	public static function getAllProducts()
	{
		return self::getAll();
	}

	public function createProduct()
	{
		return $this->insert();
	}
	public function editProduct()
	{
		return $this->update();
	}
	public function deleteProduct()
	{
		return $this->delete();
	}
}
?>