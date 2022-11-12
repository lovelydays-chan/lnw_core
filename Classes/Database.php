<?php

namespace Lnw\Core;

use Lnw\Core\Config;
use Exception;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Events\Dispatcher;
use Illuminate\Container\Container;

class Database
{
    public function __construct()
    {
        try {
            $default = Config::get("database.default");
            $connections = Config::get("database.connections");
            if (isset($connections[$default])) {
                $connections = array_merge(['default' => $connections[$default]], $connections);
            }
            $capsule = new Capsule();
            foreach ($connections as $key => $connection) {
                $capsule->addConnection([
                    'driver' => $connection['driver'],
                    'host' => $connection['host'],
                    'database' => $connection['database'],
                    'username' => $connection['username'],
                    'password' => $connection['password'],
                    'charset' => $connection['charset'],
                    'collation' => $connection['collation'],
                    'prefix' => $connection['prefix']
                ], $key);
            }
            $capsule->setEventDispatcher(new Dispatcher(new Container));
            // Setup the Eloquent ORM…
            $capsule->setAsGlobal();
            $capsule->bootEloquent();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
