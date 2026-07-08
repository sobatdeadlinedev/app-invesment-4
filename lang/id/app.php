<?php

return [
    // Navigation
    'home' => 'Beranda',
    'trade' => 'Trading',
    'team' => 'Tim',
    'my_assets' => 'Aset Saya',
    'language' => 'Bahasa',
    'profile' => 'Profil',

    // Common Actions
    'back' => 'Kembali',
    'continue' => 'Lanjutkan',
    'submit' => 'Kirim',
    'confirm' => 'Konfirmasi',
    'cancel' => 'Batal',
    'all' => 'Semua',
    'history' => 'Riwayat',
    'copy' => 'Salin',

    // Balance Transfer Page
    'exchange_balance' => 'Saldo Exchange',
    'trade_balance' => 'Saldo Trading',
    'from' => 'Dari',
    'transfer_to' => 'Transfer ke',
    'select_currency' => 'Pilih Mata Uang',
    'available' => 'Tersedia',
    'amount_of_transfers' => 'Jumlah transfer',
    'enter_transfer_amount' => 'Masukkan jumlah transfer',
    'minimum_transfer' => 'Transfer minimum',

    // Warnings & Messages
    'warning' => 'Peringatan',
'penalty_warning' => 'Volume trading Anda belum selesai. Penalti 20% akan dikenakan pada transfer ini.',
'penalty_warning_with_volume' => 'Volume trading Anda belum selesai. Kekurangan: :remaining USDT (:percentage%). Penalti 20% akan dikenakan pada transfer ini.',
'volume_info' => 'Transfer ini akan meningkatkan target volume trading Anda sebesar jumlah yang sama.',

    // Volume Progress
    'trading_volume_progress' => 'Progress Volume Trading',
    'target_volume' => 'Target Volume',
    'achieved_volume' => 'Volume Tercapai',
    'remaining_volume' => 'Volume Tersisa',
    'completed' => 'Selesai',

    // Alert Messages - Transfer
    'minimum_transfer_alert' => 'Jumlah transfer minimum adalah 10.00 USDT',
    'insufficient_balance' => 'Saldo tidak mencukupi',
    'transfer_confirmation' => 'Transfer :amount USDT dari :from ke :to?',

    // Deposit Page
    'deposit' => 'Deposit',
    'current_balance' => 'Saldo Saat Ini',
    'deposit_amount' => 'Jumlah Deposit',
    'enter_amount_usdt' => 'Masukkan Jumlah (USDT)',
    'enter_amount_manually' => 'Masukkan jumlah manual',
    // 'minimum_deposit' => 'Deposit minimum',
    'minimum_deposit_hint'   => 'Minimal deposit adalah 200 USDT',
    'minimum_deposit_alert'  => 'Minimal deposit adalah 200 USDT',
    'select_network' => 'Pilih Jaringan',
    'amount' => 'Jumlah',
    'payment' => 'Pembayaran',
    'transfer_usdt_to_ewallet' => 'Transfer USDT ke E-Wallet',
    'network' => 'Jaringan',
    'deposit_address' => 'Alamat Setoran',
    'upload_proof_of_transfer' => 'Upload Bukti Transfer',
    'click_to_upload' => 'Klik untuk upload',
    'file_size_limit' => 'PNG, JPG maksimal 5MB',
    'transfer_info' => 'Transfer USDT sesuai nominal yang tertera menggunakan network yang dipilih (:network). Lalu upload bukti transfer',

    // Networks
    'tron_network' => 'Jaringan TRON',
    'binance_smart_chain' => 'Binance Smart Chain',

    // Validation Messages - Deposit
    'amount_required'         => 'Jumlah wajib diisi',
    'wallet_type_required'    => 'Silakan pilih jaringan',
    'wallet_type_invalid'     => 'Tipe jaringan tidak valid',
    'payment_proof_required'  => 'Bukti transfer wajib diupload',
    'payment_proof_image'     => 'Bukti transfer harus berupa gambar',
    'payment_proof_mimes'     => 'Bukti transfer harus berformat JPG, JPEG, atau PNG',
    'please_enter_valid_amount' => 'Silakan masukkan jumlah yang valid',
    // 'minimum_deposit_alert' => 'Jumlah deposit minimum adalah 10 USDT',
    'please_upload_proof' => 'Silakan upload bukti transfer',
    'file_size_exceeded' => 'Ukuran file tidak boleh melebihi 5MB',
    'file_type_not_allowed' => 'Hanya file JPG, JPEG, dan PNG yang diperbolehkan',
    'deposit_confirmation' => 'Apakah Anda yakin ingin mengirim permintaan deposit ini?',
    'copied' => 'Disalin',

    // Deposit History Page
    'deposit_history' => 'Riwayat Deposit',
    'pending' => 'Menunggu',
    'approved' => 'Disetujui',
    'rejected' => 'Ditolak',
    'payment_method' => 'Metode Pembayaran',
    'date' => 'Tanggal',
    'processed_at' => 'Diproses Pada',
    'payment_proof' => 'Bukti Pembayaran',
    'view' => 'Lihat',
    'no_deposit_history' => 'Tidak ada riwayat deposit',
    'e_wallet' => 'E-Wallet',

    // ===== TRADING SIGNALS PAGE =====
    // Header & Navigation
    'open_signals' => 'Sinyal Terbuka',
    'select_coin' => 'Pilih Koin',
    'signals' => 'sinyal',
    'no_signals' => 'Tidak ada sinyal',

    // Notice & Warnings
    'notice' => 'Pemberitahuan:',
    'minimum_balance_required' => 'Minimum <strong>$ 200.00</strong> Saldo Trading tersedia diperlukan untuk bergabung dengan sinyal.',
    'please_transfer_funds' => 'Silakan <a href=":url" class="text-decoration-underline" style="color: #dc3545; font-weight: 600;">transfer dana</a> terlebih dahulu.',

    // Tabs
    'trading_signals' => 'Sinyal Trading',
    'historical_orders' => 'Riwayat Order',

    // Signal List
    'available_trading_signals' => 'Sinyal Trading Tersedia',
    'open' => 'TERBUKA',

    // Signal Configuration
    // 'signal_bet_type' => 'Tipe Taruhan Sinyal',
    'of_balance' => '% dari Saldo',
    'fixed' => 'Tetap',
    // 'signal_access' => 'Akses Sinyal',
    'public' => 'Publik',
    'private' => 'Privat',

    // Bet Preview
    'your_balance' => 'Saldo Anda',
    'your_bet' => 'Taruhan Anda',
    // 'participants' => 'Peserta',

    // Calculation
    // 'calculation' => 'Perhitungan:',
    'fixed_bet' => 'Taruhan Tetap:',
    'all_participants_bet_exactly' => 'Semua peserta bertaruh tepat',
    'regardless_of_balance' => 'tanpa memperhitungkan saldo',

    // Price Information
    'opening_price' => 'Harga Pembukaan',
    'settlement_price' => 'Harga Penyelesaian',

    // Signal Timing
    'opened_at' => 'Dibuka Pada',
    'win_rate' => 'Tingkat Kemenangan',

    // Good News
    // 'good_news' => 'Kabar baik!',
    // 'no_losses_no_fees' => 'Anda akan selalu menerima hadiah berdasarkan tingkat kemenangan. Tanpa kerugian, tanpa biaya! Taruhan Anda hanya dikunci sementara.',

    // Actions
    'join_this_signal' => 'MENGONFIRMASI UNTUK MENGIKUTI TRANSAKSI',
    'you_have_joined' => 'Anda telah bergabung dengan sinyal ini. Tunggu penyelesaian untuk menerima hadiah Anda!',
    'insufficient_balance_message' => 'Saldo Tidak Mencukupi:',
    'minimum_balance_required_short' => 'Minimum $200.00 Saldo Trading tersedia diperlukan.',
    'you_need_at_least' => 'Anda membutuhkan setidaknya',
    'available_short' => 'tersedia.',
    'transfer_now' => 'Transfer sekarang',

    // Join Confirmation
    'join_signal_confirmation' => 'Gabung sinyal ini?\n\nTaruhan Anda: $:bet_amount akan dikunci hingga penyelesaian.\n\nAnda akan menerima hadiah berdasarkan tingkat kemenangan.\n\nApakah Anda ingin melanjutkan?',
    'new_signal_available'   => 'Sinyal Baru Tersedia',
    'confirm_follow_signal'  => 'Konfirmasi Ikut Sinyal',
    'signal_joined_success'  => 'Berhasil mengikuti sinyal: :title. Taruhan :amount USDT telah dikunci.',
    'later'                  => 'Nanti',
    // Empty States
    'no_open_signals_for' => 'Tidak ada sinyal terbuka untuk',
    'check_back_later' => 'Periksa kembali nanti untuk sinyal trading baru',

    // Historical Orders
    'your_historical_orders' => 'Riwayat Order Anda -',
    'orders' => 'Order',
    'total' => 'Total',
    'profit_loss' => 'P/L',
    'fees' => 'Biaya',

    // Order Status
    'call' => 'CALL',
    'put' => 'PUT',
    'pending_status' => 'MENUNGGU',
    'na' => 'T/A',
    'direction' => 'Arah',

    // Order Details
    'time_period' => 'periode waktu',
    'trading_fee' => 'biaya trading (1%)',
    'net_profit_loss' => 'laba/rugi bersih',
    'rate_of_return' => 'tingkat pengembalian',
    'order_quantity' => 'jumlah order',
    'order_time' => 'waktu order',

    // Empty State - History
    'no_historical_orders_for' => 'Tidak ada riwayat order untuk',
    'join_signals_to_start' => 'Gabung sinyal untuk mulai trading',

    // // Info Section
    // 'how_it_works' => 'Cara Kerja',
    // 'bet_amount_varies' => 'Jumlah taruhan bervariasi per sinyal: berbasis persentase atau jumlah tetap',
    // 'percentage_signals' => 'Sinyal persentase: taruhan = % dari Saldo Trading Anda',
    // 'fixed_signals' => 'Sinyal tetap: jumlah taruhan sama untuk semua pengguna',
    // 'minimum_balance_info' => 'Minimum $ 100.00 saldo tersedia diperlukan (untuk sinyal persentase)',
    // 'bet_locked_info' => 'Taruhan Anda akan dikunci hingga penyelesaian',
    // 'always_win_rewards' => 'Anda selalu menang hadiah! Tanpa kerugian, tanpa biaya',
    // 'call_put_explanation' => 'CALL/PUT menunjukkan prediksi admin (bukan pergerakan pasar sebenarnya)',

    // ===== MY ASSETS PAGE =====
    // Section Headers
    'total_assets' => 'Total Aset',
    'today_pnl' => 'PnL Hari Ini',
    'my_account' => 'Akun Saya',
    'wallet_list' => 'Daftar Wallet',

    // Actions
    'withdrawal' => 'Penarikan',
    'transfer' => 'Transfer',
    'logout' => 'Keluar',

    // Account Types
    'exchange' => 'Exchange',
    'locked' => 'terkunci',

    // Wallet Management
    'add_wallet' => 'Tambah Wallet',
    'edit_wallet' => 'Edit Wallet',
    'no_wallet_yet' => 'Belum ada wallet',
    'delete_wallet_confirmation' => 'Yakin ingin menghapus wallet ini?',

    // Network Types
    'network_type' => 'Jenis Jaringan',
    'trc20' => 'TRC20',
    'bep20' => 'BEP20',

    // Wallet Form
    'wallet_address' => 'Alamat Wallet',
    'enter_wallet_address' => 'Masukkan alamat wallet',
    'ensure_address_match' => 'Pastikan address sesuai dengan network yang dipilih',
    'save' => 'Simpan',
    'update' => 'Update',
    // ===== TEAM / REFERRAL PAGE =====
    // Page Header
    'invite_friends' => 'Undang Teman',
    'earn_commission' => 'Dapatkan komisi referral',

    // QR & Invitation Section
    'my_qr_code' => 'QR Code Saya',
    'my_invitation_code' => 'Kode undangan saya',
    'my_invitation_link' => 'Link kode undangan saya',
    'save_qr' => 'Simpan QR',
    'copy_code' => 'Salin Kode',
    'copy_link' => 'Salin Link',

    // Statistics
    'recommended_number' => 'Jumlah orang yang direkomendasikan',
    'current_level' => 'Level saat ini',
    'total_revenue' => 'Total Pendapatan',

    // Rules
    'rules' => 'Aturan',
    'rule_share_code' => 'Bagikan kode referral atau link Anda dengan teman',
    'rule_earn_commission' => 'Dapatkan komisi ketika mereka bergabung dan trading',
    'rule_build_network' => 'Bangun jaringan Anda dan tingkatkan level Anda',
    'rule_higher_levels' => 'Level lebih tinggi mendapat tingkat komisi lebih baik',

    // Toast Messages
    'invitation_code_copied' => 'Kode undangan disalin!',
    'invitation_link_copied' => 'Link undangan disalin!',
    'qr_code_saved' => 'QR Code berhasil disimpan!',
    'failed_to_copy_code' => 'Gagal menyalin kode',
    'failed_to_copy_link' => 'Gagal menyalin link',
    'failed_to_save_qr' => 'Gagal menyimpan QR Code',
    // ===== VERIFICATION PAGE =====
    // Page Title & Header
    'account_verification' => 'Verifikasi KYC',
    'personal_information' => 'Informasi Pribadi',
    'upload_documents' => 'Upload Dokumen',
    'complete_verification_data' => 'Lengkapi data untuk verifikasi identitas Anda',

    // Personal Data Section
    'personal_data' => 'Data Diri',
    'full_name' => 'Nama Lengkap',
    'enter_full_name' => 'Masukkan nama lengkap',
    'identity_number' => 'Nomor Identitas',
    'enter_identity_number' => 'Masukkan nomor identitas',
    'identity_type' => 'Jenis Identitas',
    'select_identity_type' => 'Pilih Jenis Identitas',
    'ktp' => 'KTP',
    'sim' => 'SIM',
    'passport' => 'Paspor',

    // Photo Upload Section
    'identity_photo' => 'Foto Identitas',
    'upload_identity_photo' => 'Upload Foto Identitas',
    'selfie_with_identity' => 'Foto Selfie dengan Identitas',
    'upload_selfie_photo' => 'Upload Foto Selfie',
    'selfie_holding_identity' => 'Foto selfie sambil memegang identitas (Max 2MB)',
    'photo_format_info' => 'Format: JPG, JPEG, PNG (Max 2MB)',

    // Info & Instructions
    'verification_info' => 'Pastikan foto identitas dan selfie terlihat jelas untuk mempercepat proses verifikasi',

    // Actions
    'submit_verification' => 'Ajukan Verifikasi',

    // Verification Status
    'verification_pending' => 'Verifikasi Sedang Diproses',
    'verification_pending_message' => 'Dokumen Anda sedang dalam proses verifikasi oleh admin. Mohon tunggu hingga proses selesai.',
    'submitted_on' => 'Diajukan pada',
    'account_verified' => 'Akun Terverifikasi',
    'account_verified_message' => 'Selamat! Akun Anda telah berhasil diverifikasi.',
    'verified_on' => 'Diverifikasi pada',

    // Submitted/Verified Data
    'submitted_data' => 'Data yang Diajukan',
    'verified_data' => 'Data Terverifikasi',

    // Validation Messages
    'upload_all_photos' => 'Mohon upload semua foto yang diperlukan',
    'max_file_size' => 'Ukuran file maksimal 2MB',

    // ===== WITHDRAWAL PAGE =====
    // Header & Navigation
    'withdraw' => 'Penarikan',

    // Currency Section
    'currency' => 'Mata Uang',
    'usdt_tether' => 'USDT (Tether)',

    // Verification Alert
    'account_not_verified' => 'Akun Belum Terverifikasi',
    'account_not_verified_message' => 'Akun Anda belum terverifikasi. Untuk melakukan verifikasi akun kunjungi profile dan klik verifikasi akun.',

    // Balance Section
    'available_balance' => 'Saldo Tersedia',

    // Withdrawal Amount Section
    'withdrawal_amount' => 'Jumlah Penarikan',
    'amount_usdt' => 'Jumlah (USDT)',
    'enter_amount' => 'Masukkan jumlah',
    // 'minimum_withdrawal_info' => 'Penarikan minimum: 20 USDT | Biaya: 5 USDT (< 100 USDT) atau 5% (≥ 100 USDT)',

    // Wallet Selection
    'select_wallet_account' => 'Pilih Akun Wallet',
    'choose_wallet_account' => 'Pilih akun wallet Anda',
    'select_wallet_placeholder' => '-- Pilih Akun Wallet --',
    'no_wallet_available' => 'Tidak ada akun wallet tersedia',
    // 'need_add_wallet' => 'Anda perlu menambahkan akun wallet terlebih dahulu.',

    // Withdrawal Summary
    'withdrawal_summary' => 'Ringkasan Penarikan',
    'withdrawal_fee' => 'Biaya Penarikan',
    'you_will_receive' => 'Anda akan menerima',

    // // Withdrawal Information
    // 'withdrawal_information' => 'Informasi Penarikan',
    // 'withdrawal_process_info' => 'Withdrawal akan diproses dalam 1-3 hari kerja. Pastikan data wallet Anda sudah benar.',

    // Actions
    'submit_withdrawal' => 'Kirim Penarikan',

    // Alert Messages - Withdrawal
    'account_not_verified_alert' => 'Akun Anda belum terverifikasi. Silakan hubungi admin untuk verifikasi akun.',
    'withdrawal_not_processed' => 'Akun Anda belum terverifikasi. Withdrawal tidak dapat diproses. Silakan hubungi admin.',
    'enter_valid_amount' => 'Silakan masukkan jumlah yang valid',
    'minimum_withdrawal_20' => 'Jumlah penarikan minimum adalah 20 USDT',
    'insufficient_balance_withdraw' => 'Saldo tidak mencukupi. Saldo tersedia Anda adalah :balance USDT',
    'please_select_wallet' => 'Silakan pilih akun wallet',
    'amount_too_small' => 'Jumlah terlalu kecil. Setelah dikurangi biaya, Anda akan menerima 0 USDT atau kurang.',
    'confirm_withdrawal' => 'Konfirmasi penarikan?\n\nJumlah: :amount USDT\nBiaya: :fee USDT\nAnda akan menerima: :total USDT',

    // ===== WITHDRAWAL HISTORY PAGE =====
    // Page Header
    'withdrawal_history' => 'Riwayat Penarikan',

    // Filter Tabs
    'cancelled' => 'Dibatalkan',

    // Transaction Item
    'wallet_account' => 'Akun Wallet',
    'account_number' => 'Nomor Akun',
    'fee_5_percent' => 'Biaya (5%)',
    'you_receive' => 'Anda Terima',
    'completed_at' => 'Selesai Pada',

    // Actions
    'cancel_withdrawal' => 'Batalkan Penarikan',

    // Empty State
    'no_withdrawal_history' => 'Tidak ada riwayat penarikan',

    // Profile Dropdown
    'verification' => 'Verifikasi',
    'verified' => 'Sudah Terverifikasi',
    'unverified' => 'Belum Terverifikasi',
    'must_verify' => 'Harus Verifikasi',
    'phone_copied' => 'Nomor telepon tersalin!',

    // Confirmation Messages
    'confirm_cancel_withdrawal' => 'Yakin ingin membatalkan penarikan ini?',

    // ===== DASHBOARD PAGE =====
    'welcome_back' => 'Selamat datang,',
    'total_balance' => 'Total Saldo',
    'invite' => 'Undang',
    'announcement' => 'Pengumuman',
    'see_all' => 'Lihat Semua',
    'market' => 'Pasar',
    'banner_trade_title' => 'Ikuti Trader Expert',
    'banner_trade_text' => 'Dapatkan penghasilan pasif secara otomatis dengan strategi yang telah terbukti.',
    'banner_invite_title' => 'Undang & Dapatkan',
    'banner_invite_text' => 'Bagikan kode referral kamu dan dapatkan komisi dari setiap transaksi.',
    'banner_signal_text' => 'Ikuti sinyal expert secara langsung dan maksimalkan hasil trading kamu.',

    // ===== ACCESS PAGE =====
    'account_management' => 'Manajemen Akun',
    'transactions' => 'Transaksi',
    'manage_wallet_address' => 'Kelola alamat wallet penarikan',
    'account_already_verified' => 'Akun sudah terverifikasi',
    'verify_your_identity' => 'Verifikasi identitas kamu',
    'referral' => 'Referral',
    'invite_and_earn' => 'Undang teman & dapatkan komisi',
    'top_up_usdt_balance' => 'Top up saldo USDT',
    'withdraw_to_wallet' => 'Tarik saldo ke wallet kamu',
    'view_deposit_history' => 'Lihat histori deposit kamu',
    'view_withdrawal_history' => 'Lihat histori penarikan kamu',
    'sign_out_from_account' => 'Keluar dari akun',

    // ===== WALLET PAGE =====
    'wallet_management' => 'Manajemen Wallet',
    'wallet_information' => 'Informasi',
    'wallet_max_3' => 'Maksimal 3 wallet per akun',
    'wallet_network_supported' => 'Hanya mendukung jaringan TRC20 & BEP20',
    'wallet_address_warning' => 'Pastikan alamat wallet sudah benar sebelum melakukan penarikan',

    // ===== FUTURES PAGE =====
    'live' => 'LIVE',
    'pick_instrument' => 'Pilih Instrumen',
    'cryptocurrency' => 'Cryptocurrency',
    'forex' => 'Forex',
    'precious_metals' => 'Logam Mulia',
    'active_trade' => 'Trade Aktif',
    'seconds_remaining' => 'detik tersisa',
    'entry' => 'Entry',
    'current' => 'Saat ini',
    'potential' => 'Potensi',
    'open_trade' => 'Buka Trade',
    'trade_amount_usdt' => 'Jumlah (USDT)',
    'payout' => 'Payout',
    'total_return' => 'Total Kembali',
    'up' => 'Naik',
    'down' => 'Turun',
    'expert_signals' => 'Sinyal Expert',
    'legacy_mode' => 'Lama',
    'trade_history' => 'Riwayat Trade',
    'no_trades_yet' => 'Belum ada trade',
    'new_balance' => 'Balance baru:',
    'continue_trading' => 'Lanjut Trading',
    'enter_min_amount' => 'Masukkan jumlah minimal $1',
    'error_try_again' => 'Terjadi kesalahan. Coba lagi.',
    'win' => 'MENANG!',
    'lose' => 'KALAH',

    // ===== MARKET PAGE =====
    'hot' => 'Hot',
    'crypto' => 'Crypto',
    'metals' => 'Metals',
    'name_col' => 'Nama',
    'last_price' => 'Harga Terakhir',
    'change_24h' => '24j %',

    // ===== BOTTOMBAR =====
    'nav_home' => 'Beranda',
    'nav_futures' => 'Futures',
    'nav_access' => 'Validasi',
    'nav_wallet' => 'Dompet',

    // ===== WALLET / WITHDRAW =====
    'no_wallet_prefix' => 'Tidak ada wallet ',
    'no_wallet_suffix' => '',
    'minimum_withdrawal_50' => 'Minimum penarikan adalah 50 USDT',


    'active_signal' => 'sinyal aktif',
'no_active_signal' => 'Belum ada sinyal aktif',

    // ===== WITHDRAW PAGE REDESIGN =====
    'withdrawal_usdt' => 'Penarikan USDT',
    'withdrawal_subtitle' => 'Tarik USDT ke alamat mata uang digital',
    'blockchain_network' => 'Jaringan Blockchain',
    'no_withdrawal_address' => 'Tidak ada alamat penarikan',
    'bind' => 'Tambah',
    'quantity' => 'Jumlah',
    'receivable_amount' => 'Jumlah Diterima',
    'withdrawal_instructions' => 'Instruksi Penarikan',
    'wd_instr_1' => 'Waktu penarikan adalah 24 jam.',
    'wd_instr_2' => 'Saat ini, penarikan hanya mendukung USDT (jaringan TRC20 & BEP20).',
    'wd_instr_3' => 'Setelah mengajukan permintaan penarikan, dana akan dibekukan hingga proses selesai.',
    'wd_instr_4' => 'Penarikan akan tiba dalam 12 jam setelah pengajuan. Jika tidak diterima, hubungi layanan pelanggan.',
    'withdrawal_btn' => 'Tarik Dana',
];
