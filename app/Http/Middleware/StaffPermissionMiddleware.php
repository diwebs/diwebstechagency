<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StaffPermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $staff = auth()->guard('staff')->user();
        if (!$staff) {
            return redirect()->route('staff.login');
        }

        if ($staff->status !== 'Active') {
            auth()->guard('staff')->logout();
            return redirect()->route('staff.login')->with('error', 'Your account has been suspended or resigned.');
        }

        $role = $staff->role;
        if (!$role) {
            abort(403, 'Unauthorized. You do not have an assigned role.');
        }

        $permissions = is_array($role->permissions)
            ? $role->permissions
            : json_decode($role->permissions ?? '[]', true);

        // If they have super_admin_access, allow everything
        if (in_array('super_admin_access', $permissions)) {
            return $next($request);
        }

        if (in_array($permission, $permissions)) {
            return $next($request);
        }

        abort(403, 'Unauthorized. You do not have permission to access this module: ' . $permission);
    }
}
