<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\User;
use App\Mail\VerifikasiEmailMail;
use App\Mail\ResetPasswordMail;
use App\Mail\PasswordBerhasilDiubahMail;

class AuthController extends Controller
{
    // Tampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {   //validasi input
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);
        // mengambil email dan password
        $credentials = $request->only('email', 'password');
        //mencari email dan pw di database, jika cocok :
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            //regenerate session
            $request->session()->regenerate();

            $user = Auth::user();

            if (!$user->hasVerifiedEmail()) { //jika belum verif
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Silakan verifikasi email terlebih dahulu.'
                ]);
            }

            if ($user->roles()->count() === 0) { // cek apakah sudah punya role
                Auth::logout();

                return back()->withErrors([
                    'email' => 'Akun ini belum memiliki role. Silakan hubungi admin.'
                ]);
            }
            // ambil role userm default pasien
            $role = $user->getRoleNames()->first() ?? 'pasien';
            //mengarahkan pengguna ke dasshboard sesuai peran
            return match (strtolower($role)) {
                'admin'   => redirect()->route('admin.dashboard'),
                'dokter'  => redirect()->route('dokter.dashboard'),
                'pasien'  => redirect()->route('home'),
                default   => redirect()->route('home'),
            };
        }
        //jka email atau pw salah
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Email atau password salah.']);
    }

    // Tampilkan halaman register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses register 
    public function register(Request $request)
    {   //validasi 
        $validated = $request->validate([
            'nama'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        // menyimpan ke table users
        $user = User::create([
            'nama'     => $request->nama,
            'email'    => $request->email,
            'password' => Hash::make($request->password), //hash pw 
        ]);

        $user->assignRole('pasien'); //role default pasien
        // kirim verifikasi email
        $this->kirimVerifikasiEmail($user);

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Cek email Anda untuk verifikasi akun sebelum login.');
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate(); //invalid session
        $request->session()->regenerateToken(); //regenerate token

        return redirect()->route('home')
            ->with('success', 'Anda berhasil logout');
    }

    // Tampilkan form reset password
    public function showReset()
    {
        return view('auth.reset-password');
    }

    // Kirim link reset password 
    public function sendResetLinkEmail(Request $request)
    {   //validasi emailnya
        $request->validate(['email' => 'required|email']);
        //cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }
        // Buat token reset password
        $token = Password::createToken($user);

        // Buat URL link reset password
        $resetUrl = URL::route('password.reset', [
            'token' => $token,
            'email' => $user->email,
        ]);

        // kirim email reset password
        Mail::to($user->email)->send(new ResetPasswordMail([
            'nama'   => $user->nama,
            'url'    => $resetUrl,
            'expiry' => '60 menit',
        ]));

        return back()->with(['status' => 'Link reset password telah dikirim ke email Anda.']);
    }

    // Tampilkan form reset password dengan token
    public function showResetForm(Request $request, $token = null)
    {
        return view('auth.reset-password-form')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }

    // Reset password
    public function reset(Request $request)
    {   //validasi
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);
        // proses reset
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60)); //menghasilkan token login baru
                //session lama logout

                $user->save();

                event(new PasswordReset($user));

                //kirim email password berubah
                Mail::to($user->email)->send(new PasswordBerhasilDiubahMail([
                    'nama'   => $user->nama,
                    'waktu'  => Carbon::now()->format('d M Y H:i'),
                    'device' => request()->userAgent() ?? 'Tidak diketahui',
                ]));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', 'Password berhasil direset! Silakan login dengan password baru.')
            : back()->withErrors(['email' => [__($status)]]);
    }

    // kirim verifikasi email
    private function kirimVerifikasiEmail(User $user)
    {
        // Buat URL link verifikasi manual
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60), //link verifikasi dibatasi 60 menit
            [   //id user di db
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()), //email di hash
            ]
        );
        //kirim email verifikasi
        Mail::to($user->email)->send(new VerifikasiEmailMail([
            'nama'   => $user->nama,
            'url'    => $verificationUrl,
            'expiry' => '60 menit',
        ]));
    }
}