<x-layout>
    <div class="container py-4">
        <div class="mb-4">
            <h1 class="display-5">Edit Event</h1>

            <a
                href="{{ route('events.index', ['schedule' => $event->schedule_id]) }}"
                class="btn btn-outline-dark btn-sm"
            >
                Back to events
            </a>
        </div>

        <div class="card border border-2 border-dark">
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('events.update', $event) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="schedule_id" class="form-label">
                            Schedule
                        </label>

                        <select
                            name="schedule_id"
                            id="schedule_id"
                            class="form-select border-dark @error('schedule_id') is-invalid @enderror"
                            required
                        >
                            @foreach($schedules as $schedule)
                                <option
                                    value="{{ $schedule->id }}"
                                    {{ old('schedule_id', $event->schedule_id) == $schedule->id ? 'selected' : '' }}
                                >
                                    {{ $schedule->name }}

                                    @if($schedule->owner_id === auth()->id())
                                        (Owner)
                                    @elseif($schedule->is_shared)
                                        (Shared)
                                    @endif
                                </option>
                            @endforeach
                        </select>

                        @error('schedule_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="title" class="form-label">
                            Event title
                        </label>

                        <input
                            type="text"
                            name="title"
                            id="title"
                            class="form-control border-dark @error('title') is-invalid @enderror"
                            value="{{ old('title', $event->title) }}"
                            required
                        >

                        @error('title')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Description
                        </label>

                        <textarea
                            name="description"
                            id="description"
                            class="form-control border-dark @error('description') is-invalid @enderror"
                            rows="4"
                        >{{ old('description', $event->description) }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_time" class="form-label">
                                Start time
                            </label>

                            <input
                                type="datetime-local"
                                name="start_time"
                                id="start_time"
                                class="form-control border-dark @error('start_time') is-invalid @enderror"
                                value="{{ old('start_time', $event->start_time?->format('Y-m-d\TH:i')) }}"
                                required
                            >

                            @error('start_time')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="end_time" class="form-label">
                                End time
                            </label>

                            <input
                                type="datetime-local"
                                name="end_time"
                                id="end_time"
                                class="form-control border-dark @error('end_time') is-invalid @enderror"
                                value="{{ old('end_time', $event->end_time?->format('Y-m-d\TH:i')) }}"
                            >

                            @error('end_time')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                    </div>

                    @php
                        $collaborationChecked = old('collaboration', $event->collaboration);

                        $savedCollaborators = $event->participants
                            ->where('user_id', '!=', auth()->id())
                            ->pluck('user_id')
                            ->values()
                            ->all();

                        $oldCollaborators = old('collaborator_user_ids', count($savedCollaborators) ? $savedCollaborators : ['']);
                    @endphp

                    <div class="form-check mb-3">
                        <input
                            type="checkbox"
                            name="collaboration"
                            id="collaboration"
                            class="form-check-input"
                            value="1"
                            {{ $collaborationChecked ? 'checked' : '' }}
                        >

                        <label for="collaboration" class="form-check-label">
                            Collaboration event
                        </label>
                    </div>

                    <div
                        class="mb-3"
                        id="collaboratorsBox"
                        style="{{ $collaborationChecked ? '' : 'display: none;' }}"
                    >
                        <label class="form-label">
                            Collaborator User IDs
                        </label>

                        <div id="collaboratorsList">
                            @foreach($oldCollaborators as $collaboratorId)
                                <div class="input-group mb-2 collaborator-row">
                                    <input
                                        type="text"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        name="collaborator_user_ids[]"
                                        class="form-control border-dark"
                                        value="{{ $collaboratorId }}"
                                        placeholder="Enter user ID"
                                    >

                                    <button
                                        type="button"
                                        class="btn btn-outline-danger remove-collaborator"
                                    >
                                        Remove
                                    </button>
                                </div>
                            @endforeach
                        </div>

                        @error('collaborator_user_ids')
                            <div class="text-danger small">
                                {{ $message }}
                            </div>
                        @enderror

                        @error('collaborator_user_ids.*')
                            <div class="text-danger small">
                                {{ $message }}
                            </div>
                        @enderror

                        <button
                            type="button"
                            class="btn btn-outline-dark btn-sm mt-2"
                            id="addCollaborator"
                        >
                            + Add collaborator
                        </button>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-dark">
                            Update event
                        </button>

                        <a
                            href="{{ route('events.index', ['schedule' => $event->schedule_id]) }}"
                            class="btn btn-outline-secondary"
                        >
                            Cancel
                        </a>
                    </div>
                </form>

                @if($event->created_by === auth()->id())
                    <hr class="border-dark">

                    <form
                        method="POST"
                        action="{{ route('events.destroy', $event) }}"
                        onsubmit="return confirm('Are you sure you want to delete this event? This cannot be undone.');"
                    >
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">
                            Delete event
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const collaborationCheckbox = document.getElementById('collaboration');
            const collaboratorsBox = document.getElementById('collaboratorsBox');
            const collaboratorsList = document.getElementById('collaboratorsList');
            const addCollaboratorButton = document.getElementById('addCollaborator');

            function toggleCollaboratorsBox() {
                if (collaborationCheckbox.checked) {
                    collaboratorsBox.style.display = 'block';
                } else {
                    collaboratorsBox.style.display = 'none';

                    const inputs = collaboratorsBox.querySelectorAll('input[name="collaborator_user_ids[]"]');
                    inputs.forEach(input => input.value = '');
                }
            }

            function createCollaboratorRow() {
                const row = document.createElement('div');
                row.classList.add('input-group', 'mb-2', 'collaborator-row');

                row.innerHTML = `
                    <input
                        type="text"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        name="collaborator_user_ids[]"
                        class="form-control border-dark"
                        placeholder="Enter user ID"
                    >

                    <button
                        type="button"
                        class="btn btn-outline-danger remove-collaborator"
                    >
                        Remove
                    </button>
                `;

                collaboratorsList.appendChild(row);
            }

            addCollaboratorButton.addEventListener('click', function () {
                createCollaboratorRow();
            });

            collaboratorsList.addEventListener('click', function (event) {
                if (event.target.classList.contains('remove-collaborator')) {
                    const rows = collaboratorsList.querySelectorAll('.collaborator-row');

                    if (rows.length > 1) {
                        event.target.closest('.collaborator-row').remove();
                    } else {
                        event.target.closest('.collaborator-row').querySelector('input').value = '';
                    }
                }
            });

            collaborationCheckbox.addEventListener('change', toggleCollaboratorsBox);

            toggleCollaboratorsBox();
        });
    </script>
</x-layout>