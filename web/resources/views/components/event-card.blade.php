@props([
    'event',
    // 'pink', 'green', 'blue', etc.
    'variant' => null,
])

@php
    $colors = [
        'green'  => 'bg-success-subtle',
        'pink'   => 'bg-danger-subtle',
        'blue'   => 'bg-primary-subtle',
        'yellow' => 'bg-warning-subtle',
        'cyan'   => 'bg-info-subtle',
    ];

    $colorKeys = array_keys($colors); // non-random colors :p

    $bgClass = $variant && isset($colors[$variant])
        ? $colors[$variant]
        : $colors[$colorKeys[$event->id % count($colorKeys)]];

    $timeText = $event->start_time
        ? $event->start_time->format('g:i a')
        : null;

    $desc = $event->description
        ? \Illuminate\Support\Str::limit($event->description, 120)
        : null;
@endphp

<div {{ $attributes->merge([
        'class' => "p-3 border border-2 border-dark $bgClass",
        'style' => 'min-height: 380px;',
    ]) }}>
    <div class="fs-2 fw-semibold lh-1">
        {{ $event->title }}
    </div>

    @if (!empty($event->subtitle))
        <div class="fs-5">{{ $event->subtitle }}</div>
    @endif

    @if ($timeText)
        <div class="fs-5">{{ $timeText }}</div>
    @endif

    @if ($desc)
        <div class="mt-2 small">
            {{ $desc }}
        </div>
    @endif

    <hr class="border-dark opacity-100 mt-3 mb-2">

    <div class="small fst-italic">
        Collaboration:
        @if ($event->collaboration)
            @if ($event->relationLoaded('participants') && $event->participants->count())
                {{ $event->participants->pluck('user.name')->filter()->join(', ') }}
            @else
                Yes
            @endif
        @else
            No
        @endif
    </div>
</div>