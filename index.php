<?php
// Import the required classes
use core\database\types\DBTypes;
use core\database\types\Table;
use core\http\Next;
use core\http\Request;
use core\http\Response;

require_once './core/Router.php';
require_once './core/database/Database.php';
require_once './core/database/types/DBTypes.php';
require_once './core/database/types/Table.php';

// Set the views folder
Router::setViewsFolder('./views');
Router::setErrorViews('./views/Errors');
Router::setComponentRenderDepth(1);

function logEcho(Request $request, Next $next): Next {
    $username = strtoupper((string) $request->getParameter("username"));
    $next->setModifiedData(['username' => $username]);
    return $next;
}



Router::use('logEcho');

// Define some routes
Router::get('/', function(Request $req, Response $res) {

    Response::render('home');
});

Router::get('/db', function(Request $req, Response $res) {

    $database = new \core\database\Database('localhost', 'root', '', 'framework_test');

    $tableExists = $database->tableExists('users');

    if($tableExists){
        echo "Table exists";

        $users = $database->table('users')->where('username', 'lars')->get();
        foreach ($users as $user){
            print_r($user);
        }

    }else {
        echo "No table";


        // Create a Table object for 'users' table
        $table = new Table('users', [
            'id' => [DBTypes::INT, DBTypes::PRIMARY_KEY, DBTypes::AUTOINCREMENT],
            'username' => [DBTypes::VARCHAR(255), DBTypes::NOT_NULL],
            'created_at' => [DBTypes::DATETIME],
        ]);
/*
// Add columns to the 'users' table
        $table->addColumn('id', [DBTypes::INT, DBTypes::PRIMARY_KEY, DBTypes::AUTOINCREMENT])
            ->addColumn('username', [DBTypes::VARCHAR(50), DBTypes::NOT_NULL])
            ->addColumn('email', [DBTypes::VARCHAR(100), DBTypes::NOT_NULL, DBTypes::UNIQUE]);
*/

        $database->create($table);

    }
});

Router::post('/db/add/:username', function(Request $req, Response $res) {
    $database = new \core\database\Database('localhost', 'root', '', 'framework_test');

    $userData = [
        'username' => $req->getParameter('username'),
        'created_at' => date('Y-m-d H:i:s') // Assuming MySQL DATETIME format
    ];

    $database->table('users')->insert($userData);
    Response::setStatusCode(201);
});

Router::get('/:username', function(Request $req, Response $res) {

    $name = $req->getParameter("username");

    $users = ['admin', 'pepa', 'bogo'];
    $users3 = [['admin', [0,"a"]], ['pepa', [1, "b"]], ['bogo', [2, "c"]], ['Borg', [3, "d"]]];
    $nestedArray = [
        ['Alice', ['apple', 'orange']],
        ['Bob', ['banana', 'grapes']],
        ['Charlie', ['kiwis', 'melon']]
    ];


    $view = new \core\views\View('profile');
    $view->add('username', $name);
    $view->add('username', $name);
    $view->add('page', 1);
    $view->add('users', $users);
    $view->add('users3', $users3);
    $view->add('nestedArray', $nestedArray);
    Response::render($view);
} );



Router::get('/debug/routes', function(Request $req, Response $res) {
    echo "<pre>";
    print_r(Router::getRoutes());
    echo "</pre>";
});

Router::group(['prefix' => '/admin'], function () {
    Router::get('/dashboard', function(Request $req, Response $res) {
        echo "Admin dashboard";
    });

    Router::get('/users/:id', function(Request $req, Response $res) {
        echo "User ID: " . $req->getParameter('id');
    });
});


Router::group(['prefix' => '/admin', 'middleware' => function(Request $req, Next $next) {
    if ($req->getParameter('role') !== 'admin') {
        die('Access denied');
    }
    return $next;
}], function () {
    Router::get('/dashboard', function(Request $req, Response $res) {
        echo "Admin dashboard";
    });

    Router::get('/users/:id', function(Request $req, Response $res) {
        echo "User ID: " . $req->getParameter('id');
    });
});


// Resolve route and send response
Router::resolve();

