<?php

namespace App\Http\Middleware;

use Closure;

/**
 * Class AdminPermissionMiddleware
 * @package App\Http\Middleware
 */
class AdminPermissionMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next/*, $permision*/)
    {
//        if (!$this->admin->canUse($permision)) {
//            abort(403, 'У Вас нет прав использовать этот раздел');
//        }
        return $next($request);
    }
}
