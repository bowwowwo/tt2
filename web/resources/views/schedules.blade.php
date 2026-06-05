<x-layout>
    <div class="py-3 px-4">

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
            <div class="d-flex align-items-center flex-wrap gap-3">
                <h1 class="display-5 mb-0">Schedules</h1>

                {{-- Schedule selector --}}
                <div class="dropdown pt-2">
                    <button
                        class="btn btn-outline-dark dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        {{ $selectedSchedule?->name ?? 'Select schedule' }}
                    </button>

                    <ul class="dropdown-menu">
                        @forelse($schedules as $schedule)
                            <li>
                                <a
                                    class="dropdown-item {{ $selectedScheduleId === $schedule->id ? 'active' : '' }}"
                                    href="{{ route('schedules.index', [
                                        'schedule' => $schedule->id,
                                        'month' => $month->format('Y-m'),
                                    ]) }}"
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

            {{-- Month navigation --}}
            <div class="d-flex align-items-center gap-2 pt-2">
                <a
                    href="{{ route('schedules.index', [
                        'schedule' => $selectedScheduleId,
                        'month' => $month->copy()->subMonth()->format('Y-m'),
                    ]) }}"
                    class="btn btn-outline-dark"
                >
                    Previous
                </a>

                <div class="fw-semibold fs-5 px-2">
                    {{ $month->format('F Y') }}
                </div>

                <a
                    href="{{ route('schedules.index', [
                        'schedule' => $selectedScheduleId,
                        'month' => $month->copy()->addMonth()->format('Y-m'),
                    ]) }}"
                    class="btn btn-outline-dark"
                >
                    Next
                </a>
            </div>
        </div>

        @if(!$selectedScheduleId)
            <div class="alert alert-warning">
                You do not have any schedules yet.
            </div>
        @else

            {{-- Calendar --}}
            <div class="border border-dark border-2">

                {{-- Weekday headings --}}
                <div class="row g-0 text-center fw-bold border-bottom border-dark">
                    <div class="col py-2 border-end border-dark">Sun</div>
                    <div class="col py-2 border-end border-dark">Mon</div>
                    <div class="col py-2 border-end border-dark">Tue</div>
                    <div class="col py-2 border-end border-dark">Wed</div>
                    <div class="col py-2 border-end border-dark">Thu</div>
                    <div class="col py-2 border-end border-dark">Fri</div>
                    <div class="col py-2">Sat</div>
                </div>

                {{-- Calendar weeks --}}
                @foreach($calendarDays->chunk(7) as $week)
                    <div class="row g-0">
                        @foreach($week as $day)
                            @php
                                $dateKey = $day->toDateString();
                                $dayEvents = $eventsByDate->get($dateKey, collect());
                                $isCurrentMonth = $day->month === $month->month;
                                $isToday = $day->isToday();
                            @endphp

                            <div
                                class="col border-end border-bottom border-dark p-2 {{ !$isCurrentMonth ? 'bg-light text-muted' : '' }}"
                                style="min-height: 140px;"
                            >
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span
                                        class="fw-bold {{ $isToday ? 'bg-dark text-white rounded px-2' : '' }}"
                                    >
                                        {{ $day->day }}
                                    </span>
                                </div>

                                @forelse($dayEvents as $event)
                                    <div class="mb-2 p-2 rounded border border-dark bg-info-subtle small">
                                        <div class="fw-semibold">
                                            {{ $event->title }}
                                        </div>

                                        <div>
                                            {{ $event->start_time->format('g:i a') }}
                                        </div>

                                        @if($event->collaboration)
                                            <div class="fst-italic">
                                                Collaboration
                                            </div>
                                        @endif
                                    </div>
                                @empty
                                    {{-- No events --}}
                                @endforelse
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-layout>