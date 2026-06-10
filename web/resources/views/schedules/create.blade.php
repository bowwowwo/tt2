<x-layout>
    <div class="container py-2 px-4">
        <div class="py-3 px-4">
            <h1 class="display-5 mb-4">Add Schedule</h1>

            @if($errors->any())
                <div class="alert alert-danger mb-3">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('schedules.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">
                        Schedule Name
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="form-control border-dark @error('name') is-invalid @enderror"
                        value="{{ old('name') }}"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                {{-- Collaboration checkbox --}}
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
                        Collaboration schedule
                    </label>
                </div>

                @php
                    $oldCollaborators = old('collaborator_user_ids', ['']);
                @endphp

                {{-- Collaborator user IDs --}}
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

                <button type="submit" class="btn btn-dark">
                    Create Schedule
                </button>

                <a href="{{ route('schedules.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
            </form>
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