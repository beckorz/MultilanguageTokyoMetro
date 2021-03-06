<?php
namespace Model;

abstract class ModelBase
{
    protected $db;
    protected $app;
    public function __construct($app, $db)
    {
        $this->app = $app;
        $this->db = $db;
    }

    public function __destruct()
    {
        $this->app = null;
        $this->db = null;
    }
    abstract public function setup();
}
