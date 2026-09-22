<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->appendToGroup('web', \App\Http\Middleware\PreventBackHistory::class);
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'logout',
            '/logout',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (\Spatie\Permission\Exceptions\UnauthorizedException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'You do not have permission to access this.'], 403);
            }

            $fallback = $request->user()?->role === 'partner' ? 'partner.dashboard' : 'admin.dashboard';

            return redirect()->route($fallback)->with('error', 'Your role does not have permission to access that page.');
        });

        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            if ($request->is('logout') || $request->routeIs('logout')) {
                return redirect('/');
            }
            
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Session expired. Please reload.'], 419);
            }

            return redirect()->route('login')->with('info', 'Your session has expired. Please log in again to continue.');
        });

        $exceptions->render(function (\Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException $e, \Illuminate\Http\Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Method not allowed.'], 405);
            }

            $previousUrl = url()->previous();
            $currentUrl = $request->fullUrl();

            // If the previous URL is the same as the current URL, redirect to dashboard or home to prevent loop
            if ($previousUrl === $currentUrl || url($previousUrl) === url($currentUrl)) {
                $fallback = str_contains($currentUrl, '/admin') ? '/admin' : (str_contains($currentUrl, '/partner') ? '/partner' : '/');
                return redirect()->to($fallback)->with('info', 'The requested action is not valid.');
            }

            return redirect()->back()->with('info', 'The requested action is not valid. Please try again.');
        });
    })->create();
