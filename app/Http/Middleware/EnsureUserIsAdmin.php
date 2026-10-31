<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Kiểm tra người dùng hiện tại có quyền Admin không.
     * Middleware này được áp dụng cho tất cả các route trong nhóm /admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== 'admin') {
            abort(403, 'Bạn không có quyền truy cập khu vực Quản trị viên.');
        }

        return $next($request);
    }
}
