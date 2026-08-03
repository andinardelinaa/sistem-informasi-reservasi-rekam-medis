{{-- layouts/partials/swal-helper.blade.php --}}
<script>

window.PasienSwal = {

    success(title, message = '', timer = 3000) {
        Swal.fire({
            width: 360,
            timer: timer,
            showConfirmButton: true,
            confirmButtonText: 'Oke',
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-xl p-5',
                confirmButton: 'px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-md font-medium'
            },
            html: `
                <div class="text-center">
                    <div style="width:55px;height:55px;border-radius:50%;background:linear-gradient(135deg,#dcfce7,#bbf7d0);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="fas fa-check" style="font-size:20px;color:#16a34a;"></i>
                    </div>
                    <h2 style="font-size:17px;font-weight:600;color:#1f2937;margin-bottom:4px;">${title}</h2>
                    ${message ? `<p style="font-size:13px;color:#6b7280;">${message}</p>` : ''}
                </div>
            `
        });
    },

    error(title, message = '') {
        Swal.fire({
            width: 360,
            showConfirmButton: true,
            confirmButtonText: 'Mengerti',
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-xl p-5',
                confirmButton: 'px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-md font-medium'
            },
            html: `
                <div class="text-center">
                    <div style="width:55px;height:55px;border-radius:50%;background:linear-gradient(135deg,#fef3c7,#fde68a);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="fas fa-exclamation-triangle" style="font-size:22px;color:#d97706;"></i>
                    </div>
                    <h2 style="font-size:17px;font-weight:600;color:#1f2937;margin-bottom:4px;">${title}</h2>
                    ${message ? `<p style="font-size:13px;color:#6b7280;">${message}</p>` : ''}
                </div>
            `
        });
    },

    confirmCancel(message, callback) {
        Swal.fire({
            width: 360,
            html: `
                <div class="text-center">
                    <div style="width:55px;height:55px;border-radius:50%;background:linear-gradient(135deg,#fee2e2,#fecaca);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="fas fa-trash" style="font-size:22px;color:#dc2626;"></i>
                    </div>
                    <h2 style="font-size:17px;font-weight:600;color:#1f2937;margin-bottom:4px;">Batalkan Reservasi</h2>
                    <p style="font-size:13px;color:#6b7280;">${message}</p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan',
            cancelButtonText: 'Kembali',
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-xl p-5',
                confirmButton: 'px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm rounded-md font-medium',
                cancelButton: 'px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 text-sm rounded-md font-medium'
            }
        }).then((result) => {
            if (result.isConfirmed && callback) callback();
        });
    },

    warning(title, message, confirmText = 'Mengerti') {
        Swal.fire({
            width: 360,
            html: `
                <div class="text-center">
                    <div style="width:55px;height:55px;border-radius:50%;background:linear-gradient(135deg,#fef3c7,#fde68a);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="fas fa-lock" style="font-size:22px;color:#d97706;"></i>
                    </div>
                    <h2 style="font-size:17px;font-weight:600;color:#1f2937;margin-bottom:4px;">${title}</h2>
                    <p style="font-size:13px;color:#6b7280;">${message}</p>
                </div>
            `,
            showConfirmButton: false,
            showCancelButton: true,
            cancelButtonText: confirmText,
            buttonsStyling: false,
            customClass: {
                popup: 'rounded-xl p-5',
                cancelButton: 'px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm rounded-md font-medium'
            }
        });
    },

    // Khusus Reservasi Baru (paling kompleks)
    showNewReservationSuccess(data) {
        const noReservasi = `RS-${data.tgl_reservasi.replace(/-/g,'')}-${String(data.id_reservasi).padStart(4,'0')}`;

        Swal.fire({
            width: 360,
            showConfirmButton: true,
            confirmButtonText: 'Oke',
            confirmButtonColor: '#14b8a6',
            buttonsStyling: false,
            customClass: { popup: 'rounded-xl p-5' },
            html: `
                <div class="text-center">
                    <div style="width:55px;height:55px;border-radius:50%;background:linear-gradient(135deg,#fef3c7,#fde68a);display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                        <i class="fas fa-check-circle" style="font-size:22px;color:#d97706;"></i>
                    </div>
                    <h2 style="font-size:17px;font-weight:600;color:#1f2937;margin-bottom:4px;">Reservasi Berhasil</h2>
                    <p style="font-size:13px;color:#6b7280;margin-bottom:10px;">No: <b>${noReservasi}</b></p>
                    
                    <div style="font-size:12px;color:#374151;line-height:1.6;text-align:left;display:inline-block;">
                        <div>Tanggal: <b>${data.tanggal_formatted}</b></div>
                        <div>Jam: <b>${data.jam_formatted}</b></div>
                        <div>Pasien: <b>${data.pasien_nama}</b></div>
                        <div>Dokter: <b>${data.dokter_nama}</b></div>
                    </div>

                    <div style="margin-top:12px;background:#ecfeff;padding:10px;border-radius:10px;border:1px solid #99f6e4;">
                        <div style="font-size:10px;color:#0f766e;font-weight:700;">NOMOR ANTRIAN</div>
                        <div style="width:44px;height:44px;margin:6px auto 0;border-radius:50%;background:#14b8a6;color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:18px;">
                            ${data.antrean}
                        </div>
                    </div>
                </div>
            `
        });
    }
};
</script>