<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <link rel="stylesheet" href="views/css/style.css">
</head>

<body>
    <div class="container">
        <h1>Product List</h1>

        <form id="searchForm">
            <input type="text" id="searchQuery" placeholder="Search products by name">
            <button type="submit">Search</button>
        </form>

        <table id="productTable" class="styled-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Edit Actions</th> <!-- Cột hành động thêm vào -->
                    <th>Delete Actions</th> <!-- Cột hành động thêm vào -->
                </tr>
            </thead>
            <tbody>
                <!-- Products will be displayed here -->
            </tbody>
        </table>

        <div class="form-container">
            <form id="addProductForm">
                <h2>Add New Product</h2>
                <input type="text" id="productName" name="name" placeholder="Product Name" required>
                <input type="number" id="productPrice" name="price" placeholder="Product Price" required>
                <button type="submit">Save</button>
                <button type="reset">Cancel</button>
            </form>

            <form id="editProductForm">
                <h2>Edit Product</h2>
                <input type="hidden" id="editProductId" name="id"> <!-- Ẩn input id của sản phẩm -->
                <input type="text" id="editProductName" name="name" placeholder="Product Name" required>
                <input type="number" id="editProductPrice" name="price" placeholder="Product Price" required>
                <button type="submit">Save</button>
                <button type="reset">Cancel</button>
            </form>
        </div>

        <a href="index.php/products">url</a>
        <a href="../"> Trang chủ</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Hàm để lấy danh sách sản phẩm
        function getProducts() {
            $.ajax({
                url: 'index.php/products',
                method: 'GET',
                success: function (response) {
                    let products = JSON.parse(response);
                    let tableBody = $('#productTable tbody');
                    tableBody.empty();

                    if (products["data"].length === 0)
                        tableBody.append(`
                        <tr>
                            <td colspan="5">Chưa có sản phẩm nào!</td>
                        </tr> 
                        `);
                    else
                        products["data"].forEach(function (product) {
                            tableBody.append(`
                        <tr>
                            <td>${product.id}</td>
                            <td>${product.name}</td>
                            <td>${product.price}$</td>
                            <td>
                                <button class="editButton" data-id="${product.id}" data-name="${product.name}" data-price="${product.price}">Edit</button>
                            </td>
                            <td><button class="delete-btn" data-id="${product.id}">Delete</button></td>
                        </tr> 
                        `);
                        });
                    // Gán sự kiện click cho các nút Edit
                    $('.editButton').click(function () {
                        let productId = $(this).data('id');
                        let productName = $(this).data('name');
                        let productPrice = $(this).data('price');

                        // Điền thông tin sản phẩm vào form sửa
                        $('#editProductId').val(productId);
                        $('#editProductName').val(productName);
                        $('#editProductPrice').val(productPrice);
                    });
                }
            });
        }
        // Gọi hàm để lấy danh sách sản phẩm khi trang được tải
        $(document).ready(function () {
            getProducts();
        });

        // Gửi dữ liệu sản phẩm mới qua POST
        $('#addProductForm').submit(function (event) {
            event.preventDefault();
            let name = $('#productName').val();
            let price = $('#productPrice').val();

            $.ajax({
                url: 'index.php/products',
                method: 'POST',
                contentType: 'application/json',  // Đảm bảo gửi dữ liệu dạng JSON
                data: JSON.stringify({ name: name, price: price }), // Gửi dữ liệu qua POST
                success: function (response) {
                    alert('Product added successfully!');
                    getProducts();  // Cập nhật danh sách sản phẩm

                    $('#productName').val("");
                    $('#productPrice').val("");
                },
                error: function (xhr, status, error) {
                    alert(`Failed to add product!`);
                }
            });
        });
        // Gửi yêu cầu PUT để cập nhật sản phẩm
        $('#editProductForm').submit(function (event) {
            event.preventDefault();
            let id = $('#editProductId').val();
            let name = $('#editProductName').val();
            let price = $('#editProductPrice').val();

            $.ajax({
                url: `index.php/edit-product/${id}`, // Sử dụng URL với ID sản phẩm
                method: 'PUT',
                contentType: 'application/json',  // Đảm bảo gửi dữ liệu dạng JSON
                data: JSON.stringify({ name: name, price: price }),  // Chuyển dữ liệu thành JSON
                success: function (response) {
                    alert('Product updated successfully!');
                    getProducts();  // Cập nhật danh sách sản phẩm

                    $('#editProductId').val("");
                    $('#editProductName').val("");
                    $('#editProductPrice').val("");
                },
                error: function (xhr, status, error) {
                    alert(`Failed to edit product!`);
                }
            });
        });

        // Gọi hàm xóa khi người dùng nhấn vào nút "Delete"
        $(document).on('click', '.delete-btn', function () {
            const id = $(this).data('id'); // Lấy ID sản phẩm từ thuộc tính data-id
            deleteProduct(id);
        });
        // Hàm để xóa sản phẩm
        function deleteProduct(id) {
            $.ajax({
                url: `index.php/delete-product/${id}`, // Sửa URL để đúng với cấu trúc API
                method: 'DELETE',
                success: function (response) {
                    alert('Product deleted successfully!');
                    getProducts();  // Cập nhật danh sách sản phẩm sau khi xóa
                },
                error: function (xhr, status, error) {
                    alert(`Failed to delete product!`);
                }
            });
        }

        // Tìm kiếm 
        $('#searchForm').submit(function (event) {
            event.preventDefault();
            $.ajax({
                url: 'index.php/products?search_term=' + $('#searchQuery').val(),
                method: 'GET',
                success: function (response) {
                    let products = JSON.parse(response);
                    let tableBody = $('#productTable tbody');
                    tableBody.empty();

                    if (products["data"].length === 0)
                        tableBody.append(`
                        <tr>
                            <td colspan="5">Không tồn tại tên sản phẩm có chứa chuỗi ký tự "${$('#searchQuery').val()}"!</td>
                        </tr> 
                        `);
                    else
                        products["data"].forEach(function (product) {
                            tableBody.append(`
                        <tr>
                            <td>${product.id}</td>
                            <td>${product.name}</td>
                            <td>${product.price}$</td>
                            <td>
                                <button class="editButton" data-id="${product.id}" data-name="${product.name}" data-price="${product.price}">Edit</button>
                            </td>
                            <td><button class="delete-btn" data-id="${product.id}">Delete</button></td>
                        </tr> 
                        `);
                        });
                    // Gán sự kiện click cho các nút Edit
                    $('.editButton').click(function () {
                        let productId = $(this).data('id');
                        let productName = $(this).data('name');
                        let productPrice = $(this).data('price');

                        // Điền thông tin sản phẩm vào form sửa
                        $('#editProductId').val(productId);
                        $('#editProductName').val(productName);
                        $('#editProductPrice').val(productPrice);
                    });
                }
            });
        });
    </script>
</body>

</html>