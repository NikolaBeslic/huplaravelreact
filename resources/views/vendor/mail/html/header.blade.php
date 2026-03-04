<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
            <img src="https://hocupozoriste.rs/slike/logo.png" class="logo" alt="Hoću u pozorište Logo">
            @else
            {{ $slot }}
            @endif
        </a>
    </td>
</tr>