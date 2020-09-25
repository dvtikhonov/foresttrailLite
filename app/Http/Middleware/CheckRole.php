<?php
/**
 * Created by PhpStorm.
 * User: ddd
 * Date: 04.12.2019
 * Time: 12:03
 */

namespace App\Http\Middleware;

use Closure;

class CheckRole
{
    /**
     *  Проверка ролей
     *
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Closure $next
     * @param  string $role
     * @return mixed
     */
    public function handle($request, Closure $next, $role = null)
    {
        $roles = explode('|', $role);
        if (! in_array($request->user()->role,$roles )) {
            // Redirect...

        return  redirect()->route('home');
        }

        return $next($request);
    }

}