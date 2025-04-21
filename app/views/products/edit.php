<?php ob_start(); ?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Edit Product</h6>
            </div>
            <div class="card-body">
                <form method="post" action="<?= baseUrl("/products/{$product['id']}/edit") ?>" onsubmit="return validateForm()">
                    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Product Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= e($product['name']) ?>" required>
                        <div id="nameError" class="text-danger" style="display:none;"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                        <select class="form-control" id="category_id" name="category_id" required>
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $category): ?>
                            <option value="<?= e($category['id']) ?>" <?= (isset($product['category_id']) && $product['category_id'] == $category['id']) ? 'selected' : '' ?>>
                                <?= e($category['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <div id="categoryError" class="text-danger" style="display:none;"></div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?= e($product['description']) ?></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" class="form-control" id="price" name="price" min="0.01" step="0.01" value="<?= e($product['price']) ?>" required>
                                    <div id="priceError" class="text-danger" style="display:none;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity" class="form-label">Quantity <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="<?= e($product['quantity']) ?>" required>
                                <div id="quantityError" class="text-danger" style="display:none;"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= baseUrl('/products') ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Products
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function validateForm() {
        var name = document.getElementById('name').value;
        var category = document.getElementById('category_id').value;
        var price = document.getElementById('price').value;
        var quantity = document.getElementById('quantity').value;
        var isValid = true;

        document.getElementById('nameError').style.display = 'none';
        document.getElementById('categoryError').style.display = 'none';
        document.getElementById('priceError').style.display = 'none';
        document.getElementById('quantityError').style.display = 'none';

        if (!name) {
            document.getElementById('nameError').innerText = 'Product Name is required.';
            document.getElementById('nameError').style.display = 'block';
            isValid = false;
        }
        if (!category) {
            document.getElementById('categoryError').innerText = 'Please select a Category.';
            document.getElementById('categoryError').style.display = 'block';
            isValid = false;
        }
        if (price === '' || price <= 0) {
            document.getElementById('priceError').innerText = 'Price is required and must be greater than 0.';
            document.getElementById('priceError').style.display = 'block';
            isValid = false;
        }
        if (quantity === '' || quantity < 0) {
            document.getElementById('quantityError').innerText = 'Quantity is required and cannot be negative.';
            document.getElementById('quantityError').style.display = 'block';
            isValid = false;
        }
        return isValid;
    }
</script>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?>