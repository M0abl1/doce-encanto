@props(['color' => '#C2416B', 'title' => null, 'kind' => 'bolo'])
@php
    // Ilustração vetorial (bolo, torta doce ou torta salgada) gerada a partir da cor cadastrada.
    // Substitui fotos de terceiros: nenhuma imagem externa é usada.
    // O nome do item escolhe uma variação (andares e enfeite), para os cards não ficarem iguais.
    $hex = preg_match('/^#[0-9A-Fa-f]{6}$/', (string) $color) ? $color : '#C2416B';
    [$r, $g, $b] = sscanf($hex, '#%02x%02x%02x');
    $mix = fn (array $to, float $t) => sprintf('#%02x%02x%02x',
        (int) round($r + ($to[0] - $r) * $t), (int) round($g + ($to[1] - $g) * $t), (int) round($b + ($to[2] - $b) * $t));
    $white = [255, 255, 255];
    $cocoa = [90, 52, 38];
    $light = $mix($white, 0.55);
    $lighter = $mix($white, 0.82);
    $dark = $mix($cocoa, 0.55);      // massa: puxa para o marrom, nunca para o cinza
    $deep = $mix($cocoa, 0.75);
    $seed = crc32((string) ($title ?? $hex));
    $topper = $seed % 4;              // 0 cereja e vela, 1 morangos, 2 raspas, 3 confeitos
    $tiers = ($seed >> 3) % 3 === 0 ? 2 : 1;
    $id = 'a'.substr(md5($hex.$kind.($title ?? '')), 0, 8);
    $drip = 'Q176 112 170 104 Q164 96 158 108 Q152 120 146 106 Q140 96 132 104 Q124 114 116 102 Q110 94 102 108 Q96 118 90 104 Q84 94 76 104 Q68 114 64 102 Q60 96 58 102 Z';
@endphp
<svg viewBox="0 0 240 200" xmlns="http://www.w3.org/2000/svg" role="img" {{ $attributes->merge(['class' => 'h-full w-full']) }}>
    <title>{{ $title ?? 'Ilustração' }}</title>
    <defs>
        <linearGradient id="{{ $id }}-bg" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0" stop-color="{{ $lighter }}"/>
            <stop offset="1" stop-color="#fffaf3"/>
        </linearGradient>
    </defs>
    <rect width="240" height="200" fill="url(#{{ $id }}-bg)"/>
    <circle cx="{{ 170 + $seed % 50 }}" cy="38" r="46" fill="#ffffff" opacity=".45"/>
    <circle cx="30" cy="170" r="38" fill="#ffffff" opacity=".35"/>
    <ellipse cx="120" cy="166" rx="86" ry="12" fill="#2f1f18" opacity=".08"/>
    <rect x="44" y="152" width="152" height="10" rx="5" fill="#ffffff"/>
    <rect x="44" y="158" width="152" height="4" rx="2" fill="#e9ddd0"/>

    @if ($kind === 'bolo')
        {{-- andar de baixo --}}
        <rect x="58" y="88" width="124" height="66" rx="10" fill="{{ $dark }}"/>
        <rect x="58" y="112" width="124" height="8" fill="{{ $light }}"/>
        <rect x="58" y="134" width="124" height="7" fill="{{ $light }}"/>
        <path d="M58 98 Q58 84 72 84 H168 Q182 84 182 98 V104 {{ $drip }}" fill="{{ $hex }}"/>
        <path d="M70 88 H170" stroke="#ffffff" stroke-width="3" stroke-linecap="round" opacity=".35"/>
        @php($top = 84)
        @if ($tiers === 2)
            {{-- andar de cima --}}
            <rect x="84" y="50" width="72" height="38" rx="8" fill="{{ $dark }}"/>
            <rect x="84" y="68" width="72" height="6" fill="{{ $light }}"/>
            <path d="M84 58 Q84 48 94 48 H146 Q156 48 156 58 V62 Q151 68 146 62 Q141 56 136 64 Q130 72 124 62 Q118 54 112 64 Q106 72 100 62 Q94 54 90 62 Q86 68 84 62 Z" fill="{{ $hex }}"/>
            @php($top = 48)
        @endif
        @php($cx = 120)
        @switch($topper)
            @case(0)
                <path d="M{{ $cx }} {{ $top - 14 }} Q{{ $cx + 4 }} {{ $top - 26 }} {{ $cx + 12 }} {{ $top - 30 }}" stroke="#4a7a3a" stroke-width="2.5" fill="none" stroke-linecap="round"/>
                <circle cx="{{ $cx - 2 }}" cy="{{ $top - 8 }}" r="9" fill="#b0273b"/>
                <circle cx="{{ $cx - 5 }}" cy="{{ $top - 11 }}" r="2.5" fill="#ffffff" opacity=".7"/>
                @if ($tiers === 1)
                    <rect x="146" y="58" width="6" height="26" rx="2" fill="{{ $lighter }}" stroke="{{ $deep }}" stroke-width="1"/>
                    <path d="M149 46 Q154 52 149 57 Q144 52 149 46 Z" fill="#f2a33a"/>
                @endif
                @break
            @case(1)
                @foreach ([-26, 0, 26] as $dx)
                    <path d="M{{ $cx + $dx - 8 }} {{ $top - 4 }} Q{{ $cx + $dx }} {{ $top - 22 }} {{ $cx + $dx + 8 }} {{ $top - 4 }} Q{{ $cx + $dx }} {{ $top + 4 }} {{ $cx + $dx - 8 }} {{ $top - 4 }} Z" fill="#d8344f"/>
                    <path d="M{{ $cx + $dx - 6 }} {{ $top - 16 }} L{{ $cx + $dx }} {{ $top - 12 }} L{{ $cx + $dx + 6 }} {{ $top - 16 }}" stroke="#4a7a3a" stroke-width="3" fill="none" stroke-linecap="round"/>
                @endforeach
                @break
            @case(2)
                @foreach ([[-30, -8, -20], [-10, -14, 15], [12, -10, -10], [32, -6, 25]] as [$dx, $dy, $rot])
                    <rect x="{{ $cx + $dx - 4 }}" y="{{ $top + $dy - 12 }}" width="9" height="20" rx="1.5" fill="{{ $deep }}" transform="rotate({{ $rot }} {{ $cx + $dx }} {{ $top + $dy }})"/>
                @endforeach
                @break
            @default
                <g opacity=".95">
                    @foreach ([[-34, -4, '#f2a33a'], [-18, -7, '#ffffff'], [-2, -5, '#7cc4b5'], [14, -8, '#ffffff'], [30, -4, '#f2a33a'], [-26, -12, '#7cc4b5'], [6, -13, '#f2a33a'], [22, -12, '#ffffff']] as [$dx, $dy, $c])
                        <rect x="{{ $cx + $dx }}" y="{{ $top + $dy }}" width="7" height="2.6" rx="1.3" fill="{{ $c }}" transform="rotate({{ ($dx * 7) % 60 }} {{ $cx + $dx + 3 }} {{ $top + $dy + 1 }})"/>
                    @endforeach
                </g>
        @endswitch
    @elseif ($kind === 'torta')
        {{-- torta doce: base de massa, recheio e cobertura com fatia aberta --}}
        <path d="M46 150 L62 118 H178 L194 150 Z" fill="#d9a066"/>
        <path d="M52 150 L66 124 H174 L188 150 Z" fill="#c78a4f"/>
        <ellipse cx="120" cy="118" rx="60" ry="14" fill="{{ $hex }}"/>
        <ellipse cx="120" cy="115" rx="52" ry="9" fill="{{ $light }}" opacity=".55"/>
        @if ($topper % 2 === 0)
            <g fill="#ffffff">
                <circle cx="88" cy="114" r="6"/><circle cx="104" cy="109" r="6"/><circle cx="122" cy="107" r="6"/>
                <circle cx="140" cy="109" r="6"/><circle cx="156" cy="114" r="6"/>
            </g>
            <g fill="{{ $deep }}"><circle cx="104" cy="106" r="2.5"/><circle cx="140" cy="106" r="2.5"/><circle cx="122" cy="104" r="2.5"/></g>
        @else
            <path d="M74 116 Q120 96 166 116" stroke="{{ $deep }}" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="1 7"/>
            <path d="M84 120 Q120 104 156 120" stroke="#ffffff" stroke-width="3" fill="none" stroke-linecap="round" stroke-dasharray="1 7"/>
        @endif
        <path d="M150 150 L178 118 L194 150 Z" fill="#fffaf3"/>
        <path d="M150 150 L178 118" stroke="#c78a4f" stroke-width="3"/>
    @else
        {{-- torta salgada: massa dourada com treliça --}}
        <path d="M48 150 Q52 116 120 112 Q188 116 192 150 Z" fill="#d79a4e"/>
        <ellipse cx="120" cy="116" rx="70" ry="16" fill="#e8b66c"/>
        <ellipse cx="120" cy="116" rx="62" ry="11" fill="{{ $hex }}" opacity=".85"/>
        <g stroke="#f4cf8d" stroke-width="5" stroke-linecap="round">
            <path d="M76 110 L96 124"/><path d="M98 106 L122 126"/><path d="M122 105 L146 125"/><path d="M146 107 L166 121"/>
            <path d="M84 124 L104 106"/><path d="M110 127 L132 105"/><path d="M136 126 L158 108"/>
        </g>
        <path d="M166 92 Q172 84 168 76 M178 94 Q186 84 180 72" stroke="#cbb9a8" stroke-width="2.5" fill="none" stroke-linecap="round" opacity=".7"/>
    @endif
</svg>
