{{-- resources/views/events/partials/create-event-modal.blade.php --}}

<div
    class="modal fade"
    id="createEventModal"
    tabindex="-1"
    aria-labelledby="createEventModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border border-2 border-dark">
            <form method="POST" action="{{ route('events.store') }}">
                @csrf

                <div class="modal-header border-dark">
                    <h5 class="modal-title" id="createEventModalLabel">
                        Create Event
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>

                <div class="modal-body">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <input
                        type="hidden"
                        name="schedule_id"
                        value="{{ $selectedScheduleId }}"
                    >

                    <div class="mb-3">
                        <label for="title" class="form-label">
                            Event title
                        </label>

                        <input
                            type="text"
                            name="title"
                            id="title"
                            class="form-control border-dark @error('title') is-invalid @enderror"
                            value="{{ old('title') }}"
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
                            rows="3"
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="start_time" class="form-label">
                            Start time
                        </label>

                        <input
                            type="datetime-local"
                            name="start_time"
                            id="start_time"
                            class="form-control border-dark @error('start_time') is-invalid @enderror"
                            value="{{ old('start_time') }}"
                            required
                        >

                        @error('start_time')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="end_time" class="form-label">
                            End time
                        </label>

                        <input
                            type="datetime-local"
                            name="end_time"
                            id="end_time"
                            class="form-control border-dark @error('end_time') is-invalid @enderror"
                            value="{{ old('end_time') }}"
                        >

                        @error('end_time')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                        {{-- //^ checkbox --}}
                <div class="form-check mb-3">
                    <input
                        type="checkbox"
                        name="collaboration"
                        id="collaboration"
                        class="form-check-input"
                        value="1"
                        {{ old('collaboration') ? 'checked' : '' }}
                    >

                    <label for="collaboration" class="form-check-label">
                        Collaboration event
                    </label>
                </div>

                @php
                    $oldCollaborators = old('collaborator_user_ids', ['']);
                @endphp

                <div
                    class="mb-3"
                    id="collaboratorsBox"
                    style="{{ old('collaboration') ? '' : 'display: none;' }}"
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
                                    class="form-control border-dark @error('collaborator_user_ids.*') is-invalid @enderror"
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
                
                </div>

                <div class="modal-footer border-dark">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal"
                    >
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-dark">
                        Create event
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- reopen modal--}}
@if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = new bootstrap.Modal(document.getElementById('createEventModal'));
            modal.show();
        });
    </script>
@endif

{{-- //* checkbox extra script --}}
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