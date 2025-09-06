# RouteHandler

A simple helper class to manage routes in [ThunderPHP](https://github.com/prateekbhujel/ThunderPHP).

RouteHandler is designed specifically for use in [ThunderPHP](https://github.com/prateekbhujel/ThunderPHP).
RouteHandler helps you manage routes without having to parse them manually. Maps route names to routes, and routes to controllers and views, automatically matches routes and controllers, routes and views, validates routes, and handles route prefixes (very useful in views).
It's important to clarify that this isn't a router, but rather a route manager. Loading controllers and views is done in the corresponding action functions.
With RouteHandler the code of that functions is much cleaner and clearer, making it easier to write and read.

## Example of use in a user administration plugin

```php
$routeHandler = new Helpers\RouteHandler('admin/users');

// ['route-name' => 'route']
$routeHandler->routeNames = [
    'view' => 'view',
    'add' => 'add',
    'edit' => 'edit',
    'delete' => 'delete'
];

/** route requests to the proper controller */
addAction('controller', function () use ($routeHandler, $dataBag)
{
    if (! $routeHandler->isValidRoute()) return; // The App file handles the 404 error.

    // If no controller is specified, register with the {route}-controller.php pattern.
    $routeHandler->addController('view', 'get');
    $routeHandler->addController('add', 'post');
    $routeHandler->addController('edit', 'get');
    $routeHandler->addController('edit', 'post', 'update-controller.php');
    $routeHandler->addController('delete', ['get', 'post]);

    $controller = $routeHandler->matchController();
    $controller = $controller ? $controller : 'list-controller.php';
    $controllerPath = pluginPath('controllers' . DS . $controller);

    if (! file_exists($controllerPath)) {
        dd('Create controller: ' . $controllerPath);
    }
    require $controllerPath;
});

/** add main content (user-manager views) to admin area (basic-admin view) */
addAction('basic-admin_main_content', function () use ($routeHandler, $dataBag)
{ 
    if (! $routeHandler->isValidRoute()) return; // The App file handles the 404 error.

    // If no view is specified, register with the {view}-controller.php pattern.
    $routeHandler->addView('view');
    $routeHandler->addView('add');
    $routeHandler->addView('edit');
    $routeHandler->addView('delete');

    $view = $routeHandler->matchView();

    $view = $view ? $view : 'list.php';

    $data = [
        'users' => $dataBag->get('users') ?? [],
        'user' => $dataBag->get('user') ?? null,
        'errors' => $dataBag->get('errors') ?? [],
    ];
    
    extract($data);
    
    $viewPath = pluginPath('views' . DS . $view);
    
    if (! file_exists($viewPath)) {
        dd('Create de view: ' . getLastPathSections($viewPath));
    }
    
    require $viewPath;
});
```
