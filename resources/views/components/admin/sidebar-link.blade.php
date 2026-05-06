@props(['active' => false, 'icon', 'badge' => null])

@php
$classes = $active
    ? 'flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-primary-50 to-primary-100/40 text-primary-700 font-medium transition-all duration-200 border border-primary-100 shadow-subtle group'
    : 'flex items-center gap-3 px-4 py-3 rounded-xl text-gray-600 hover:bg-gray-50 hover:text-primary-600 font-medium transition-all duration-200 border border-transparent hover:border-gray-100 group';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} {{ $active ? 'aria-current="page"' : '' }}>
    @if(isset($icon))
        <i class="{{ $active ? 'ph-bold' : 'ph' }} {{ $icon }} w-5 h-5 shrink-0 {{ $active ? 'text-primary-600' : 'text-gray-500' }} group-hover:text-primary-600 transition-colors" aria-hidden="true"></i>
    @endif
    <span class="text-sm font-medium flex-1">{{ $slot }}</span>
    
    @if($badge)
        <span class="px-2 py-0.5 rounded-full bg-primary-100 text-primary-700 text-[10px] font-black min-w-[1.25rem] text-center">
            {{ $badge }}
        </span>
    @endif
</a>
