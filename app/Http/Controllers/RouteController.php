<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Route;

class RouteController extends Controller
{
    public function index()
    {
        $routes = collect(Route::getRoutes())->filter(function ($route) {
            return strpos($route->getActionName(), 'App\Http\Controllers') !== false;
        })->map(function ($route) {
            return [
                'url' => $route->uri(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'method' => $route->methods()[0], // Get the first HTTP method for the route
                'middleware' => $route->middleware(),
            ];
        });

        return view('endpoints', [
            'routeData' => $routes,
        ]);
    }
}
