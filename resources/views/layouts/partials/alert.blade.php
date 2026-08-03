@if(session('success') || session('error'))
    <div id="toast" class="toast">
        @if(session('success'))
            <span class="toast-success-text">
                {{ session('success') }}
            </span>
        @endif

        @if(session('error'))
            <span class="toast-error-text">
                {{ session('error') }}
            </span>
        @endif
    </div>

<script>
(function () {
    const toastEl = document.getElementById('toast');

    // ❗ kalau tidak ada element, stop
    if (!toastEl) return;

    setTimeout(() => {
        toastEl.classList.add('show');
    }, 100);

    setTimeout(() => {
        toastEl.classList.remove('show');
    }, 3000);

    setTimeout(() => {
        toastEl.remove();
    }, 3500);
})();
</script>
@endif

<style>
.toast {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 14px 22px;
    border-radius: 10px;
    box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    opacity: 0;
    transform: translateY(-20px);
    transition: all 0.4s ease;
    z-index: 9999;
    font-size: 14px;
    color: white;
}

/* warna beda sesuai kondisi */
.toast-success-text {
    color: #fff;
}

.toast-error-text {
    color: #fff;
}

/* background beda */
.toast:has(.toast-success-text) {
    background-color: #2aaee7;
}

.toast:has(.toast-error-text) {
    background-color: #dc3545;
}

.toast.show {
    opacity: 1;
    transform: translateY(0);
}
</style>