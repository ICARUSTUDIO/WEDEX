<?php
// This is the official client library for the Tinify API.
// For the latest version, please visit: https://github.com/tinify/tinify-php

namespace Tinify;

class Tinify {
    private static $key = null;
    private static $proxy = null;
    private static $appIdentifier = null;
    private static $client = null;

    const VERSION = "1.6.0";

    public static function setKey($key) {
        self::$key = $key;
        self::$client = null;
    }

    public static function setProxy($proxy) {
        self::$proxy = $proxy;
        self::$client = null;
    }

    public static function setAppIdentifier($appIdentifier) {
        self::$appIdentifier = $appIdentifier;
        self::$client = null;
    }

    public static function getKey() {
        return self::$key;
    }

    public static function getProxy() {
        return self::$proxy;
    }

    public static function getAppIdentifier() {
        return self::$appIdentifier;
    }

    public static function getClient() {
        if (!self::$key) {
            throw new AccountException("Provide an API key with Tinify::setKey(...)");
        }

        if (!self::$client) {
            self::$client = new Client(self::$key, self::$appIdentifier, self::$proxy);
        }
        return self::$client;
    }

    public static function setClient($client) {
        self::$client = $client;
    }

    public static function fromFile($path) {
        return Source::fromFile($path);
    }

    public static function fromBuffer($buffer) {
        return Source::fromBuffer($buffer);
    }

    public static function fromUrl($url) {
        return Source::fromUrl($url);
    }

    public static function validate() {
        try {
            self::getClient()->request("post", "/shrink");
        } catch (AccountException $err) {
            if ($err->status == 429) return true;
            throw $err;
        } catch (ClientException $err) {
            return true;
        }
    }

    public static function compressionCount() {
        return self::getClient()->getCompressionCount();
    }
}

class Source {
    private $url, $commands;

    public static function fromFile($path) {
        $response = Tinify::getClient()->request("post", "/shrink", file_get_contents($path));
        return new self($response->headers["Location"]);
    }

    public static function fromBuffer($buffer) {
        $response = Tinify::getClient()->request("post", "/shrink", $buffer);
        return new self($response->headers["Location"]);
    }

    public static function fromUrl($url) {
        $body = array("source" => array("url" => $url));
        $response = Tinify::getClient()->request("post", "/shrink", $body);
        return new self($response->headers["Location"]);
    }

    public function __construct($url, $commands = array()) {
        $this->url = $url;
        $this->commands = $commands;
    }

    public function preserve() {
        $commands = func_get_args();
        $options = array_merge($this->commands, array("preserve" => $commands));
        return new self($this->url, $options);
    }

    public function resize($options) {
        $options = array_merge($this->commands, array("resize" => $options));
        return new self($this->url, $options);
    }

    public function store($options) {
        $response = Tinify::getClient()->request("post", $this->url, array_merge($this->commands, array("store" => $options)));
        return new Result($response->headers, $response->body);
    }

    public function toFile($path) {
        $response = Tinify::getClient()->request("get", $this->url, $this->commands);
        return file_put_contents($path, $response->body);
    }

    public function toBuffer() {
        $response = Tinify::getClient()->request("get", $this->url, $this->commands);
        return $response->body;
    }

    public function result() {
        $response = Tinify::getClient()->request("get", $this->url, $this->commands);
        return new Result($response->headers, $response->body);
    }
}

class Result extends ResultMeta {
    public function __construct($headers, $body) {
        parent::__construct($headers);
        $this->body = $body;
    }

    public function toFile($path) {
        return file_put_contents($path, $this->body);
    }

    public function toBuffer() {
        return $this->body;
    }

    public function size() {
        return $this->contentLength();
    }

    public function mediaType() {
        return $this->contentType();
    }
}

class ResultMeta {
    protected $meta;

    public function __construct($meta) {
        $this->meta = $meta;
    }

    public function width() {
        return isset($this->meta["Image-Width"]) ? intval($this->meta["Image-Width"]) : null;
    }

    public function height() {
        return isset($this->meta["Image-Height"]) ? intval($this->meta["Image-Height"]) : null;
    }

    public function location() {
        return isset($this->meta["Location"]) ? $this->meta["Location"] : null;
    }

    protected function contentLength() {
        return isset($this->meta["Content-Length"]) ? intval($this->meta["Content-Length"]) : null;
    }

    protected function contentType() {
        return isset($this->meta["Content-Type"]) ? $this->meta["Content-Type"] : null;
    }
}

class Client {
    const API_ENDPOINT = "https://api.tinify.com";

    private $options;

    public function __construct($key, $appIdentifier = null, $proxy = null) {
        $this->options = array(
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_USERPWD => "api:" . $key,
            CURLOPT_CAINFO => __DIR__ . "/cacert.pem",
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_TIMEOUT => 60,
        );

        if ($proxy) {
            $this->options[CURLOPT_PROXY] = $proxy;
        }

        $this->options[CURLOPT_USERAGENT] =
            (isset($appIdentifier) ? $appIdentifier . " " : "") .
            "Tinify/" . Tinify::VERSION . " PHP/" . PHP_VERSION;
    }

    public function request($method, $url, $body = null) {
        $header = array();

        if (is_array($body)) {
            $body = json_encode($body);
            array_push($header, "Content-Type: application/json");
        }

        $url = (substr($url, 0, 8) == "https://" || substr($url, 0, 7) == "http://") ? $url : self::API_ENDPOINT . $url;
        $request = curl_init($url);

        if ($method == "post") {
            $this->options[CURLOPT_POST] = true;
            $this->options[CURLOPT_POSTFIELDS] = $body;
        } else {
            $this->options[CURLOPT_POST] = false;
            $this->options[CURLOPT_POSTFIELDS] = null;
        }

        if (is_array($body) || empty($body)) {
            /* Add content-length header to avoid chunked encoding, which is not supported by all servers. */
            array_push($header, "Content-Length: " . strlen($body));
        }

        $this->options[CURLOPT_HTTPHEADER] = $header;
        curl_setopt_array($request, $this->options);

        $response = curl_exec($request);

        if ($response === false) {
            $message = curl_error($request);
            curl_close($request);
            throw new ConnectionException("Error while connecting: " . $message);
        }

        $headerSize = curl_getinfo($request, CURLINFO_HEADER_SIZE);
        $status = curl_getinfo($request, CURLINFO_HTTP_CODE);

        $headers = self::parseHeaders(substr($response, 0, $headerSize));
        $body = substr($response, $headerSize);

        $this->setCompressionCount(isset($headers["Compression-Count"]) ? $headers["Compression-Count"] : null);

        curl_close($request);

        if ($status >= 200 && $status < 300) {
            return (object) array("headers" => $headers, "body" => $body);
        } else {
            $details = json_decode($body);
            $message = "Unknown error";
            if (isset($details->message) && isset($details->error)) {
                $message = $details->message . " (HTTP " . $status . "/" . $details->error . ")";
            }
            if ($status == 401 || $status == 429) {
                throw new AccountException($message, null, $status);
            }
            if ($status >= 400 && $status < 500) {
                throw new ClientException($message, null, $status);
            }
            if ($status >= 500 && $status < 600) {
                throw new ServerException($message, null, $status);
            }
            throw new Exception($message, null, $status);
        }
    }

    private static function parseHeaders($headers) {
        if (!is_array($headers)) {
            $headers = explode("\r\n", $headers);
        }
        $res = array();
        foreach ($headers as $header) {
            if (empty($header)) continue;
            $parts = explode(":", $header, 2);
            if (count($parts) == 2) {
                $res[trim($parts[0])] = trim($parts[1]);
            }
        }
        return $res;
    }

    private $compressionCount;

    protected function setCompressionCount($count) {
        $this->compressionCount = $count ? intval($count) : null;
    }

    public function getCompressionCount() {
        return $this->compressionCount;
    }
}

class Exception extends \Exception {
    public $status;
    public function __construct($message, $type = null, $status = null) {
        $this->status = $status;
        parent::__construct($message);
    }
}

class AccountException extends Exception {}
class ClientException extends Exception {}
class ServerException extends Exception {}
class ConnectionException extends Exception {}
