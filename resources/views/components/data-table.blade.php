@props(['headers' => []])

<div class="overflow-x-auto rounded-xl border border-[#DDD6FE]">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-[#F5F3FF] border-b border-[#DDD6FE]">
                @foreach ($headers as $header)
                    <th class="px-4 py-3 text-start text-xs font-semibold text-[#6B21A8] uppercase tracking-wider whitespace-nowrap">
                        {{ $header }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-[#DDD6FE] bg-white">
            {{ $slot }}
        </tbody>
    </table>
</div>
