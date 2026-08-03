<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login'); //cek login, jika belum diarahkan ke halaman login
        }

        $user = Auth::user(); // data pengguna yg sedang login

        // pakai spatie
        $userRole = strtolower($user->getRoleNames()->first() ?? '');

        // cek role apakah diizinkan
        foreach ($roles as $role) {
            if ($userRole === strtolower($role)) {
                return $next($request);
            }
        }
        // mengarahkan ketika user sudah berhasil login
        return match ($userRole) {
            'admin'  => redirect()->route('admin.dashboard'),
            'dokter' => redirect()->route('dokter.dashboard'),
            'pasien' => redirect()->route('home'),
            default  => abort(403, 'Role tidak valid: ' . $userRole),
        };
    }
}
