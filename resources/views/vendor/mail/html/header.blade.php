@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block; mix-blend-mode: multiply;">
            <img src="{{ config('app.url') }}/logo.png" class="logo" alt="{{ config('app.name') }}">
        </a>
    </td>
</tr>
