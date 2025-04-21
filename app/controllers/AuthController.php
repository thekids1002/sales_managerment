<?php
/**
 * Auth Controller
 * Handles all authentication-related operations
 */
class AuthController
{
    private $db;
    
    public function __construct($db = null)
    {
        if ($db) {
            $this->db = $db;
        } else {
            global $db;
            $this->db = $db;
        }
    }
    
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        if (isLoggedIn()) {
            redirect('/');
        }
        view('auth/login', ['title' => 'Login']);
    }
    
    /**
     * Process login
     */
    public function login()
    {
        $username = isset($_POST['username']) ? $_POST['username'] : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';
        
        // Validate input
        if (empty($username) || empty($password)) {
            flash('error', 'Username and password are required');
            redirect('/login');
        }
        
        // Check CSRF token
        if (!isset($_POST['csrf_token']) || !csrf_verify($_POST['csrf_token'])) {
            flash('error', 'Invalid request');
            redirect('/login');
        }
        
        $user = $this->db->fetch("SELECT * FROM users WHERE username = ?", [$username]);
        
        if ($user && password_verify($password, $user['password'])) {
            // Set user session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            flash('success', 'Login successful');
            redirect('/');
        } else {
            flash('error', 'Invalid username or password');
            redirect('/login');
        }
    }
    
    /**
     * Process logout
     */
    public function logout()
    {
        // Clear session
        session_unset();
        session_destroy();
        
        redirect('/login');
    }
} 