<?php

namespace mc;

class http
{
    private const ENCODERS = [
        "http_build_query",
        "json_encode"
    ];

    private string $url;
    private array $options = [];
    private string $encoder = "http_build_query";

    public function __construct(string $url, array $options = [])
    {
        $this->url = $url;
        $this->set_options($options);
    }

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

    public function set_option($key, $value)
    {
        $this->options[$key] = $value;
    }

    public function set_encoder(callable $encoder) {
        if(array_search($encoder, self::ENCODERS) !== false){
            $this->encoder = $encoder;
        }
    }

    public function set_write_function(callable $write_function): void {
        $this->set_option(CURLOPT_WRITEFUNCTION, $write_function);
    }

    public function set_options(array $options)
    {
        foreach ($options as $key => $value) {
            $this->set_option($key, $value);
        }
    }

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
