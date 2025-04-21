<?php ob_start(); ?>

<style>
.card-body {
    position: relative;
}

#save-notification {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 1050;
    display: none;
}

/* Modal styling */
#print-preview-modal .modal-dialog {
    max-width: 800px;
}

#print-preview-modal .modal-body {
    max-height: 70vh;
    overflow-y: auto;
}
</style>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tạo đơn hàng mới</h6>
    </div>
    <div class="card-body">
        <form id="orderForm">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Khách hàng <span class="text-danger">*</span></label>
                        <select class="form-select" id="customer_id" name="customer_id" required>
                            <option value="">Chọn khách hàng</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= e($customer['id']) ?>" data-name="<?= e($customer['name']) ?>" data-email="<?= e($customer['email']) ?>" data-phone="<?= e($customer['phone']) ?>" data-address="<?= e($customer['address']) ?>"><?= e($customer['name']) ?> (<?= e($customer['phone']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Ghi chú</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
            </div>
            
            <h5 class="mb-3">Sản phẩm</h5>
            
            <div class="table-responsive mb-3">
                <table class="table table-bordered" id="orderItemsTable">
                    <thead>
                        <tr>
                            <th width="40%">Sản phẩm</th>
                            <th width="20%">Giá</th>
                            <th width="20%">Số lượng</th>
                            <th width="15%">Thành tiền</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody id="order-items">
                        <tr class="order-item">
                            <td>
                                <select class="form-select product-select" name="items[0][product_id]" required>
                                    <option value="">Chọn sản phẩm</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?= e($product['id']) ?>" 
                                                data-name="<?= e($product['name']) ?>"
                                                data-price="<?= e($product['price']) ?>"
                                                data-max="<?= e($product['quantity']) ?>">
                                            <?= e($product['name']) ?> (<?= e($product['quantity']) ?> sản phẩm có sẵn)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control item-price" name="items[0][unit_price]" readonly>
                                    <span class="input-group-text">VND</span>
                                </div>
                            </td>
                            <td>
                                <input type="number" class="form-control item-quantity" name="items[0][quantity]" min="1" value="1" required>
                                <div class="invalid-feedback">Không đủ hàng trong kho!</div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <input type="text" class="form-control item-total" readonly>
                                    <span class="input-group-text">VND</span>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger btn-sm remove-item" tabindex="-1">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="5">
                                <button type="button" class="btn btn-success btn-sm" id="add-item">
                                    <i class="fas fa-plus"></i> Thêm sản phẩm
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                            <td colspan="2">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="order-total" readonly>
                                    <span class="input-group-text">VND</span>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= baseUrl('/orders') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
                <div>
                    <button type="button" class="btn btn-info" id="btn-print-preview">
                        <i class="fas fa-print"></i> In
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-save-order">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Print Preview Modal -->
<div class="modal fade" id="print-preview-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Xem trước đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="print-preview-content">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let itemCount = 1;
        
        // Initialize calculations
        calculateTotals();
        
        // Convert form to real form with method post
        const orderForm = document.getElementById('orderForm');
        orderForm.method = 'post';
        orderForm.action = '<?= baseUrl('/orders/create') ?>';
        
        // Add new item button
        document.getElementById('add-item').addEventListener('click', function() {
            addItem();
        });

        // Print preview button
        document.getElementById('btn-print-preview').addEventListener('click', function() {
            // Validate form first
            const { hasErrors, errorMessage } = validateForm();
            
            if (hasErrors) {
                alert(errorMessage);
                return;
            }
            
            // Generate print preview and show in modal
            generatePrintPreview();
        });
        
        // Form submission - prevent if there are validation errors
        orderForm.addEventListener('submit', function(e) {
            const { hasErrors, errorMessage } = validateForm();
            if (hasErrors) {
                e.preventDefault();
                alert(errorMessage);
                return false;
            }
        });
        
        // Add event listeners to initial row
        addItemEventListeners(document.querySelector('.order-item'));
        
        // Function to add event listeners to a row
        function addItemEventListeners(row) {
            const productSelect = row.querySelector('.product-select');
            const unitPrice = row.querySelector('.item-price');
            const quantity = row.querySelector('.item-quantity');
            const removeButton = row.querySelector('.remove-item');
            
            // Product selection change
            productSelect.addEventListener('change', function() {
                if (this.value) {
                    const option = this.options[this.selectedIndex];
                    const price = parseFloat(option.dataset.price || 0);
                    unitPrice.value = price.toFixed(2);
                    
                    // Check and update max quantity
                    const maxQty = parseInt(option.dataset.max);
                    quantity.max = maxQty;
                    
                    // Reset quantity if exceeds stock
                    if (parseInt(quantity.value) > maxQty) {
                        quantity.value = maxQty;
                        quantity.classList.add('is-invalid');
                    } else {
                        quantity.classList.remove('is-invalid');
                    }
                } else {
                    unitPrice.value = '';
                }
                
                updateItemTotal(row);
                calculateTotals();
            });
            
            // Quantity change
            quantity.addEventListener('input', function() {
                if (productSelect.value) {
                    const option = productSelect.options[productSelect.selectedIndex];
                    const maxQty = parseInt(option.dataset.max);
                    
                    if (parseInt(this.value) > maxQty) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                    }
                }
                
                updateItemTotal(row);
                calculateTotals();
            });
            
            // Remove item
            removeButton.addEventListener('click', function() {
                row.remove();
                
                // Ensure at least one row remains
                if (document.querySelectorAll('.order-item').length === 0) {
                    addItem();
                }
                
                calculateTotals();
            });
        }
        
        // Calculate item total
        function updateItemTotal(row) {
            const price = parseFloat(row.querySelector('.item-price').value) || 0;
            const quantity = parseInt(row.querySelector('.item-quantity').value) || 0;
            const total = price * quantity;
            
            row.querySelector('.item-total').value = total.toFixed(2);
        }
        
        // Calculate order total
        function calculateTotals() {
            let orderTotal = 0;
            
            document.querySelectorAll('.item-total').forEach(function(input) {
                orderTotal += parseFloat(input.value) || 0;
            });
            
            document.getElementById('order-total').value = orderTotal.toFixed(2);
        }
        
        // Generate print preview
        function generatePrintPreview() {
            const formData = {
                customer_id: document.getElementById('customer_id').value,
                customer_name: '',
                customer_email: '',
                customer_phone: '',
                customer_address: '',
                notes: document.getElementById('notes').value,
                total_amount: parseFloat(document.getElementById('order-total').value) || 0,
                id: 'PREVIEW',
                created_at: new Date().toISOString(),
                items: []
            };
            
            // Get customer info
            const customerSelect = document.getElementById('customer_id');
            if (customerSelect.value) {
                const option = customerSelect.options[customerSelect.selectedIndex];
                formData.customer_name = option.dataset.name || '';
                formData.customer_email = option.dataset.email || '';
                formData.customer_phone = option.dataset.phone || '';
                formData.customer_address = option.dataset.address || '';
            }
            
            // Get items
            document.querySelectorAll('.order-item').forEach(function(row) {
                const productSelect = row.querySelector('.product-select');
                const price = row.querySelector('.item-price');
                const quantity = row.querySelector('.item-quantity');
                
                if (productSelect.value) {
                    formData.items.push({
                        product_id: productSelect.value,
                        product_name: productSelect.options[productSelect.selectedIndex].dataset.name,
                        unit_price: parseFloat(price.value) || 0,
                        quantity: parseInt(quantity.value) || 0,
                        subtotal: parseFloat(price.value) * parseInt(quantity.value)
                    });
                }
            });
            
            // Generate preview HTML
            const previewHTML = `
                <div class="order-header text-center mb-4">
                    <h3>HÓA ĐƠN</h3>
                    <div class="company-info">
                        <h5>Hệ Thống Quản Lý Bán Hàng</h5>
                        <p>123 Đường ABC, Thành phố XYZ</p>
                        <p>Email: contact@example.com | Điện thoại: (123) 456-7890</p>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Khách hàng:</h5>
                        <p>
                            <strong>${formData.customer_name}</strong><br>
                            ${formData.customer_address ? formData.customer_address + '<br>' : ''}
                            ${formData.customer_email ? 'Email: ' + formData.customer_email + '<br>' : ''}
                            ${formData.customer_phone ? 'Điện thoại: ' + formData.customer_phone : ''}
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h5>Thông tin đơn hàng:</h5>
                        <p>
                            <strong>Mã đơn hàng:</strong> #${formData.id}<br>
                            <strong>Ngày tạo:</strong> ${new Date().toLocaleDateString('vi-VN')}<br>
                            ${formData.notes ? '<strong>Ghi chú:</strong> ' + formData.notes : ''}
                        </p>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>STT</th>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th>Số lượng</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${formData.items.map((item, index) => `
                                <tr>
                                    <td>${index + 1}</td>
                                    <td>${item.product_name}</td>
                                    <td>${item.unit_price.toLocaleString('vi-VN')} VND</td>
                                    <td>${item.quantity}</td>
                                    <td>${item.subtotal.toLocaleString('vi-VN')} VND</td>
                                </tr>
                            `).join('')}
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="4" class="text-end">Tổng cộng:</th>
                                <th>${formData.total_amount.toLocaleString('vi-VN')} VND</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <div class="footer text-center mt-4">
                    <p>PHIẾU XEM TRƯỚC - KHÔNG PHẢI HÓA ĐƠN HỢP LỆ</p>
                    <p>Cảm ơn quý khách đã mua hàng!</p>
                </div>
            `;
            
            // Show in modal
            document.getElementById('print-preview-content').innerHTML = previewHTML;
            
            // Open modal using Bootstrap 5 modal
            const modal = new bootstrap.Modal(document.getElementById('print-preview-modal'));
            modal.show();
        }
        
        // Validate form
        function validateForm() {
            let hasErrors = false;
            let errorMessage = '';
            
            // Check for customer
            if (!document.getElementById('customer_id').value) {
                hasErrors = true;
                errorMessage = 'Vui lòng chọn khách hàng.';
                return { hasErrors, errorMessage };
            }
            
            // Check for items
            let hasItems = false;
            let hasStockError = false;
            document.querySelectorAll('.order-item').forEach(function(row) {
                const productSelect = row.querySelector('.product-select');
                
                if (productSelect.value) {
                    hasItems = true;
                    
                    // Check stock
                    const option = productSelect.options[productSelect.selectedIndex];
                    const max = parseInt(option.dataset.max || '0');
                    const quantity = parseInt(row.querySelector('.item-quantity').value || '0');
                    
                    if (quantity > max) {
                        hasErrors = true;
                        hasStockError = true;
                        errorMessage = 'Một số sản phẩm có số lượng vượt quá tồn kho!';
                    }
                }
            });
            
            if (!hasItems && !hasErrors) {
                hasErrors = true;
                errorMessage = 'Vui lòng thêm ít nhất một sản phẩm vào đơn hàng.';
            }
            
            return { hasErrors, errorMessage };
        }

        // Function to add a new item with optional data
        function addItem(itemData = null) {
            const tbody = document.getElementById('order-items');
            const newRow = document.createElement('tr');
            newRow.className = 'order-item';
            
            const productOptions = Array.from(document.querySelectorAll('#orderItemsTable .product-select option'))
                .filter(opt => opt.value) // Remove empty options
                .map(opt => `<option value="${opt.value}" data-name="${opt.dataset.name}" data-price="${opt.dataset.price}" data-max="${opt.dataset.max}">${opt.textContent}</option>`)
                .join('');
            
            // Create new row HTML
            newRow.innerHTML = `
                <td>
                    <select class="form-select product-select" name="items[${itemCount}][product_id]" required>
                        <option value="">Chọn sản phẩm</option>
                        ${productOptions}
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control item-price" name="items[${itemCount}][unit_price]" readonly>
                        <span class="input-group-text">VND</span>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control item-quantity" name="items[${itemCount}][quantity]" min="1" value="1" required>
                    <div class="invalid-feedback">Không đủ hàng trong kho!</div>
                </td>
                <td>
                    <div class="input-group">
                        <input type="text" class="form-control item-total" readonly>
                        <span class="input-group-text">VND</span>
                    </div>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm remove-item" tabindex="-1">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            `;
            
            tbody.appendChild(newRow);
            
            // Increment item counter
            itemCount++;
            
            // Add event listeners to new row
            addItemEventListeners(newRow);
            
            // Fill item data if provided
            if (itemData) {
                const select = newRow.querySelector('.product-select');
                const price = newRow.querySelector('.item-price');
                const quantity = newRow.querySelector('.item-quantity');
                
                select.value = itemData.product_id;
                price.value = parseFloat(itemData.unit_price).toFixed(2);
                quantity.value = itemData.quantity;
                
                // Verify stock levels
                const selectedOption = select.options[select.selectedIndex];
                if (selectedOption) {
                    const maxStock = parseInt(selectedOption.dataset.max || 0);
                    const currentQty = parseInt(quantity.value);
                    
                    // Highlight if quantity exceeds stock
                    if (currentQty > maxStock) {
                        quantity.classList.add('is-invalid');
                    }
                }
                
                // Update total
                updateItemTotal(newRow);
            }
            
            return newRow;
        }
    });
</script>

<?php 
$content = ob_get_clean();
include VIEWS_PATH . '/layouts/main.php';
?>