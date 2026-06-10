<x-layout>
    <div class="container py-4">
        <h1 class="display-5 mb-4">Invites</h1>

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

        <div class="card border border-2 border-dark mb-4">
            <div class="card-body">
                <h3 class="mb-3">Event Invites</h3>

                @forelse($eventInvites as $invite)
                    <div class="border border-dark rounded p-3 mb-3">
                        <div class="fw-bold fs-5">
                            {{ $invite->event->title }}
                        </div>

                        <div class="small">
                            Schedule:
                            {{ $invite->event->schedule?->name ?? 'No schedule' }}
                        </div>

                        <div class="small">
                            Created by:
                            {{ $invite->event->creator?->name ?? 'Unknown' }}
                        </div>

                        <div class="small">
                            Starts:
                            {{ $invite->event->start_time?->format('F j, Y g:i a') }}
                        </div>

                        @if($invite->event->description)
                            <div class="mt-2">
                                {{ $invite->event->description }}
                            </div>
                        @endif

                        <div class="d-flex gap-2 mt-3">
                            <form
                                method="POST"
                                action="{{ route('invites.events.accept', $invite) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="btn btn-success btn-sm">
                                    Accept
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('invites.events.decline', $invite) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    Decline
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">
                        You have no pending event invites.
                    </p>
                @endforelse
            </div>
        </div>

        <div class="card border border-2 border-dark">
            <div class="card-body">
                <h3 class="mb-3">Schedule Invites</h3>

                @forelse($scheduleInvites as $invite)
                    <div class="border border-dark rounded p-3 mb-3">
                        <div class="fw-bold fs-5">
                            {{ $invite->schedule->name }}
                        </div>

                        <div class="small">
                            Owner:
                            {{ $invite->schedule->owner?->name ?? 'Unknown' }}
                        </div>

                        <div class="small">
                            Schedule ID:
                            {{ $invite->schedule->id }}
                        </div>

                        <div class="d-flex gap-2 mt-3">
                            <form
                                method="POST"
                                action="{{ route('invites.schedules.accept', $invite) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="btn btn-success btn-sm">
                                    Accept
                                </button>
                            </form>

                            <form
                                method="POST"
                                action="{{ route('invites.schedules.decline', $invite) }}"
                            >
                                @csrf
                                @method('PATCH')

                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                    Decline
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">
                        You have no pending schedule invites.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-layout>