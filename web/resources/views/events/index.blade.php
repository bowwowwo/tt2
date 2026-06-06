<x-layout>
    <div class="py-2 px-4">

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Close"
                ></button>
            </div>
        @endif

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <h1 class="display-5 mb-0">Upcoming events</h1>

                {{-- Select schedule --}}
                <div class="dropdown pt-3">
                    <button
                        class="btn btn-outline-dark dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        {{ $selectedSchedule?->name ?? 'Change schedule' }}
                    </button>

                    <ul class="dropdown-menu">
                        @forelse($schedules as $schedule)
                            <li>
                                <a
                                    class="dropdown-item {{ $selectedScheduleId === $schedule->id ? 'active' : '' }}"
                                    href="{{ route('events.index', ['schedule' => $schedule->id]) }}"
                                >
                                    {{ $schedule->name }}

                                    @if($schedule->owner_id === auth()->id())
                                        <span class="text-muted small">(Owner)</span>
                                    @elseif($schedule->is_shared)
                                        <span class="text-muted small">(Shared)</span>
                                    @endif
                                </a>
                            </li>
                        @empty
                            <li>
                                <span class="dropdown-item text-muted">
                                    No schedules available
                                </span>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>

            {{-- Create event button --}}
            @if($selectedScheduleId)
                <a
                    href="{{ route('events.create', ['schedule' => $selectedScheduleId]) }}"
                    class="btn btn-dark"
                >
                    Create event
                </a>
            @else
                <a
                    href="#"
                    class="btn btn-dark disabled"
                    aria-disabled="true"
                    tabindex="-1"
                >
                    Create event
                </a>
            @endif
        </div>

        <div class="d-flex flex-wrap gap-3 align-content-start pt-4">
            @forelse($events as $event)
                <x-event-card
                    :event="$event"
                    class="flex-grow-0"
                    style="width: 320px;"
                />
            @empty
                <p class="text-muted">No upcoming events for this schedule.</p>
            @endforelse
        </div>
    </div>
</x-layout>