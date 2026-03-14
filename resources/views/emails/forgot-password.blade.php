<!DOCTYPE html>
<html lang="sr">

<head>
    <meta charset="UTF-8">
    <title>Resetovanje lozinke</title>
</head>

<body style="margin:0;padding:0;background:#f7f7f7;font-family:Arial,sans-serif;">
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
                    <tr>
                        <td align="center" style="padding:6px 0 16px;">
                            <h2 style="margin-top:0;color:#222;">Resetovanje lozinke</h2>

                            <p style="font-size:16px;color:#444;line-height:1.6;">
                                Zatražili ste promenu lozinke za vaš nalog.
                            </p>

                            <p style="font-size:16px;color:#444;line-height:1.6;">
                                Kliknite na dugme ispod da postavite novu lozinku.
                            </p>

                            <p style="margin:30px 0;">
                                <a href="{{ $resetUrl }}"
                                    style="display:inline-block;padding:14px 24px;background:#6c0101;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:bold;">
                                    Promenite lozinku
                                </a>
                            </p>

                            <p style="font-size:14px;color:#666;line-height:1.6;">
                                Ovaj link važi 60 minuta.
                            </p>

                            <p style="margin:20px;font-size:13px;line-height:1.6;color:#6b7280;">
                                Ako klik na dugme ne radi, kopirajte ovaj link u browser:
                            </p>
                            <p
                                style="margin:8px 0 0;font-size:12px;line-height:1.6;color:#6b7280;word-break:break-all;">
                                <a href="{{ $resetUrl }}" target="_blank" style="text-decoration:underline;">{{
                                    $resetUrl }}</a>
                            </p>

                            <p style="font-size:14px;color:#666;line-height:1.6;">
                                Ako niste vi zatražili promenu lozinke, slobodno ignorišite ovu poruku.
                            </p>
                        </td>
                    </tr>
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