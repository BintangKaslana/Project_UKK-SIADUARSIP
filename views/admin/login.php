<?php
require_once dirname(__DIR__, 2) . '/app/bootstrap.php';

// Generate captcha baru setiap kali halaman login dimuat (kecuali sudah ada di session)
if (empty($_SESSION['captcha_num1']) || empty($_SESSION['captcha_num2'])) {
    $_SESSION['captcha_num1'] = rand(1, 9);
    $_SESSION['captcha_num2'] = rand(1, 9);
}
$n1 = $_SESSION['captcha_num1'];
$n2 = $_SESSION['captcha_num2'];
?>
<div class="min-h-screen bg-[#4455DD] flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-lg p-8 w-full max-w-sm">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-[#4455DD] rounded-xl mb-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5.121 17.804A9 9 0 1118.88 6.196M15 11l-3 3m0 0l-3-3m3 3V4"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-[#4455DD]">Login Admin</h1>
            <p class="text-xs text-gray-400 mt-1">Masuk ke panel administrasi</p>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <?php $errCode = $_GET['error']; ?>
            <div class="bg-[#EE6666] text-white px-4 py-2 rounded-lg mb-4 text-sm">
                <?php if ($errCode === 'captcha'): ?>
                    ⚠️ Jawaban CAPTCHA salah. Silakan coba lagi.
                <?php else: ?>
                    ❌ Username atau password salah.
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <form action="<?= BASE_PATH ?>/admin/authenticate" method="post" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Username</label>
                <input type="text" name="username" required autocomplete="username"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input type="password" name="password" id="passwordInput" required autocomplete="current-password"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-[#4455DD]">
                    <button type="button" onclick="togglePassword()"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7
                                     -1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- CAPTCHA -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Verifikasi</label>
                <div class="flex items-center gap-3 bg-gray-50 border border-gray-200 rounded-lg px-4 py-2 mb-2 select-none">
                    <span class="text-lg font-bold text-[#4455DD] tracking-widest" style="font-family:monospace;letter-spacing:.2em;">
                        <?= $n1 ?> + <?= $n2 ?> = ?
                    </span>
                </div>
                <input type="number" name="captcha_answer" required placeholder="Ketik jawaban di sini"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-[#4455DD]"
                       min="0" max="99">
            </div>

            <button type="submit"
                    class="w-full bg-[#4455DD] text-white py-2 rounded-lg font-semibold hover:opacity-90 transition">
                Login
            </button>
        </form>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7
                     a9.956 9.956 0 012.223-3.592M6.531 6.53A9.956 9.956 0 0112 5
                     c4.477 0 8.268 2.943 9.542 7a9.963 9.963 0 01-4.072 5.294
                     M3 3l18 18"/>`;
    } else {
        input.type = 'password';
        icon.innerHTML = `
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7
                     -1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
    }
}
</script>
