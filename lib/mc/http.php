<?php

namespace mc;

/**
 * HTTP Client Class
 * 
 * A lightweight HTTP client built on top of cURL for making HTTP requests.
 * Supports GET, POST, PUT, and DELETE methods with configurable options
 * and data encoding (JSON or URL-encoded).
 * 
 * This class provides a simple interface for HTTP operations with support
 * for custom encoders, write functions, and cURL options.
 * 
 * @package mc
 * @author Mihail Croitor <mcroitor@gmail.com>
 * @version 1.0.0
 * @since 1.0.0
 * 
 * @example
 * ```php
 * $http = new http('https://api.example.com/data');
 * $http->set_encoder('json_encode');
 * $response = $http->post(['key' => 'value']);
 * ```
 */
class http
{
    /**
     * Available data encoders for request bodies
     * 
     * @var string[]
     */
    private const ENCODERS = [
        "http_build_query",
        "json_encode"
    ];

    /**
     * Target URL for HTTP requests
     * 
     * @var string
     */
    private string $url;
    
    /**
     * cURL options array
     * 
     * @var array
     */
    private array $options = [];
    
    /**
     * Current data encoder function name
     * 
     * @var string
     */
    private string $encoder = "http_build_query";

    /**
     * Initialize the HTTP client
     * 
     * @param string $url The target URL for requests
     * @param array $options Optional cURL options to set initially
     */
    public function __construct(string $url, array $options = [])
    {
        $this->url = $url;
        $this->set_options($options);
    }

    /**
     * Execute a cURL request with given options
     * 
     * This is a low-level method that handles the actual cURL execution.
     * It creates a cURL handle, applies options, executes the request,
     * and handles errors.
     * 
     * @param array $options cURL options array
     * 
     * @return mixed The result of curl_exec()
     * 
     * @throws \RuntimeException When cURL execution fails
     */
    private static function request(array $options)
    {

        $ch = curl_init();
        curl_setopt_array($ch, $options);
        if (!$result = curl_exec($ch)) {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);
        return $result;
    }

    /**
     * Set a single cURL option
     * 
     * @param int $key cURL option constant (e.g., CURLOPT_TIMEOUT)
     * @param mixed $value Option value
     * 
     * @return void
     */
    public function set_option($key, $value)
    {
        $this->options[$key] = $value;
    }

    /**
     * Set the data encoder for request bodies
     * 
     * The encoder determines how data arrays are converted to strings
     * for transmission. Supported encoders are 'http_build_query' for
     * URL-encoded data and 'json_encode' for JSON data.
     * 
     * @param callable $encoder Encoder function name
     * 
     * @return void
     * 
     * @throws \InvalidArgumentException When encoder is not supported
     */
    public function set_encoder(callable $encoder) {
        if(array_search($encoder, self::ENCODERS) !== false){
            $this->encoder = $encoder;
        }
    }

    /**
     * Set a custom write function for handling response data
     * 
     * This allows custom processing of response data as it's received,
     * useful for streaming responses or custom data handling.
     * 
     * @param callable $write_function Function to handle response data
     *                                Signature: function($curl, $data): int
     * 
     * @return void
     */
    public function set_write_function(callable $write_function): void {
        $this->set_option(CURLOPT_WRITEFUNCTION, $write_function);
    }

    /**
     * Set multiple cURL options at once
     * 
     * @param array $options Associative array of cURL options
     * 
     * @return void
     */
    public function set_options(array $options)
    {
        foreach ($options as $key => $value) {
            $this->set_option($key, $value);
        }
    }

    /**
     * Perform an HTTP GET request
     * 
     * @param array $data Query parameters to append to URL
     * @param array $options Additional cURL options for this request
     * 
     * @return mixed Response from the server
     * 
     * @throws \RuntimeException When the request fails
     */
    public function get(array $data = [], array $options = [])
    {
        $q = strpos($this->url, '?') === false ? '?' : '';
        $this->set_options($options);
        $this->set_options([
            CURLOPT_HTTPGET => true,
            CURLOPT_URL => $this->url . $q . http_build_query($data)
        ]);
        return self::request($this->options);
    }

    /**
     * Perform an HTTP POST request
     * 
     * @param array $data Data to send in the request body
     * @param array $options Additional cURL options for this request
     * 
     * @return mixed Response from the server
     * 
     * @throws \RuntimeException When the request fails
     */
    public function post(array $data = [], array $options = [])
    {
        $this->set_options($options);
        $this->set_options([
            CURLOPT_POST => true,
            CURLOPT_URL => $this->url,
            CURLOPT_POSTFIELDS => ($this->encoder)($data)
        ]);
        return self::request($this->options);
    }

    /**
     * Perform an HTTP PUT request
     * 
     * @param array $data Data to send in the request body
     * @param array $options Additional cURL options for this request
     * 
     * @return mixed Response from the server
     * 
     * @throws \RuntimeException When the request fails
     */
    public function put(array $data = [], array $options = [])
    {
        $this->set_options($options);
        $this->set_options([
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_URL => $this->url,
            CURLOPT_POSTFIELDS => ($this->encoder)($data)
        ]);
        return self::request($this->options);
    }

    /**
     * Perform an HTTP DELETE request
     * 
     * @param array $data Query parameters to append to URL
     * @param array $options Additional cURL options for this request
     * 
     * @return mixed Response from the server
     * 
     * @throws \RuntimeException When the request fails
     */
    public function delete(array $data = [], array $options = [])
    {
        $q = strpos($this->url, '?') === false ? '?' : '';
        $this->set_options($options);
        $this->set_options([
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_URL => $this->url . $q . http_build_query($data),
            CURLOPT_POSTFIELDS => ''
        ]);
        return self::request($this->options);
    }
}
