<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kode OTP</title>
</head>
<body style="margin:0;padding:0;background:#FAF7F2;font-family:Arial,sans-serif;">
    <table width="100%" cellpadding="0" cellspacing="0" style="padding:32px 16px;">
        <tr>
            <td align="center">
                <table width="480" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:16px;padding:32px;border:1px solid #e7e5e4;">
                    <tr>
                        <td>
                            <p style="margin:0;color:#C9952A;font-size:12px;font-weight:bold;letter-spacing:1px;">GBI PONDOK DAUD KENDARI</p>
                            <h1 style="margin:12px 0 8px;color:#1B4332;font-size:22px;">Kode verifikasi email</h1>
                            <p style="margin:0 0 20px;color:#57534e;font-size:14px;line-height:1.6;">
                                Shalom {{ $user->name }}. Gunakan kode OTP berikut untuk mengaktifkan akun portal jemaat:
                            </p>
                            <p style="margin:0 0 20px;text-align:center;letter-spacing:8px;font-size:32px;font-weight:bold;color:#1B4332;">
                                {{ $code }}
                            </p>
                            <p style="margin:0;color:#78716c;font-size:12px;line-height:1.5;">
                                Kode berlaku 10 menit. Jangan bagikan kode ini kepada siapa pun, termasuk pengurus gereja.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
