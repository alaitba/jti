<?php

namespace App\Http\Middleware;

use Closure;

class AdminPermissionMiddleware
{
    public function handle($request, Closure $next, $permision)
    {
//        if (!$this->admin->canUse($permision)) {
//            abort(403, 'У Вас нет прав использовать этот раздел');
//        }
        return $next($request);
    }
}
