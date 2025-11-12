<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uploader Link - Face Finder</title>
</head>
<body style="margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif; background-color: #f1f5f9;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background: linear-gradient(to bottom right, #f8fafc, #e0e7ff, #e0f2fe); padding: 40px 20px;">
        <tr>
            <td align="center">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 16px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);">
                    <!-- Header -->
                    <tr>
                        <td style="padding: 40px 40px 30px; text-align: center; background: linear-gradient(to right, #16a34a, #10b981, #14b8a6); border-radius: 16px 16px 0 0;">
                            <div style="display: inline-flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                                <span style="font-size: 24px; font-weight: bold; color: #ffffff;">Face Finder</span>
                            </div>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <h1 style="margin: 0 0 20px; font-size: 28px; font-weight: 700; color: #0f172a; line-height: 1.3;">
                                Upload Your Photos
                            </h1>
                            <p style="margin: 0 0 30px; font-size: 16px; color: #475569; line-height: 1.6;">
                                You've been invited to upload photos to <strong style="color: #0f172a;">{{ $albumName ?? 'the album' }}</strong>.
                            </p>

                            @if(isset($passcode) && !empty($passcode))
                            <div style="background-color: #f1f5f9; border-left: 4px solid #10b981; padding: 16px 20px; margin: 0 0 30px; border-radius: 8px;">
                                <p style="margin: 0; font-size: 14px; color: #64748b; font-weight: 500;">
                                    <strong style="color: #0f172a;">Passcode:</strong>
                                    <span style="font-family: 'Courier New', monospace; font-size: 18px; color: #10b981; font-weight: 700; letter-spacing: 2px;">{{ $passcode }}</span>
                                </p>
                            </div>
                            @endif

                            <div style="text-align: center; margin: 30px 0;">
                                <a href="{{ $uploaderUrl ?? '#' }}"
                                   style="display: inline-block; padding: 16px 32px; background: linear-gradient(to right, #16a34a, #10b981); color: #ffffff; text-decoration: none; border-radius: 12px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3); transition: all 0.3s;">
                                    Open Uploader Link
                                </a>
                            </div>

                            <p style="margin: 30px 0 0; font-size: 14px; color: #64748b; line-height: 1.6;">
                                Or copy and paste this link into your browser:
                            </p>
                            <p style="margin: 10px 0 0; font-size: 13px; color: #10b981; word-break: break-all; font-family: 'Courier New', monospace; background-color: #f1f5f9; padding: 12px; border-radius: 8px;">
                                {{ $uploaderUrl ?? '#' }}
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding: 30px 40px; text-align: center; background-color: #f8fafc; border-radius: 0 0 16px 16px; border-top: 1px solid #e2e8f0;">
                            <p style="margin: 0; font-size: 13px; color: #64748b; line-height: 1.6;">
                                This link was shared with you by Face Finder.<br>
                                If you didn't expect this email, you can safely ignore it.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

