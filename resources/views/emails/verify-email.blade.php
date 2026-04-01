<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <style>
        /* Email-safe reset and base styles */
        body, table, td, p, a {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        /* Prevent font scaling in landscape */
        .ExternalClass, .ReadMsgBody {
            width: 100%;
            background-color: #f9faff;
        }
        body {
            font-family: 'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: linear-gradient(145deg, #f0f3fd 0%, #f9faff 100%);
            padding: 30px 15px;
            margin: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        /* Main container */
        .email-container {
            max-width: 520px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 32px;
            box-shadow: 0 20px 40px -10px rgba(79, 86, 143, 0.25), 0 8px 20px rgba(0, 20, 60, 0.08);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(2px);
        }
        /* header gradient area */
        .email-header {
            background: linear-gradient(135deg, #667eea 0%, #8a6cf0 100%);
            padding: 32px 40px 20px 40px;
            text-align: center;
        }
        .email-header h1 {
            color: #ffffff;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: -0.2px;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }
        .email-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            margin-top: 8px;
            font-weight: 400;
        }
        /* content area */
        .email-content {
            padding: 36px 40px 32px 40px;
            background: #ffffff;
        }
        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #1a1f3a;
            margin-bottom: 12px;
        }
        .greeting span {
            background: linear-gradient(145deg, #667eea22, #ffffff);
            padding: 3px 10px 3px 0;
        }
        .message {
            font-size: 16px;
            line-height: 26px;
            color: #2a2f4b;
            margin-bottom: 24px;
            font-weight: 400;
        }
        .message strong {
            color: #1f2544;
            font-weight: 600;
        }
        /* button styling */
        .btn-wrapper {
            text-align: center;
            margin: 32px 0 28px;
        }
        .verify-btn {
            display: inline-block;
            background: linear-gradient(145deg, #667eea, #7c6aef);
            color: #ffffff;
            font-size: 17px;
            font-weight: 600;
            padding: 16px 42px;
            text-decoration: none;
            border-radius: 60px;
            box-shadow: 0 10px 20px -5px rgba(102, 126, 234, 0.5), 0 4px 8px rgba(0, 0, 0, 0.1);
            letter-spacing: 0.3px;
            transition: all 0.15s ease;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .verify-btn:hover {
            background: linear-gradient(145deg, #5a71d0, #6b5bd8);
            box-shadow: 0 14px 26px -6px rgba(102, 126, 234, 0.6);
            transform: translateY(-2px);
        }
        /* fallback link */
        .fallback-link {
            text-align: center;
            font-size: 14px;
            color: #6f7799;
            margin: 16px 0 20px;
            word-break: break-all;
        }
        .fallback-link a {
            color: #667eea;
            text-decoration: underline;
            font-weight: 500;
        }
        /* ignore text */
        .ignore-message {
            background: #f7f9fe;
            padding: 18px 20px;
            border-radius: 20px;
            margin: 20px 0 14px;
            border-left: 4px solid #cad3ff;
            font-size: 15px;
            color: #3e4568;
        }
        .ignore-message svg {
            vertical-align: middle;
            margin-right: 6px;
        }
        /* signature */
        .signature {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 2px dashed #dee4fd;
            font-size: 16px;
            color: #2b3150;
        }
        .signature strong {
            color: #1f2544;
            font-weight: 600;
        }
        .team-name {
            background: #e9edff;
            padding: 3px 12px;
            border-radius: 40px;
            font-size: 15px;
            display: inline-block;
            margin-top: 6px;
            color: #4c5494;
        }
        /* footer small */
        .footer-note {
            text-align: center;
            padding: 20px 20px 24px;
            background: #fafbff;
            color: #8089b0;
            font-size: 13px;
            border-top: 1px solid #eaeefd;
        }
        .footer-note a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        /* responsive */
        @media screen and (max-width: 500px) {
            .email-content { padding: 30px 24px; }
            .email-header { padding: 28px 24px 16px; }
            .verify-btn { padding: 14px 30px; font-size: 16px; }
        }
    </style>
</head>
<body>
<div class="email-container">
    <!-- decorative header with wave effect -->
    <div class="email-header">
        <h1>✨ Verify your email</h1>
        <p>Just one click & you're all set</p>
    </div>

    <!-- main content -->
    <div class="email-content">
        <div class="greeting">
            <span>👋 Hello, {{ $user->name }}!</span>
        </div>

        <div class="message">
            <strong>Thanks for joining us!</strong> We're thrilled to have you on board. To start exploring all the features, please confirm that this is your email address.
        </div>

        <!-- big beautiful button -->
        <div class="btn-wrapper">
            <a href="{{ $url }}" class="verify-btn">
                ✓ Verify Email Address
            </a>
        </div>

        <!-- subtle fallback (hidden behind nice design) -->
        <div class="fallback-link">
            ⚡ Button not working? <a href="{{ $url }}">Click here</a> or copy the link.
        </div>

        <!-- ignore section with subtle icon -->
        <div class="ignore-message">
            <span style="font-size: 18px; margin-right: 8px;">🔒</span>
            <strong>Not you?</strong> If you didn’t create an account, simply ignore this email — no further action is needed.
        </div>

        <!-- signature with flair -->
        <div class="signature">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 22px;">🚀</span>
                <span style="flex:1;">Happy to have you,<br><strong>The Application Team</strong></span>
            </div>
            <div class="team-name">
                ★ craft • code • create
            </div>
        </div>
    </div>

    <!-- tiny footer with info -->
    <div class="footer-note">
        🌟 Need help? <a href="#">Contact support</a> — we're here for you.
        <div style="margin-top: 8px; opacity: 0.7;">© 2025 Your Application. All rights reserved.</div>
    </div>
</div>

<!-- optional extra spacing for email clients -->
<div style="text-align: center; font-size: 12px; color: #acb7d4; margin-top: 16px;">
    🌈 This is a secure verification email • {{ $user->email ?? '' }}
</div>
</body>
</html>
