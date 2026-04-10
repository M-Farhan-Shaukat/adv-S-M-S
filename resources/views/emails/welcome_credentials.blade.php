<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 20px; color: #333; }
        .container { max-width: 580px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
        .header { background: linear-gradient(135deg, #1e3a5f, #2d6a9f); color: white; padding: 30px; text-align: center; }
        .header h2 { margin: 0; font-size: 22px; }
        .header p { margin: 8px 0 0; opacity: 0.85; font-size: 14px; }
        .body { padding: 30px; }
        .credentials-box {
            background: #f8fafc;
            border: 2px dashed #2d6a9f;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .credentials-box .label { font-size: 12px; color: #666; text-transform: uppercase; letter-spacing: 1px; }
        .credentials-box .value { font-size: 18px; font-weight: bold; color: #1e3a5f; margin: 4px 0 16px; }
        .btn {
            display: inline-block;
            background: linear-gradient(135deg, #1e3a5f, #2d6a9f);
            color: white !important;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin: 20px 0;
        }
        .note { background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px 16px; border-radius: 4px; font-size: 13px; margin: 16px 0; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; color: #888; font-size: 12px; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h2>{{ $schoolName }}</h2>
        <p>Welcome to {{ $role }} Portal</p>
    </div>
    <div class="body">
        <p>Dear <strong>{{ $recipientName }}</strong>,</p>
        <p>Your <strong>{{ $role }}</strong> account has been created at <strong>{{ $schoolName }}</strong>. Here are your login credentials:</p>

        <div class="credentials-box">
            <div class="label">Email Address</div>
            <div class="value">{{ $email }}</div>

            <div class="label">Temporary Password</div>
            <div class="value" style="font-family: monospace; letter-spacing: 2px;">{{ $password }}</div>
        </div>

        <div style="text-align: center;">
            <a href="{{ $loginUrl }}" class="btn">Login to Portal →</a>
        </div>

        <div class="note">
            <strong>⚠️ Important:</strong> Please change your password after your first login for security.
        </div>

        @if($portalNote)
        <p style="font-size: 13px; color: #555;">{{ $portalNote }}</p>
        @endif

        <p style="font-size: 13px; color: #888;">If you have any issues logging in, please contact the school administration.</p>
    </div>
    <div class="footer">
        <p>This is an automated email from {{ $schoolName }}. Please do not reply.</p>
    </div>
</div>
</body>
</html>
