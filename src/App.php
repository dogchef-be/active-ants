<?php

namespace ActiveAnts;

class App
{

    /**
     * Contains the client
     * @var Client
     */
    public $client;

    /**
     * The settings
     * @var Settings
     */
    public $settings;

    /**
     * The cache component
     * @var Cache
     */
    public $cache;

    /**
     * The session hash (user connected)
     * @var string
     */
    private $_hash;

    /**
     * Contains the app
     * @var App
     */
    private static $app;

    public $endpoint;

    /**
     * Build an api client
     * @param sring $endpoint
     * @param string $username
     * @param string $password
     * @param string $cacheDirectory
     */
    public static function start($endpoint, $username, $password, $cacheDirectory)
    {
        if (self::$app == null) {
            self::$app = new self();
            self::$app->setAuth($username, $password)->setEndpoint($endpoint)->setCacheDirectory($cacheDirectory);
            self::$app->client->authorize();
        }
        return self::$app;
    }

    /**
     * Return the ready instance
     * @param boolean $debug
     * @return App
     */
    public static function getInstance()
    {
        if (self::$app == null) {
            throw new ApiException('Not connected');
        }
        return self::$app;
    }
    /**
     * Set the authorization parameters
     * @param string $username
     * @param string $password
     * @return App
     */
    public function setAuth($username, $password)
    {
        $this->client = new Client();
        $this->client->setUsername($username);
        $this->client->setPassword($password);
        $this->_hash = sha1($username . $password);
        return $this;
    }


    /**
     * Set the authorization parameters
     * @param string $endpoint
     * @return App
     */
    public function setEndpoint($endpoint)
    {
        $this->endpoint = $endpoint;
        return $this;
    }

    /**
     * Prepare the cache component
     * @param string $cacheDirectory
     * @return \ActiveAnts\App
     */
    public function setCacheDirectory($cacheDirectory)
    {
        $this->cache = new Cache($this->_hash, $cacheDirectory);
        return $this;
    }

    /**
     * Return the active ants settings
     * @return Settings
     */
    public function getSettings()
    {
        if (is_null($this->settings)) {
            $this->settings = Settings::load();
        }
        return $this->settings;
    }
}
