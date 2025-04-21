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
</style>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary"><?= isset($editing) && $editing ? 'Edit Draft Order' : 'Create New Order' ?></h6>
    </div>
    <div class="card-body">
        <form id="orderForm">
            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer <span class="text-danger">*</span></label>
                        <select class="form-select" id="customer_id" name="customer_id" required>
                            <option value="">Select Customer</option>
                            <?php foreach ($customers as $customer): ?>
                                <option value="<?= e($customer['id']) ?>" data-name="<?= e($customer['name']) ?>"><?= e($customer['name']) ?> (<?= e($customer['phone']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Order Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
            </div>
            
            <h5 class="mb-3">Order Items</h5>
            
            <div class="table-responsive mb-3">
                <table class="table table-bordered" id="orderItemsTable">
                    <thead>
                        <tr>
                            <th width="40%">Product</th>
                            <th width="20%">Price</th>
                            <th width="20%">Quantity</th>
                            <th width="15%">Total</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody id="order-items">
                        <tr class="order-item">
                            <td>
                                <select class="form-select product-select" name="items[0][product_id]" required>
                                    <option value="">Select Product</option>
                                    <?php foreach ($products as $product): ?>
                                        <option value="<?= e($product['id']) ?>" 
                                                data-name="<?= e($product['name']) ?>"
                                                data-price="<?= e($product['price']) ?>"
                                                data-max="<?= e($product['quantity']) ?>">
                                            <?= e($product['name']) ?> (<?= e($product['quantity']) ?> in stock)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control item-price" name="items[0][unit_price]" readonly>
                                </div>
                            </td>
                            <td>
                                <input type="number" class="form-control item-quantity" name="items[0][quantity]" min="1" value="1" required>
                                <div class="invalid-feedback">Not enough stock!</div>
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control item-total" readonly>
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
                                    <i class="fas fa-plus"></i> Add Item
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Order Total:</strong></td>
                            <td colspan="2">
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" class="form-control" id="order-total" readonly>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <div class="d-flex justify-content-between">
                <a href="<?= baseUrl('/orders') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Orders
                </a>
                <div>
                    <button type="button" class="btn btn-info" id="save-temp-order">
                        <i class="fas fa-save"></i> Save as Draft
                    </button>
                </div>
            </div>
        </form>
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
        orderForm.action = '<?= isset($draft) ? baseUrl('/orders/save-draft') : baseUrl('/orders/create') ?>';
        
        <?php if (isset($draft) && $draft): ?>
        // Add draft ID if editing
        const draftIdField = document.createElement('input');
        draftIdField.type = 'hidden';
        draftIdField.name = 'draft_id';
        draftIdField.value = '<?= $draft['id'] ?>';
        orderForm.appendChild(draftIdField);
        <?php endif; ?>
        
        
        // Add new item button
        document.getElementById('add-item').addEventListener('click', function() {
            addItem();
        });

        // Preview order button
        document.getElementById('save-temp-order').addEventListener('click', function() {
            // Validate form
            const { hasErrors, errorMessage } = validateForm();
            
            if (hasErrors) {
                alert(errorMessage);
                return;
            }
            // Save to session and then redirect to orders page
            saveToSession()
                .then(data => {
                    window.location.href = '<?= baseUrl('/orders') ?>';
                })
                .catch(error => {
                    console.error('Failed to save order data:', error);
                    alert('Failed to save order data. Please try again.');
                });
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
        
        // Save form data to session
        function saveToSession() {
            const formData = {
                customer_id: document.getElementById('customer_id').value,
                notes: document.getElementById('notes').value,
                items: []
            };
            
            document.querySelectorAll('.order-item').forEach(function(row) {
                const productSelect = row.querySelector('.product-select');
                const price = row.querySelector('.item-price');
                const quantity = row.querySelector('.item-quantity');
                
                if (productSelect.value) {
                    formData.items.push({
                        product_id: productSelect.value,
                        product_name: productSelect.options[productSelect.selectedIndex].dataset.name,
                        unit_price: price.value,
                        quantity: quantity.value
                    });
                }
            });
            
            // Send AJAX request to save to session
            return fetch('<?= baseUrl('/orders/save-temp') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
                },
                body: JSON.stringify(formData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                
                // Check if the response is JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('Response is not JSON. You may have been logged out or there was a server error.');
                }
                
                return response.json();
            })
            .then(data => {
                console.log('Saved to session', data);
                return data;
            })
            .catch(error => {
                console.error('Error saving to session:', error);
                // If the error suggests a session timeout, redirect to login
                if (error.message.includes('logged out')) {
                    window.location.href = '<?= baseUrl('/login') ?>';
                }
                throw error;
            });
        }
        
        // Validate form
        function validateForm() {
            let hasErrors = false;
            let errorMessage = '';
            
            // Check for customer
            if (!document.getElementById('customer_id').value) {
                hasErrors = true;
                errorMessage = 'Please select a customer.';
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
                        errorMessage = 'Some items have quantity exceeding available stock!';
                    }
                }
            });
            
            if (!hasItems && !hasErrors) {
                hasErrors = true;
                errorMessage = 'Please add at least one product to the order.';
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
                        <option value="">Select Product</option>
                        ${productOptions}
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" class="form-control item-price" name="items[${itemCount}][unit_price]" readonly>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control item-quantity" name="items[${itemCount}][quantity]" min="1" value="1" required>
                    <div class="invalid-feedback">Not enough stock!</div>
                </td>
                <td>
                    <div class="input-group">
                        <span class="input-group-text">$</span>
                        <input type="text" class="form-control item-total" readonly>
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