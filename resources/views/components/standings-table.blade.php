@props(['standings', 'clubTeam' => null])

<div class="overflow-hidden rounded-2xl border border-line shadow-lg max-mobile:w-full max-mobile:overflow-x-auto">
    <table class="w-full border-collapse max-mobile:w-full max-mobile:table-fixed">
        <thead>
            <tr class="bg-ink text-white">
                <th class="w-9 px-4 py-4 text-left text-10 font-bold uppercase tracking-label text-white/50 max-mobile:!w-[1.8rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">#</th>
                <th class="px-4 py-4 text-left text-10 font-bold uppercase tracking-label text-white/50 max-mobile:overflow-hidden max-mobile:text-ellipsis max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">TÝM</th>
                <th class="w-[42px] px-4 py-4 text-center text-10 font-bold uppercase tracking-label text-white/50 max-mobile:!w-[1.9rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]"><span class="group relative cursor-help border-b border-dotted border-white/40">Z<span role="tooltip" class="pointer-events-none absolute left-1/2 top-full z-20 mt-2 -translate-x-1/2 whitespace-nowrap rounded border border-line bg-white px-2 py-1 text-[11px] font-semibold normal-case tracking-normal text-ink opacity-0 shadow-lg transition-opacity duration-100 group-hover:opacity-100">Odehrané zápasy</span></span></th>
                <th class="w-[42px] px-4 py-4 text-center text-10 font-bold uppercase tracking-label text-white/50 max-mobile:!w-[1.9rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]"><span class="group relative cursor-help border-b border-dotted border-white/40">V<span role="tooltip" class="pointer-events-none absolute left-1/2 top-full z-20 mt-2 -translate-x-1/2 whitespace-nowrap rounded border border-line bg-white px-2 py-1 text-[11px] font-semibold normal-case tracking-normal text-ink opacity-0 shadow-lg transition-opacity duration-100 group-hover:opacity-100">Výhry</span></span></th>
                <th class="w-[42px] px-4 py-4 text-center text-10 font-bold uppercase tracking-label text-white/50 max-mobile:!w-[1.9rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]"><span class="group relative cursor-help border-b border-dotted border-white/40">VP<span role="tooltip" class="pointer-events-none absolute left-1/2 top-full z-20 mt-2 -translate-x-1/2 whitespace-nowrap rounded border border-line bg-white px-2 py-1 text-[11px] font-semibold normal-case tracking-normal text-ink opacity-0 shadow-lg transition-opacity duration-100 group-hover:opacity-100">Výhry v prodloužení</span></span></th>
                <th class="w-[42px] px-4 py-4 text-center text-10 font-bold uppercase tracking-label text-white/50 max-mobile:!w-[1.9rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]"><span class="group relative cursor-help border-b border-dotted border-white/40">P<span role="tooltip" class="pointer-events-none absolute left-1/2 top-full z-20 mt-2 -translate-x-1/2 whitespace-nowrap rounded border border-line bg-white px-2 py-1 text-[11px] font-semibold normal-case tracking-normal text-ink opacity-0 shadow-lg transition-opacity duration-100 group-hover:opacity-100">Prohry</span></span></th>
                <th class="w-[42px] px-4 py-4 text-center text-10 font-bold uppercase tracking-label text-white/50 max-mobile:!w-[1.9rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]"><span class="group relative cursor-help border-b border-dotted border-white/40">PP<span role="tooltip" class="pointer-events-none absolute left-1/2 top-full z-20 mt-2 -translate-x-1/2 whitespace-nowrap rounded border border-line bg-white px-2 py-1 text-[11px] font-semibold normal-case tracking-normal text-ink opacity-0 shadow-lg transition-opacity duration-100 group-hover:opacity-100">Prohry v prodloužení</span></span></th>
                <th class="w-[42px] px-4 py-4 text-center text-10 font-bold uppercase tracking-label text-white/50 max-mobile:!w-[1.9rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]"><span class="group relative cursor-help border-b border-dotted border-white/40">VB<span role="tooltip" class="pointer-events-none absolute left-1/2 top-full z-20 mt-2 -translate-x-1/2 whitespace-nowrap rounded border border-line bg-white px-2 py-1 text-[11px] font-semibold normal-case tracking-normal text-ink opacity-0 shadow-lg transition-opacity duration-100 group-hover:opacity-100">Vstřelené branky</span></span></th>
                <th class="w-[42px] px-4 py-4 text-center text-10 font-bold uppercase tracking-label text-white/50 max-mobile:!w-[1.9rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]"><span class="group relative cursor-help border-b border-dotted border-white/40">IB<span role="tooltip" class="pointer-events-none absolute left-1/2 top-full z-20 mt-2 -translate-x-1/2 whitespace-nowrap rounded border border-line bg-white px-2 py-1 text-[11px] font-semibold normal-case tracking-normal text-ink opacity-0 shadow-lg transition-opacity duration-100 group-hover:opacity-100">Inkasované branky</span></span></th>
                <th class="w-[42px] px-4 py-4 text-center text-10 font-bold uppercase tracking-label text-white/50 max-mobile:!w-[1.9rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]"><span class="group relative cursor-help border-b border-dotted border-white/40">B<span role="tooltip" class="pointer-events-none absolute left-1/2 top-full z-20 mt-2 -translate-x-1/2 whitespace-nowrap rounded border border-line bg-white px-2 py-1 text-[11px] font-semibold normal-case tracking-normal text-ink opacity-0 shadow-lg transition-opacity duration-100 group-hover:opacity-100">Body</span></span></th>
            </tr>
        </thead>
        <tbody>
            @forelse($standings as $row)
                @php
                    $team = $row->team ?? ($row['team'] ?? null);
                    $teamId = $row->team_id ?? ($row['team_id'] ?? null);
                    $isClub = $clubTeam && (int)$clubTeam->id === (int)$teamId;
                    $get = fn($key) => data_get($row, $key, 0);
                @endphp
                <tr class="{{ $loop->last ? '' : 'border-b border-b-line' }} {{ $isClub ? 'border-b-orange-css/15 bg-[rgba(245,120,0,0.05)]' : '' }}">
                    <td class="px-4 py-3.5 text-sm {{ $isClub ? 'font-black text-orange' : 'font-bold text-muted' }} max-mobile:!w-[1.8rem] max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">
                        {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                    </td>
                    <td class="px-4 py-3.5 max-mobile:overflow-hidden max-mobile:text-ellipsis max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">
                        <div class="flex items-center gap-3 max-mobile:min-w-0 max-mobile:gap-[0.35rem]">
                            <x-team-badge :team="$team" />
                            <span class="{{ $isClub ? 'font-black text-wine' : 'font-semibold' }} text-sm max-mobile:overflow-hidden max-mobile:text-ellipsis">
                                {{ $team?->name ?? data_get($row, 'team_name') }}
                            </span>
                        </div>
                    </td>
                    @foreach(['games_played', 'wins', 'overtime_wins', 'losses', 'overtime_losses'] as $field)
                        <td class="px-4 py-3.5 text-center text-sm {{ $isClub ? 'font-bold' : '' }} max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">
                            {{ $get($field) }}
                        </td>
                    @endforeach
                    <td class="px-4 py-3.5 text-center text-sm {{ $isClub ? 'font-black text-orange' : 'font-bold' }} max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">
                        {{ $get('goals_for') }}
                    </td>
                    <td class="px-4 py-3.5 text-center text-sm {{ $isClub ? 'font-black text-orange' : 'font-bold' }} max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">
                        {{ $get('goals_against') }}
                    </td>
                    <td class="px-4 py-3.5 text-center text-sm {{ $isClub ? 'font-black text-orange' : 'font-bold' }} max-mobile:whitespace-nowrap max-mobile:!px-[0.3rem]">
                        {{ $get('points') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="px-4 py-8 text-center text-sm text-muted">
                        Tabulka zatím nemá žádná data.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
