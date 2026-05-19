<?php

return [
    // Navigation
    'home' => 'Home',
    'trade' => 'Trade',
    'team' => 'Team',
    'my_assets' => 'My Assets',
    'language' => 'Language',
    'profile' => 'Profile',

    // Common Actions
    'back' => 'Back',
    'continue' => 'Continue',
    'submit' => 'Submit',
    'confirm' => 'Confirm',
    'cancel' => 'Cancel',
    'all' => 'All',
    'history' => 'History',
    'copy' => 'Copy',

    // Balance Transfer Page
    'exchange_balance' => 'Exchange Balance',
    'trade_balance' => 'Trade Balance',
    'from' => 'From',
    'transfer_to' => 'Transfer to',
    'select_currency' => 'Please select a currency',
    'available' => 'Available',
    'amount_of_transfers' => 'Amount of transfers',
    'enter_transfer_amount' => 'Please enter the transferred amount',
    'minimum_transfer' => 'Minimum transfer',

    // Warnings & Messages
   'warning' => 'Warning',
'penalty_warning' => 'Your trading volume is not completed yet. A 20% penalty will be applied to this transfer.',
'penalty_warning_with_volume' => 'Your trading volume is not completed yet. Remaining: :remaining USDT (:percentage%). A 20% penalty will be applied to this transfer.',
'volume_info' => 'This transfer will increase your trading volume target by the same amount.',

    // Volume Progress
    'trading_volume_progress' => 'Trading Volume Progress',
    'target_volume' => 'Target Volume',
    'achieved_volume' => 'Achieved Volume',
    'remaining_volume' => 'Remaining Volume',
    'completed' => 'Completed',

    // Alert Messages - Transfer
    'minimum_transfer_alert' => 'Minimum transfer amount is 10.00 USDT',
    'insufficient_balance' => 'Insufficient balance',
    'transfer_confirmation' => 'Transfer :amount USDT from :from to :to?',

    // Deposit Page
    'deposit' => 'Deposit',
    'current_balance' => 'Current Balance',
    'deposit_amount' => 'Deposit Amount',
    'enter_amount_usdt' => 'Enter Amount (USDT)',
    'enter_amount_manually' => 'Enter amount manually',
    
    // 'minimum_deposit' => 'Minimum deposit',
    'select_network' => 'Select Network',
    'amount' => 'Amount',
    'payment' => 'Payment',
    'transfer_usdt_to_ewallet' => 'Transfer USDT to E-Wallet',
    'network' => 'Network',
    'deposit_address' => 'Deposit Address',
    'upload_proof_of_transfer' => 'Upload Proof of Transfer',
    'click_to_upload' => 'Click to upload',
    'file_size_limit' => 'PNG, JPG up to 5MB',
    'transfer_info' => 'Transfer USDT according to the stated amount using the selected network (:network). Then upload proof of transfer',

    // Networks
    'tron_network' => 'TRON Network',
    'binance_smart_chain' => 'Binance Smart Chain',

    // Validation Messages - Deposit
    'amount_required'         => 'Amount is required',
    'wallet_type_required'    => 'Please select a network',
    'wallet_type_invalid'     => 'Invalid network type',
    'payment_proof_required'  => 'Payment proof is required',
    'payment_proof_image'     => 'Payment proof must be an image',
    'payment_proof_mimes'     => 'Payment proof must be JPG, JPEG, or PNG',
    'please_enter_valid_amount' => 'Please enter a valid amount',
     'minimum_deposit_hint'   => 'Minimum deposit is 200 USDT',
    'minimum_deposit_alert'  => 'Minimum deposit is 200 USDT',
    // 'minimum_deposit_alert' => 'Minimum deposit amount is 10 USDT',
    'please_upload_proof' => 'Please upload proof of transfer',
    'file_size_exceeded' => 'File size must not exceed 5MB',
    'file_type_not_allowed' => 'Only JPG, JPEG, and PNG files are allowed',
    'deposit_confirmation' => 'Are you sure you want to submit this deposit request?',
    'copied' => 'Copied',

    // Deposit History Page
    'deposit_history' => 'Deposit History',
    'pending' => 'Pending',
    'approved' => 'Approved',
    'rejected' => 'Rejected',
    'payment_method' => 'Payment Method',
    'date' => 'Date',
    'processed_at' => 'Processed At',
    'payment_proof' => 'Payment Proof',
    'view' => 'View',
    'no_deposit_history' => 'No deposit history',
    'e_wallet' => 'E-Wallet',

    // ===== TRADING SIGNALS PAGE =====
    // Header & Navigation
    'open_signals' => 'Open Signals',
    'select_coin' => 'Select Coin',
    'signals' => 'signals',
    'no_signals' => 'No signals',

    // Notice & Warnings
    'notice' => 'Notice:',
    'minimum_balance_required' => 'Minimum <strong>$ 200.00</strong> available Trade Balance required to join signals.',
    'please_transfer_funds' => 'Please <a href=":url" class="text-decoration-underline" style="color: #dc3545; font-weight: 600;">transfer funds</a> first.',

    // Tabs
    'trading_signals' => 'Trading Signals',
    'historical_orders' => 'Historical Orders',

    // Signal List
    'available_trading_signals' => 'Available Trading Signals',
    'open' => 'OPEN',

    // Signal Configuration
    // 'signal_bet_type' => 'Signal Bet Type',
    'of_balance' => '% of Balance',
    'fixed' => 'Fixed',
    // 'signal_access' => 'Signal Access',
    'public' => 'Public',
    'private' => 'Private',

    // Bet Preview
    'your_balance' => 'Your Balance',
    'your_bet' => 'Your Bet',
    // 'participants' => 'Participants',

    // Calculation
    // 'calculation' => 'Calculation:',
    'fixed_bet' => 'Fixed Bet:',
    'all_participants_bet_exactly' => 'All participants bet exactly',
    'regardless_of_balance' => 'regardless of balance',

    // Price Information
    'opening_price' => 'Opening Price',
    'settlement_price' => 'Settlement Price',

    // Signal Timing
    'opened_at' => 'Opened At',
    'win_rate' => 'Win Rate',

    // Good News
    // 'good_news' => 'Good news!',
    // 'no_losses_no_fees' => 'You will always receive rewards based on the win rate. No losses, no fees! Your bet is just locked temporarily.',

    // Actions
    'join_this_signal' => 'CONFIRM TO FOLLOW ORDER',
    'you_have_joined' => 'You have joined this signal. Wait for settlement to receive your rewards!',
    'insufficient_balance_message' => 'Insufficient Balance:',
    'minimum_balance_required_short' => 'Minimum $200.00 available Trade Balance required.',
    'you_need_at_least' => 'You need at least',
    'available_short' => 'available.',
    'transfer_now' => 'Transfer now',

    // Join Confirmation
    'join_signal_confirmation' => 'Join this signal?\n\nYour bet: $:bet_amount will be locked until settlement.\n\nYou will receive rewards based on the win rate.\n\nDo you want to continue?',
    'new_signal_available'   => 'New Signal Available',
    'confirm_follow_signal'  => 'Confirm Follow Signal',
    'signal_joined_success'  => 'Successfully joined signal: :title. Bet :amount USDT has been locked.',
    'later'                  => 'Later',
    // Empty States
    'no_open_signals_for' => 'No open signals for',
    'check_back_later' => 'Check back later for new trading signals',

    // Historical Orders
    'your_historical_orders' => 'Your Historical Orders -',
    'orders' => 'Orders',
    'total' => 'Total',
    'profit_loss' => 'P/L',
    'fees' => 'Fees',

    // Order Status
    'call' => 'CALL',
    'put' => 'PUT',
    'pending_status' => 'PENDING',
    'na' => 'N/A',
    'direction' => 'Direction',

    // Order Details
    'time_period' => 'time period',
    'trading_fee' => 'trading fee (1%)',
    'net_profit_loss' => 'net profit/loss',
    'rate_of_return' => 'rate of return',
    'order_quantity' => 'order quantity',
    'order_time' => 'order time',

    // Empty State - History
    'no_historical_orders_for' => 'No historical orders for',
    'join_signals_to_start' => 'Join signals to start trading',

    // Info Section
    // 'how_it_works' => 'How It Works',
    // 'bet_amount_varies' => 'Bet amount varies by signal: percentage-based or fixed amount',
    // 'percentage_signals' => 'Percentage signals: bet = % of your Trade Balance',
    // 'fixed_signals' => 'Fixed signals: same bet amount for all users',
    // 'minimum_balance_info' => 'Minimum $ 100.00 available balance required (for percentage signals)',
    // 'bet_locked_info' => 'Your bet will be locked until settlement',
    // 'always_win_rewards' => 'You always win rewards! No losses, no fees',
    // 'call_put_explanation' => 'CALL/PUT shows admin\'s prediction (not actual market movement)',

    // ===== MY ASSETS PAGE =====
    // Section Headers
    'total_assets' => 'Total Assets',
    'today_pnl' => "Today's PnL",
    'my_account' => 'My Account',
    'wallet_list' => 'Wallet List',

    // Actions
    'withdrawal' => 'Withdrawal',
    'transfer' => 'Transfer',
    'logout' => 'Logout',

    // Account Types
    'exchange' => 'Exchange',
    'locked' => 'locked',

    // Wallet Management
    'add_wallet' => 'Add Wallet',
    'edit_wallet' => 'Edit Wallet',
    'no_wallet_yet' => 'No wallet yet',
    'delete_wallet_confirmation' => 'Are you sure you want to delete this wallet?',

    // Network Types
    'network_type' => 'Network Type',
    'trc20' => 'TRC20',
    'bep20' => 'BEP20',

    // Wallet Form
    'wallet_address' => 'Wallet Address',
    'enter_wallet_address' => 'Enter wallet address',
    'ensure_address_match' => 'Make sure the address matches the selected network',
    'save' => 'Save',
    'update' => 'Update',
    // ===== TEAM / REFERRAL PAGE =====
    // Page Header
    'invite_friends' => 'Invite Friends',
    'earn_commission' => 'Earn referral commissions',

    // QR & Invitation Section
    'my_qr_code' => 'My QR Code',
    'my_invitation_code' => 'My invitation code',
    'my_invitation_link' => 'My invitation code link',
    'save_qr' => 'Save QR',
    'copy_code' => 'Copy Code',
    'copy_link' => 'Copy Link',

    // Statistics
    'recommended_number' => 'Recommended number of people',
    'current_level' => 'Current level',
    'total_revenue' => 'Total Revenue',

    // Rules
    'rules' => 'Rules',
    'rule_share_code' => 'Share your referral code or link with friends',
    'rule_earn_commission' => 'Earn commission when they join and trade',
    'rule_build_network' => 'Build your network and increase your level',
    'rule_higher_levels' => 'Higher levels get better commission rates',

    // Toast Messages
    'invitation_code_copied' => 'Invitation code copied!',
    'invitation_link_copied' => 'Invitation link copied!',
    'qr_code_saved' => 'QR Code saved successfully!',
    'failed_to_copy_code' => 'Failed to copy code',
    'failed_to_copy_link' => 'Failed to copy link',
    'failed_to_save_qr' => 'Failed to save QR Code',
    // ===== VERIFICATION PAGE =====
    // Page Title & Header
    'account_verification' => 'KYC Verification',
    'personal_information' => 'Personal Information',
    'upload_documents' => 'Upload Documents',
    'complete_verification_data' => 'Complete data for identity verification',

    // Personal Data Section
    'personal_data' => 'Personal Data',
    'full_name' => 'Full Name',
    'enter_full_name' => 'Enter full name',
    'identity_number' => 'Identity Number',
    'enter_identity_number' => 'Enter identity number',
    'identity_type' => 'Identity Type',
    'select_identity_type' => 'Select Identity Type',
    'ktp' => 'KTP',
    'sim' => 'SIM',
    'passport' => 'Passport',

    // Photo Upload Section
    'identity_photo' => 'Identity Photo',
    'upload_identity_photo' => 'Upload Identity Photo',
    'selfie_with_identity' => 'Selfie with Identity',
    'upload_selfie_photo' => 'Upload Selfie Photo',
    'selfie_holding_identity' => 'Selfie photo while holding identity (Max 2MB)',
    'photo_format_info' => 'Format: JPG, JPEG, PNG (Max 2MB)',

    // Info & Instructions
    'verification_info' => 'Make sure identity and selfie photos are clearly visible to speed up the verification process',

    // Actions
    'submit_verification' => 'Submit Verification',

    // Verification Status
    'verification_pending' => 'Verification Being Processed',
    'verification_pending_message' => 'Your documents are being verified by admin. Please wait until the process is complete.',
    'submitted_on' => 'Submitted on',
    'account_verified' => 'Account Verified',
    'account_verified_message' => 'Congratulations! Your account has been successfully verified.',
    'verified_on' => 'Verified on',

    // Submitted/Verified Data
    'submitted_data' => 'Submitted Data',
    'verified_data' => 'Verified Data',

    // Validation Messages
    'upload_all_photos' => 'Please upload all required photos',
    'max_file_size' => 'Maximum file size is 2MB',

    // ===== WITHDRAWAL PAGE =====
    // Header & Navigation
    'withdraw' => 'Withdraw',

    // Currency Section
    'currency' => 'Currency',
    'usdt_tether' => 'USDT (Tether)',

    // Verification Alert
    'account_not_verified' => 'Account Not Verified',
    'account_not_verified_message' => 'Your account is not verified. To verify your account, visit profile and click account verification.',

    // Balance Section
    'available_balance' => 'Available Balance',

    // Withdrawal Amount Section
    'withdrawal_amount' => 'Withdrawal Amount',
    'amount_usdt' => 'Amount (USDT)',
    'enter_amount' => 'Enter amount',
    // 'minimum_withdrawal_info' => 'Minimum withdrawal: 20 USDT | Fee: 5 USDT (< 100 USDT) or 5% (≥ 100 USDT)',

    // Wallet Selection
    'select_wallet_account' => 'Select Wallet Account',
    'choose_wallet_account' => 'Choose your wallet account',
    'select_wallet_placeholder' => '-- Select Wallet Account --',
    'no_wallet_available' => 'No wallet account available',
    // 'need_add_wallet' => 'You need to add a wallet account first.',

    // Withdrawal Summary
    'withdrawal_summary' => 'Withdrawal Summary',
    'withdrawal_fee' => 'Withdrawal Fee',
    'you_will_receive' => 'You will receive',

    // // Withdrawal Information
    // 'withdrawal_information' => 'Withdrawal Information',
    // 'withdrawal_process_info' => 'Withdrawal will be processed within 1-3 business days. Make sure your wallet data is correct.',

    // Actions
    'submit_withdrawal' => 'Submit Withdrawal',

    // Alert Messages - Withdrawal
    'account_not_verified_alert' => 'Your account is not verified. Please contact admin for account verification.',
    'withdrawal_not_processed' => 'Your account is not verified. Withdrawal cannot be processed. Please contact admin.',
    'enter_valid_amount' => 'Please enter a valid amount',
    'minimum_withdrawal_20' => 'Minimum withdrawal amount is 20 USDT',
    'insufficient_balance_withdraw' => 'Insufficient balance. Your available balance is :balance USDT',
    'please_select_wallet' => 'Please select a wallet account',
    'amount_too_small' => 'Amount too small. After fee deduction, you will receive 0 USDT or less.',
    'confirm_withdrawal' => 'Confirm withdrawal?\n\nAmount: :amount USDT\nFee: :fee USDT\nYou will receive: :total USDT',

    // ===== WITHDRAWAL HISTORY PAGE =====
    // Page Header
    'withdrawal_history' => 'Withdrawal History',

    // Filter Tabs
    'cancelled' => 'Cancelled',

    // Transaction Item
    'wallet_account' => 'Wallet Account',
    'account_number' => 'Account Number',
    'fee_5_percent' => 'Fee (5%)',
    'you_receive' => 'You Receive',
    'completed_at' => 'Completed At',

    // Actions
    'cancel_withdrawal' => 'Cancel Withdrawal',

    // Empty State
    'no_withdrawal_history' => 'No withdrawal history',

    // Profile Dropdown
    'verification' => 'Verification',
    'verified' => 'Verified',
    'unverified' => 'Unverified',
    'must_verify' => 'Must Verify',
    'phone_copied' => 'Phone number copied!',

    // ===== DASHBOARD PAGE =====
    'welcome_back' => 'Welcome back,',
    'total_balance' => 'Total Balance',
    'invite' => 'Invite',
    'announcement' => 'Announcement',
    'see_all' => 'See All',
    'market' => 'Market',
    'banner_trade_title' => 'Copy Expert Traders',
    'banner_trade_text' => 'Earn passive income automatically and proven strategies at your fingertips.',
    'banner_invite_title' => 'Invite & Earn',
    'banner_invite_text' => 'Share your referral code and earn commissions on every trade.',
    'banner_signal_text' => 'Follow live expert signals and maximize your trading results.',

    // ===== ACCESS PAGE =====
    'account_management' => 'Account Management',
    'transactions' => 'Transactions',
    'manage_wallet_address' => 'Manage withdrawal wallet addresses',
    'account_already_verified' => 'Account already verified',
    'verify_your_identity' => 'Verify your identity',
    'referral' => 'Referral',
    'invite_and_earn' => 'Invite friends & earn commission',
    'top_up_usdt_balance' => 'Top up USDT balance',
    'withdraw_to_wallet' => 'Withdraw balance to your wallet',
    'view_deposit_history' => 'View your deposit history',
    'view_withdrawal_history' => 'View your withdrawal history',
    'sign_out_from_account' => 'Sign out from account',

    // ===== WALLET PAGE =====
    'wallet_management' => 'Wallet Management',
    'wallet_information' => 'Information',
    'wallet_max_3' => 'Maximum 3 wallets per account',
    'wallet_network_supported' => 'Only supports TRC20 & BEP20 networks',
    'wallet_address_warning' => 'Make sure the wallet address is correct before withdrawing',

    // Confirmation Messages
    'confirm_cancel_withdrawal' => 'Are you sure you want to cancel this withdrawal?',

    // ===== FUTURES PAGE =====
    'live' => 'LIVE',
    'pick_instrument' => 'Pick Instrument',
    'cryptocurrency' => 'Cryptocurrency',
    'forex' => 'Forex',
    'precious_metals' => 'Precious Metals',
    'active_trade' => 'Active Trade',
    'seconds_remaining' => 'seconds remaining',
    'entry' => 'Entry',
    'current' => 'Current',
    'potential' => 'Potential',
    'open_trade' => 'Open Trade',
    'trade_amount_usdt' => 'Amount (USDT)',
    'payout' => 'Payout',
    'total_return' => 'Total Return',
    'up' => 'Up',
    'down' => 'Down',
    'expert_signals' => 'Expert Signals',
    'legacy_mode' => 'Legacy',
    'trade_history' => 'Trade History',
    'no_trades_yet' => 'No trades yet',
    'new_balance' => 'New Balance:',
    'continue_trading' => 'Continue Trading',
    'enter_min_amount' => 'Enter minimum amount of $1',
    'error_try_again' => 'An error occurred. Please try again.',
    'win' => 'WIN!',
    'lose' => 'LOSE',

    // ===== MARKET PAGE =====
    'hot' => 'Hot',
    'crypto' => 'Crypto',
    'metals' => 'Metals',
    'name_col' => 'Name',
    'last_price' => 'Last Price',
    'change_24h' => '24h %',

    // ===== BOTTOMBAR =====
    'nav_home' => 'Home',
    'nav_futures' => 'Futures',
    'nav_access' => 'Access',
    'nav_wallet' => 'Wallet',

    // ===== WALLET / WITHDRAW =====
    'no_wallet_prefix' => 'No ',
    'no_wallet_suffix' => ' wallet',
    'minimum_withdrawal_50' => 'Minimum withdrawal is 50 USDT',

    // ===== WITHDRAW PAGE REDESIGN =====
    'withdrawal_usdt' => 'Withdrawal USDT',
    'withdrawal_subtitle' => 'Withdraw USDT to digital currency address',
    'blockchain_network' => 'Blockchain Network',
    'no_withdrawal_address' => 'No withdrawal address bound',
    'bind' => 'Bind',
    'quantity' => 'Quantity',
    'all' => 'All',
    'available' => 'Available',
    'receivable_amount' => 'Receivable Amount',
    'withdrawal_instructions' => 'Withdrawal Instructions',
    'wd_instr_1' => 'Withdrawal time is 24 hours.',
    'wd_instr_2' => 'Currently, withdrawals only support USDT (TRC20 & BEP20 networks).',
    'wd_instr_3' => 'After submitting a withdrawal request, the funds will be frozen until the withdrawal is complete.',
    'wd_instr_4' => 'Withdrawals will arrive within 12 hours after submission. If not received, please contact customer service.',
    'select_currency' => 'Select Currency',
    'withdrawal_btn' => 'Withdrawal',
];
