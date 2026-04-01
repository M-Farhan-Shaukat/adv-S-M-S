<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset your password</title>
    <style>
        /* robust email client reset */
        body, table, td, p, a {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Inter', 'Segoe UI', 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: linear-gradient(145deg, #eef2f6 0%, #f5f9ff 100%);
            padding: 25px 15px;
            margin: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        /* main card container — softer, modern */
        .reset-container {
            max-width: 540px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 40px;
            box-shadow: 0 30px 50px -20px rgba(30, 45, 90, 0.25), 0 12px 25px rgba(0, 0, 0, 0.03);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.7);
            backdrop-filter: blur(2px);
        }
        /* header with gentle depth */
        .reset-header {
            background: linear-gradient(105deg, #3b4c9b 0%, #5c6ac4 100%);
            padding: 34px 40px 24px 40px;
            text-align: center;
        }
        .reset-header h1 {
            color: #ffffff;
            font-size: 32px;
            font-weight: 600;
            letter-spacing: -0.3px;
            margin: 0 0 6px 0;
            text-shadow: 0 2px 5px rgba(0, 10, 30, 0.2);
        }
        .reset-header .secure-badge {
            color: rgba(255, 255, 255, 0.85);
            font-size: 16px;
            font-weight: 400;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: rgba(255,255,255,0.12);
            padding: 8px 18px;
            border-radius: 60px;
            width: fit-content;
            margin: 12px auto 0;
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255,255,255,0.2);
        }
        /* main content */
        .reset-content {
            padding: 40px 42px 32px 42px;
            background: #ffffff;
        }
        .greeting {
            font-size: 20px;
            font-weight: 600;
            color: #161e3c;
            margin-bottom: 10px;
        }
        .greeting span {
            background: #edf2ff;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 18px;
        }
        .message {
            font-size: 16px;
            line-height: 27px;
            color: #2a314b;
            margin: 10px 0 20px;
            font-weight: 400;
        }
        .message strong {
            color: #1f2a5a;
            font-weight: 600;
        }
        /* secure notice */
        .request-context {
            background: #f5f8ff;
            border-radius: 28px;
            padding: 16px 20px;
            margin: 18px 0 24px;
            border-left: 5px solid #5c6ac4;
            color: #252f54;
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .request-context .icon {
            background: #ffffff;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }
        /* button */
        .btn-wrapper {
            text-align: center;
            margin: 32px 0 28px;
        }
        .reset-btn {
            display: inline-block;
            background: linear-gradient(145deg, #4f5db5, #5c6ac4);
            color: #ffffff !important;
            font-size: 18px;
            font-weight: 600;
            padding: 16px 48px;
            text-decoration: none;
            border-radius: 60px;
            box-shadow: 0 12px 22px -8px #3f4c9b80, 0 6px 12px rgba(0, 20, 40, 0.1);
            letter-spacing: 0.4px;
            transition: all 0.15s;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .reset-btn:hover {
            background: linear-gradient(145deg, #4452a5, #4f5db5);
            box-shadow: 0 16px 28px -6px #3b4b9bb0;
            transform: translateY(-2px);
        }
        /* fallback link */
        .fallback {
            text-align: center;
            font-size: 14px;
            margin: 16px 0 20px;
            color: #67729e;
        }
        .fallback a {
            color: #4f5db5;
            text-decoration: underline;
            font-weight: 500;
        }
        /* ignore block */
        .ignore-card {
            background: #fafbff;
            padding: 16px 22px;
            border-radius: 24px;
            margin: 24px 0 14px;
            border: 1px solid #dfe6fd;
            color: #333f6e;
            font-size: 15px;
            text-align: left;
            display: flex;
            gap: 12px;
            align-items: flex-start;
        }
        .ignore-card span:first-child {
            font-size: 22px;
        }
        /* signature */
        .signature {
            margin-top: 28px;
            padding-top: 22px;
            border-top: 2px solid #e8edfe;
            font-size: 16px;
            color: #20294e;
        }
        .signature .team {
            font-weight: 600;
            color: #202b59;
        }
        .badge-role {
            background: #e4e9ff;
            border-radius: 30px;
            padding: 6px 16px;
            font-size: 14px;
            display: inline-block;
            margin-top: 8px;
            color: #34428e;
        }
        /* footer */
        .footer-safe {
            text-align: center;
            padding: 22px 30px 24px;
            background: #f8faff;
            color: #61709e;
            font-size: 13px;
            border-top: 1px solid #dee6fd;
        }
        .footer-safe a {
            color: #4f5db5;
            text-decoration: none;
            font-weight: 500;
        }
        @media screen and (max-width: 500px) {
            .reset-content { padding: 30px 25px; }
            .reset-header { padding: 28px 25px 18px; }
            .reset-btn { padding: 14px 32px; font-size: 17px; }
        }
    </style>
</head>
<body>
<div class="reset-container">
    <!-- header with reassuring lock -->
    <div class="reset-header">
        <h1>🔐 password reset</h1>
        <div class="secure-badge">
            <span>✓ secure request</span>
            <span style="font-size: 18px;">🛡️</span>
        </div>
    </div>

    <div class="reset-content">
        <div class="greeting">
            <span>Hi {{ $user->name ?? 'there' }},</span>
        </div>

        <div class="message">
            <strong>We received a request</strong> to reset the password for your account.
            If you made this request, click the button below. <span style="font-size: 18px;">👇</span>
        </div>

        <!-- context box  -->
        <div class="request-context">
            <div class="icon">⏳</div>
            <div><strong>This link expires in 60 minutes</strong> — for your security, don't share it.</div>
        </div>

        <!-- primary CTA -->
        <div class="btn-wrapper">
            <a href="{{ $url }}" class="reset-btn">
                🔁 Reset password
            </a>
        </div>

        <!-- secondary link for safety -->
        <div class="fallback">
            ⚡ Button not responding? <a href="{{ $url }}">Use this direct link</a>
        </div>

        <!-- ignore section with clear instruction -->
        <div class="ignore-card">
            <span>🚫</span>
            <div>
                <strong>Didn’t request this?</strong> No problem — just ignore this email.
                Your password will stay the same, and no changes will be made.
            </div>
        </div>

        <!-- signature block -->
        <div class="signature">
            <div style="display: flex; align-items: center; gap: 8px;">
                <span style="font-size: 28px;">🔷</span>
                <span>Stay secure,<br><span class="team">The Application Team</span></span>
            </div>
            <div class="badge-role">
                ✦ help & support available 24/7 ✦
            </div>
        </div>
    </div>

    <!-- footer -->
    <div class="footer-safe">
        💙 Need assistance? <a href="#">Contact our support</a> — we’re ready to help.
        <div style="margin-top: 8px; opacity: 0.7;">© {{ date('Y') }} Your Application. Secure reset service.</div>
    </div>
</div>

<!-- hidden data / fallback info for email clients -->
<div style="text-align: center; font-size: 12px; color: #98a6cf; margin-top: 14px;">
    🌐 password reset • {{ $user->email ?? 'account' }}
</div>
</body>
</html>
