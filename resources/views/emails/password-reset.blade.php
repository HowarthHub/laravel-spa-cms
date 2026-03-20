<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin: 0; padding: 0; background-color: #f4f4f5; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f4f4f5; padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellspacing="0" cellpadding="0" style="max-width: 600px; width: 100%;">
                    {{-- Header --}}
                    <tr>
                        <td style="background-color: #0891b2; padding: 24px 32px; border-radius: 8px 8px 0 0;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 18px; font-weight: 600;">{{ config('app.name') }}</h1>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="background-color: #ffffff; padding: 32px;">
                            <h2 style="margin: 0 0 16px; color: #111827; font-size: 16px; font-weight: 600;">Reset Your Password</h2>
                            <p style="margin: 0 0 24px; color: #4b5563; font-size: 14px; line-height: 1.6;">
                                You are receiving this email because we received a password reset request for your account. Click the button below to reset your password.
                            </p>

                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin: 0 auto 24px;">
                                <tr>
                                    <td style="border-radius: 6px; background-color: #0891b2;">
                                        <a href="{{ $url }}" style="display: inline-block; padding: 12px 32px; color: #ffffff; font-size: 14px; font-weight: 600; text-decoration: none;">
                                            Reset Password
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin: 0 0 16px; color: #6b7280; font-size: 13px; line-height: 1.5;">
                                This password reset link will expire in {{ $count }} minutes.
                            </p>
                            <p style="margin: 0; color: #6b7280; font-size: 13px; line-height: 1.5;">
                                If you did not request a password reset, no further action is required.
                            </p>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 32px; border-radius: 0 0 8px 8px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0 0 8px; color: #9ca3af; font-size: 12px;">
                                If you're having trouble clicking the button, copy and paste this URL into your browser:
                            </p>
                            <p style="margin: 0; color: #0891b2; font-size: 12px; word-break: break-all;">
                                {{ $url }}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
