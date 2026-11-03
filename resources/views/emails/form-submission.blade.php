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
                            <h1 style="margin: 0; color: #ffffff; font-size: 18px; font-weight: 600;">{{ $siteName }}</h1>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="background-color: #ffffff; padding: 32px;">
                            <h2 style="margin: 0 0 8px; color: #111827; font-size: 16px; font-weight: 600;">New Enquiry</h2>
                            <p style="margin: 0 0 24px; color: #6b7280; font-size: 14px;">
                                Submitted via <strong>{{ $formName }}</strong> on {{ $submittedAt }}.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border: 1px solid #e5e7eb; border-radius: 6px; overflow: hidden;">
                                @foreach ($fields as $field)
                                    <tr style="border-bottom: 1px solid #e5e7eb;">
                                        <td style="padding: 12px 16px; background-color: #f9fafb; color: #374151; font-size: 13px; font-weight: 600; width: 35%; vertical-align: top;">
                                            {{ $field['label'] }}
                                        </td>
                                        <td style="padding: 12px 16px; color: #1f2937; font-size: 14px; vertical-align: top;">
                                            {{ $field['value'] ?: '—' }}
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="background-color: #f9fafb; padding: 20px 32px; border-radius: 0 0 8px 8px; border-top: 1px solid #e5e7eb;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                This is an automated notification from your website. Do not reply to this email.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
