<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStaff
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        // Admin không được vào staff routes - redirect ngay lập tức
        if ($user && $user->isAdmin()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Bạn không có quyền truy cập trang này.',
                ], 403);
            }
            return redirect()->route('admin.dashboard')
                ->with('error', 'Bạn không có quyền truy cập trang này.');
        }

        // Kiểm tra user có phải staff không
        if (!$user || !$user->isStaff()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Unauthorized. Staff access required.',
                ], 403);
            }
            return redirect()->route('login')
                ->with('error', 'Bạn cần đăng nhập với tài khoản nhân viên.');
        }

        return $next($request);
    }
}
