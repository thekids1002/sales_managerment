<?php
/**
 * Router Class
 * Handles URL routing and dispatching to appropriate controllers
 */
class Router
{
    private $routes = [];
    private $notFoundCallback;
    
    /**
     * Add a route
     * 
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $pattern URL pattern
     * @param callable $callback Function to execute for this route
     * @return void
     */
    public function addRoute($method, $pattern, $callback) 
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'pattern' => $pattern,
            'callback' => $callback
        ];
    }
    
    /**
     * Add a GET route
     * 
     * @param string $pattern URL pattern
     * @param callable $callback Function to execute for this route
     * @return void
     */
    public function get($pattern, $callback) 
    {
        $this->addRoute('GET', $pattern, $callback);
    }
    
    /**
     * Add a POST route
     * 
     * @param string $pattern URL pattern
     * @param callable $callback Function to execute for this route
     * @return void
     */
    public function post($pattern, $callback) 
    {
        $this->addRoute('POST', $pattern, $callback);
    }
    
    /**
     * Add a PUT route
     * 
     * @param string $pattern URL pattern
     * @param callable $callback Function to execute for this route
     * @return void
     */
    public function put($pattern, $callback) 
    {
        $this->addRoute('PUT', $pattern, $callback);
    }
    
    /**
     * Add a DELETE route
     * 
     * @param string $pattern URL pattern
     * @param callable $callback Function to execute for this route
     * @return void
     */
    public function delete($pattern, $callback) 
    {
        $this->addRoute('DELETE', $pattern, $callback);
    }
    
    /**
     * Set 404 not found handler
     * 
     * @param callable $callback Function to execute when route not found
     * @return void
     */
    public function notFound($callback) 
    {
        $this->notFoundCallback = $callback;
    }
    
    /**
     * Match the current request to a route
     * 
     * @param string $method HTTP method
     * @param string $uri Request URI
     * @return array|false Matched route or false if no match
     */
    private function matchRoute($method, $uri) 
    {
        $method = strtoupper($method);
        
        foreach ($this->routes as $route) {
            // Check if method matches
            if ($route['method'] !== $method) {
                continue;
            }
            
            // Convert route pattern to regex
            $pattern = preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $route['pattern']);
            $pattern = '#^' . $pattern . '$#';
            
            // Check if URI matches pattern
            if (preg_match($pattern, $uri, $matches)) {
                // Extract named parameters
                $params = array_filter($matches, function($key) {
                    return !is_numeric($key);
                }, ARRAY_FILTER_USE_KEY);
                
                return [
                    'callback' => $route['callback'],
                    'params' => $params
                ];
            }
        }
        
        return false;
    }
    
    /**
     * Dispatch the current request
     * 
     * @return void
     */
    public function dispatch() 
    {
        // Get current request method and URI
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '/';
        
        // Remove query string
        $uri = parse_url($uri, PHP_URL_PATH);
        
        // Match route
        $route = $this->matchRoute($method, $uri);
        
        if ($route) {
            // Call route callback with parameters
            call_user_func_array($route['callback'], $route['params']);
        } else if ($this->notFoundCallback) {
            // Call not found callback
            call_user_func($this->notFoundCallback);
        } else {
            // Default 404 response
            header("HTTP/1.0 404 Not Found");
            echo "404 Not Found";
        }
    }
} 