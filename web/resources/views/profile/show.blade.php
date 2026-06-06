<x-layout>
    <div class="container py-4">
        <div class="mb-4">
            <h1 class="display-5">My Profile</h1>

            <a href="{{ route('events.index') }}" class="btn btn-outline-dark btn-sm">
                Back to events
            </a>
        </div>

        <div class="card border border-2 border-dark mb-4">
            <div class="card-body">
                <h3 class="card-title mb-4">
                    Profile Details
                </h3>

                <div class="mb-3">
                    <strong>Name:</strong>
                    <span>{{ $user->name }}</span>
                </div>

                <div class="mb-3">
                    <strong>Email:</strong>
                    <span>{{ $user->email }}</span>
                </div>

                <div class="mb-3">
                    <strong>User ID:</strong>
                    <span>{{ $user->id }}</span>
                </div>

                <div class="alert alert-info mb-0">
                    Give your User ID to other users if they want to add you as a collaborator.
                </div>
            </div>
        </div>

        <div class="card border border-2 border-dark">
            <div class="card-body">
                <h3 class="card-title mb-4">
                    Schedules Created
                </h3>

                @forelse($createdSchedules as $schedule)
                    <div class="border border-dark rounded p-3 mb-3">
                        <div class="fw-semibold fs-5">
                            {{ $schedule->name }}
                        </div>

                        <div class="small text-muted">
                            Schedule ID: {{ $schedule->id }}
                        </div>

                        <div class="small">
                            Shared:
                            {{ $schedule->is_shared ? 'Yes' : 'No' }}
                        </div>

                        <div class="small">
                            Created:
                            {{ $schedule->created_at?->format('F j, Y') }}
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">
                        You have not created any schedules yet.
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-layout>