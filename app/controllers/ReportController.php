<?php
/**
 * Report Controller
 * Handles all report-related operations
 */
class ReportController
{
    private $customerModel;
    private $orderModel;
    
    public function __construct($db = null)
    {
        if ($db) {
            $this->customerModel = new Customer($db);
            $this->orderModel = new Order($db);
        } else {
            global $customerModel, $orderModel;
            $this->customerModel = $customerModel;
            $this->orderModel = $orderModel;
        }
    }
    
    /**
     * Show customer report
     */
    public function customerReport()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        $customers = $this->customerModel->getAll();
        
        view('reports/customer', [
            'title' => 'Customer Purchase Report',
            'customers' => $customers
        ]);
    }
    
    /**
     * Show customer purchase history
     */
    public function customerPurchaseHistory($id)
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        // Default to current month
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        
        if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $startDate = $_GET['start_date'];
            $endDate = $_GET['end_date'];
        }
        
        $customer = $this->customerModel->getById($id);
        
        if (!$customer) {
            flash('error', 'Customer not found');
            redirect('/reports/customer');
        }
        
        $purchaseHistory = $this->orderModel->getCustomerPurchaseHistory($id, $startDate, $endDate);
        
        view('reports/customer_detail', [
            'title' => 'Purchase History: ' . $customer['name'],
            'customer' => $customer,
            'history' => $purchaseHistory,
            'startDate' => $startDate,
            'endDate' => $endDate
        ]);
    }
    
    /**
     * Show sales report
     */
    public function salesReport()
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }
        
        // Default to current month
        $startDate = date('Y-m-01');
        $endDate = date('Y-m-t');
        $groupBy = 'day';
        
        if (isset($_GET['start_date']) && isset($_GET['end_date'])) {
            $startDate = $_GET['start_date'];
            $endDate = $_GET['end_date'];
        }
        
        if (isset($_GET['group_by']) && in_array($_GET['group_by'], ['day', 'month', 'year'])) {
            $groupBy = $_GET['group_by'];
        }
        
        $salesSummary = $this->orderModel->getSalesSummary($groupBy, $startDate, $endDate);
        $orders = $this->orderModel->getByDateRange($startDate, $endDate);
        
        view('reports/sales', [
            'title' => 'Sales Reports',
            'salesSummary' => $salesSummary,
            'orders' => $orders,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'groupBy' => $groupBy
        ]);
    }
} 