<?php

namespace Lnw\Core;

use Illuminate\Database\Capsule\Manager as Capsule;

class Database
{
    public function __construct()
    {
        $config = require $_SERVER['DOCUMENT_ROOT'].(!empty(env('ROOT_PATH')) ? env('ROOT_PATH') : '').'config/database.php';
        $db_config = $config['connections'][$config['default']];
        $capsule = new Capsule();
        $capsule->addConnection([
         'driver' => $db_config['driver'],
         'host' => $db_config['host'],
         'database' => $db_config['database'],
         'username' => $db_config['username'],
         'password' => $db_config['password'],
         'charset' => $db_config['charset'],
         'collation' => $db_config['collation'],
         'prefix' => $db_config['prefix'],
    ]);
        // Setup the Eloquent ORM…
        $capsule->bootEloquent();
    }
}
