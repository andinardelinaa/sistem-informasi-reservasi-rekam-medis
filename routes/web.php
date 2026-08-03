<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\ReservasiController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\KlinikController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\COntrollers\Admin\RekamMedisAdminController;
use App\Http\Controllers\Pasien\PasienReservasiController;
use App\Http\Controllers\Pasien\ProfileController;
use App\Http\Controllers\Dokter\AntreanController;
use App\Http\Controllers\Dokter\JadwalDokterController;
use App\Http\Controllers\Dokter\RekamMedisController;
use App\Http\Controllers\Dokter\ProfilDokterController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Dokter\DashboardDokterController;
use App\Http\Controllers\Pasien\PasienRekamMedisController;
use App\Http\Controllers\Admin\HariLiburController;
use App\Http\Controllers\Admin\PerubahanJadwalDokterController;
use App\Http\controllers\NotifikasiController;
use App\Models\User;

// langsung diarahkan ke dashboard pasien
Route::get('/', fn () => redirect()->route('pasien.dashboard'))->name('home');
// menampilkan halaman dashboard pasien, tapi kalau belum login diarahkan ke login
Route::prefix('pasien')->name('pasien.')->group(function () {
    Route::get('/dashboard', function () {
        return view('pasien.dashboard');
    })->name('dashboard');
});
// yang dapat diakses pengguna yg belum login
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.proses');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.proses');
    Route::get('/password/reset', [AuthController::class, 'showReset'])->name('password.request');
    Route::post('/password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/password/reset/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [AuthController::class, 'reset'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    // verifikasi email
    Route::get('/email/verify', fn () => view('auth.verify-email'))->name('verification.notice');
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('success', 'Link verifikasi baru telah dikirim!');
    })->middleware(['throttle:6,1'])->name('verification.send');

    // notifikasi
    Route::middleware('auth')->group(function () {
    Route::get('/notifikasi/unread', [NotifikasiController::class, 'unread'])->name('notifikasi.unread');
    Route::post('/notifikasi/read-all', [NotifikasiController::class, 'markAllRead'])->name('notifikasi.read-all');
    Route::post('/notifikasi/read/{id}', [NotifikasiController::class, 'markRead'])->name('notifikasi.read');
    Route::delete('/notifikasi/{id}', [NotifikasiController::class, 'destroy'])->name('notifikasi.destroy');
    });

});
// route untuk verifikasi email, bisa diakses tanpa login tapi harus pakai signed URL
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {
    $user = User::findOrFail($id);
    if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
        abort(403);
    }
    if (!$user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
    }
    return redirect()->route('login', ['email' => $user->email])
        ->with('success', 'Email berhasil diverifikasi! Silakan login.');
})->middleware(['signed'])->name('verification.verify');
// route untuk logout, hanya bisa diakses jika sudah login
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// route admin
Route::middleware(['auth', 'verified', 'CekRole:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [DashboardController::class, 'getData'])->name('dashboard.data');
        
        // reservasi
        Route::post('reservasi/{id_reservasi}/kehadiran', [ReservasiController::class, 'kehadiran'])
            ->name('reservasi.kehadiran');
        Route::get('/reservasi/export-excel', [ReservasiController::class, 'exportExcel'])
            ->name('reservasi.export.excel');
        Route::get('/reservasi/export-word', [ReservasiController::class, 'exportWord'])
            ->name('reservasi.export.word');
        Route::get('/reservasi/{id}/export-pdf', [ReservasiController::class, 'exportPdf'])
            ->name('reservasi.export-pdf');
        Route::get('/reservasi/get-jadwal', [ReservasiController::class, 'getJadwal'])
            ->name('reservasi.getJadwal');
        Route::post('/reservasi/{id}/send-email', [ReservasiController::class, 'sendEmail']);
        Route::post('/reservasi/{id}/hubungi', [ReservasiController::class, 'hubungiPasien'])
            ->name('reservasi.hubungi');
        Route::post('/reservasi/{id}/send-email', [ReservasiController::class, 'sendEmail'])
            ->name('reservasi.send-email');
        Route::post('/reservasi/{id}/update-status', [ReservasiController::class, 'updateStatus'])
            ->name('reservasi.update-status');
        Route::post('/reservasi/{id}/lanjut-dokter-pengganti', [ReservasiController::class, 'lanjutDokterPengganti'])
            ->name('reservasi.lanjut-dokter-pengganti');
        Route::post('/reservasi/{id}/konfirmasi-reschedule', [ReservasiController::class, 'konfirmasiReschedule'])
            ->name('reservasi.konfirmasi-reschedule');
        Route::post('/reservasi/{id}/update-status', [ReservasiController::class, 'updateStatus'])
            ->name('reservasi.update-status');
        Route::post('/{id}/lanjut-dokter-pengganti', [ReservasiController::class, 'lanjutDokterPengganti'])
            ->name('lanjut-dokter-pengganti');
        
        // rekam medis
        Route::get('/rekam-medis/export-word', [RekamMedisAdminController::class, 'exportWord'])
            ->name('rekam-medis.export.word');
        Route::get('/rekam-medis/export-excel', [RekamMedisAdminController::class, 'exportExcel'])
            ->name('rekam-medis.export.excel');
        Route::get('/rekam-medis/{id}', [RekamMedisAdminController::class, 'show'])
            ->whereNumber('id')
            ->name('rekam-medis.show');

        // hari libur
        Route::get('hari-libur/export', [HariLiburController::class, 'export'])
            ->name('hari-libur.export');
        Route::post('hari-libur/import', [HariLiburController::class, 'import'])
            ->name('hari-libur.import');
        Route::get('hari-libur/template/download', [HariLiburController::class, 'template'])
            ->name('hari-libur.template');

        // perubahan jadwal dokter
        Route::get('perubahan-jadwal/dokter-available', [PerubahanJadwalDokterController::class, 'dokterAvailable'])
            ->name('perubahan-jadwal.dokter-available');
        Route::get('perubahan-jadwal/dokter-pengganti', [PerubahanJadwalDokterController::class, 'dokterPengganti'])
            ->name('perubahan-jadwal.dokter-pengganti');
        Route::get('/dokter-available', [PerubahanJadwalDokterController::class, 'dokterAvailable'])
            ->name('perubahan-jadwal.dokter-available');
        Route::get('/dokter-pengganti', [PerubahanJadwalDokterController::class, 'dokterPengganti'])
            ->name('perubahan-jadwal.dokter-pengganti');

        // user
        Route::get('/user/{id}/edit-password', [UserController::class, 'editPassword'])
            ->name('user.edit-password');
        Route::put('/user/{id}/update-password', [UserController::class, 'updatePassword'])
            ->name('user.update-password');
        Route::get('/user/{id}/edit-roles', [UserController::class, 'editRoles'])
            ->name('user.edit-roles');
        Route::put('/user/{id}/update-roles', [UserController::class, 'updateRoles'])
            ->name('user.update-roles');

        // jadwal
        Route::post('/jadwal/{id}/restore', [JadwalController::class, 'restore'])
            ->name('jadwal.restore');
        Route::post('/jadwal/generate', [JadwalController::class, 'generate'])
            ->name('jadwal.generate');
        Route::get('/jadwal/templates', [JadwalController::class, 'templates'])
            ->name('jadwal.templates');

        // verifikasi email
        Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
            $request->fulfill();
            return redirect('/')->with('success', 'Email berhasil diverifikasi!');
        })->middleware(['auth', 'signed'])->name('verification.verify');

        Route::post('/email/verification-notification', function (Request $request) {
            $request->user()->sendEmailVerificationNotification();
            return back()->with('message', 'Link verifikasi telah dikirim ulang!');
        })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

        // resource controller
        Route::resource('klinik', KlinikController::class);
        Route::resource('dokter', DokterController::class);
        Route::resource('jadwal', JadwalController::class);
        Route::resource('users', UserController::class);
        Route::resource('pasien', PasienController::class);
        Route::resource('reservasi', ReservasiController::class);
        Route::resource('rekam-medis', RekamMedisAdminController::class);
        Route::resource('hari-libur', HariLiburController::class);
        Route::resource('perubahan-jadwal', PerubahanJadwalDokterController::class);
});
    

Route::middleware(['auth', 'verified', 'CekRole:dokter'])
    ->prefix('dokter')
    ->name('dokter.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardDokterController::class, 'dashboard'])
        ->name('dashboard');

        // Jadwal Praktik
        Route::get('/jadwal', [JadwalDokterController::class, 'index'])->name('jadwal');
        Route::get('/jadwal/jadwal-saya', [JadwalDokterController::class, 'mySchedule'])->name('jadwal.saya');
        Route::get('/jadwal/semua-jadwal', [JadwalDokterController::class, 'allSchedules'])->name('jadwal.semua');

        // Profil
        Route::get('/profil', [ProfilDokterController::class, 'index'])
        ->name('profil');
        Route::post('/profil/update', [ProfilDokterController::class, 'update'])
        ->name('profil.update');

        // Antrean Pasien (satu route saja, tidak duplikat)
        Route::get('/antrean', [AntreanController::class, 'index'])->name('antrean');
        Route::post('/antrean/{id}/selesai', [AntreanController::class, 'selesai'])->name('antrean.selesai');
        Route::get('/antrean/{id}/rekam-medis', [AntreanController::class, 'rekamMedis'])->name('antrean.rekam-medis');
        Route::post('/selesai/{id}', [AntreanController::class, 'selesai'])->name('dokter.selesai');
        Route::post('/selesai/{id_reservasi}', [ReservasiController::class, 'selesai'])
        ->name('dokter.selesai');

        // Rekam Medis  
        Route::get('/rekam-medis', [RekamMedisController::class, 'index'])->name('rekam-medis.index');
        Route::get('/create-rekam-medis/{id_reservasi}', [RekamMedisController::class, 'create'])->name('rekam-medis.create');
        Route::post('/store-rekam-medis', [RekamMedisController::class, 'store'])->name('rekam-medis.store');
        Route::get('/rekam-medis/{id_rm}', [RekamMedisController::class, 'show'])->name('rekam-medis.show');
        Route::get('/rekam-medis/riwayat/{id_pasien}', [RekamMedisController::class, 'riwayatPasien'])->name('rekam-medis.riwayat');
        Route::post('/rekam-medis/finalize/{id_reservasi}', [RekamMedisController::class, 'finalize'])->name('dokter.rekam-medis.finalize');
        Route::resource('/rekam-medis', RekamMedisController::class)
            ->except(['index', 'create', 'store', 'show']);

        Route::get('/spesialisasi', [DokterController::class, 'getSpesialisasi'])
        ->name('spesialisasi');
    });

Route::middleware(['auth', 'verified', 'CekRole:pasien'])
    ->prefix('pasien')
    ->name('pasien.')
    ->group(function () {
        // reservasi
        Route::get('/reservasi', [PasienReservasiController::class, 'index'])->name('reservasi');
        Route::get('/pilih-klinik', [PasienReservasiController::class, 'pilihJadwal'])->name('pilih.klinik');
        Route::get('/jadwal/available', [PasienReservasiController::class, 'jadwalAvailable'])
        ->name('jadwal.available');
        Route::get('/form-reservasi/{id}', [PasienReservasiController::class, 'formReservasi'])->name('reservasi.form-reservasi');
        Route::post('/form-reservasi/{id}/store', [PasienReservasiController::class, 'store'])->name('reservasi.store-redirect');
        Route::get('/reservasi/create', [PasienReservasiController::class, 'create'])->name('reservasi.create');
        Route::post('/reservasi', [PasienReservasiController::class, 'store'])->name('reservasi.store');
        Route::delete('/reservasi/{id}', [PasienReservasiController::class, 'destroy'])->name('reservasi.destroy');
        Route::get('/riwayat', [PasienReservasiController::class, 'riwayat'])->name('riwayat');
        Route::post('/form', [PasienReservasiController::class, 'store'])
        ->name('form.store');
        Route::get('/form', [PasienReservasiController::class, 'edit'])->name('form.edit');
        Route::post('/form', [PasienReservasiController::class, 'update'])->name('form.update');
        Route::get('/form-reservasi', [PasienReservasiController::class, 'editReservasi'])->name('form.reservasi');
        Route::post('/form-reservasi', [PasienReservasiController::class, 'updateReservasi'])->name('form.reservasi.update');   

        // profil
        Route::get('/profil', fn () => view('pasien.profil'))->name('profil');
        Route::get('/form', [ProfileController::class, 'create'])->name('form.create');
        Route::post('/form', [ProfileController::class, 'store'])->name('form.store');
        Route::get('/profil/edit', [ProfileController::class, 'edit']) ->name('profil.edit');
        Route::put('/profil/update', [ProfileController::class, 'update'])->name('profil.update');

        // rekam medis
        Route::get('rekam-medis/riwayat-rekam-medis', [PasienRekamMedisController::class, 'index'])
        ->name('rekam-medis.index');
        Route::get('rekam-medis/riwayat-rekam-medis/{id_rm}/detail', [PasienRekamMedisController::class, 'show'])
        ->name('rekam-medis.show');
        
    });
