<!doctype html>
<html lang="sr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Potvrdite email</title>
</head>

<body style="margin:0;padding:0;background:#f6f7f9;font-family:Arial,Helvetica,sans-serif;color:#111827;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
        style="background:#f6f7f9;padding:24px 12px;">
        <tr>
            <td align="center">
                <!-- Container -->
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="max-width:560px;">
                    <!-- Logo / Brand -->
                    <tr>
                        <td align="center" style="padding:6px 0 16px;">
                            {{-- Option A: image logo (recommended) --}}
                            @if(!empty($logoUrl))
                            <img src="{{ $logoUrl }}" alt="{{ $appName }}"
                                style="height:44px;max-width:220px;display:block;">
                            @else
                            {{-- Option B: text fallback --}}
                            <div
                                style="display:inline-block;padding:10px 14px;border-radius:12px;background:#ffffff;border:1px solid #e5e7eb;">
                                <span style="font-weight:700;font-size:14px;color:#111827;">{{ $appName }}</span>
                            </div>
                            @endif
                        </td>
                    </tr>

                    <!-- Card -->
                    <tr>
                        <td style="background:#ffffff;border:1px solid #e5e7eb;border-radius:16px;padding:22px;">
                            <h1 style="margin:20px;font-size:22px;line-height:1.25;color:#111827;">
                                Dobrodošli na hocupozoriste.rs
                            </h1>

                            <p style="margin:20px;font-size:15px;line-height:1.6;color:#374151;">
                                Zdravo{{ !empty($userName) ? ', ' . e($userName) : '' }}!
                                <br><br>
                                Kliknite dugme ispod da aktivirate nalog. Nakon verifikacije možete da se ulogujete.
                            </p>

                            <!-- Button -->
                            <table role="presentation" cellpadding="0" cellspacing="0" style="margin:20px;">
                                <tr>
                                    <td align="center" bgcolor="#6c0101" style="border-radius:12px;">
                                        <a href="{{ $url }}" target="_blank"
                                            style="display:inline-block;padding:12px 18px;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;">
                                            Aktivirajte nalog
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:20px;font-size:13px;line-height:1.6;color:#6b7280;">
                                Ako klik dugme ne radi, kopirajte ovaj link u browser:
                            </p>
                            <p
                                style="margin:8px 0 0;font-size:12px;line-height:1.6;color:#6b7280;word-break:break-all;">
                                <a href="{{ $url }}" target="_blank" style="color:#6c0101;text-decoration:underline;">{{
                                    $url }}</a>
                            </p>

                            <hr style="border:none;border-top:1px solid #eef2f7;margin:18px 0;">

                            <p style="margin:0;font-size:12px;line-height:1.6;color:#9ca3af;">
                                Ako niste vi napravili nalog, slobodno ignorišite ovaj email.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding:16px 0 0;font-size:12px;color:#9ca3af;">
                            © {{ date('Y') }} {{ $appName }}
                        </td>
                    </tr>
                </table>
                <!-- /Container -->
            </td>
        </tr>
    </table>
</body>

</html>