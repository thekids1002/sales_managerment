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
        <h6 class="m-0 font-weight-bold text-primary">Create New Order</h6>
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
                                <option value="<?= e($customer['id']) ?>" data-name="<?= e($customer['name']) ?>" data-email="<?= e($customer['email']) ?>" data-phone="<?= e($customer['phone']) ?>" data-address="<?= e($customer['address']) ?>"><?= e($customer['name']) ?> (<?= e($customer['phone']) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
            </div>
            
            <h5 class="mb-3">Products</h5>
            
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
                                            <?= e($product['name']) ?> (<?= e($product['quantity']) ?> products available)
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
                                <div class="invalid-feedback">Not enough stock available!</div>
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
                                    <i class="fas fa-plus"></i> Add Product
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end"><strong>Total:</strong></td>
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
                    <i class="fas fa-arrow-left"></i> Go Back
                </a>
                <div>
                    <button type="button" class="btn btn-info" id="btn-print-preview">
                        <i class="fas fa-print"></i> Print Preview
                    </button>
                    <button type="submit" class="btn btn-primary" id="btn-save-order">
                        <i class="fas fa-save"></i> Save
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
                <h5 class="modal-title">Order Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="print-preview-content">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
            
            // Fetch the invoice template
            fetch('<?= baseUrl('/orders/template') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-Token': document.querySelector('input[name="csrf_token"]').value
                },
                body: JSON.stringify({
                    order: formData,
                    items: formData.items,
                    isPreview: true
                })
            })
            .then(response => response.text())
            .then(html => {
                // Show in modal
                document.getElementById('print-preview-content').innerHTML = html;
                
                // Open modal using Bootstrap 5 modal
                const modal = new bootstrap.Modal(document.getElementById('print-preview-modal'));
                modal.show();
            })
            .catch(error => {
                console.error('Error generating preview:', error);
                alert('Failed to generate preview. Please try again.');
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
                        errorMessage = 'Some products have quantities exceeding stock!';
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
                        <input type="text" class="form-control item-price" name="items[${itemCount}][unit_price]" readonly>
                        <span class="input-group-text">VND</span>
                    </div>
                </td>
                <td>
                    <input type="number" class="form-control item-quantity" name="items[${itemCount}][quantity]" min="1" value="1" required>
                    <div class="invalid-feedback">Not enough stock available!</div>
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