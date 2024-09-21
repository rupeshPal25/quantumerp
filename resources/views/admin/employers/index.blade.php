@extends('layouts.app')
@section('content')


<!-- Button to Open the Modal -->
<div class="d-flex justify-content-end">
    <button type="button" class="btn btn-success" data-toggle="modal" data-target="#addEmployerModal">
        Add Employer
    </button>
</div>
<!-- Employer List Table -->
<h1>Employer List</h1>
<table class="table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Image</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="employer-list">
        @foreach($employers as $employer)
        <tr>
            <td>{{ $employer->name }}</td>
            <td>{{ $employer->email }}</td>
            <td><img src="{{ asset('images/' . $employer->image) }}" alt="{{ $employer->name }}" width="50"></td>
            <td>
            <a href="{{ route('employers.show', $employer->id) }}">View Details</a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<!-- Add Employer Modal -->
<div class="modal fade" id="addEmployerModal" tabindex="-1" role="dialog" aria-labelledby="addEmployerModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmployerModalLabel">Add Employer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="employer-form" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="name">Employer Name</label>
                        <input type="text" name="name" class="form-control" placeholder="Employer Name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Employer Email</label>
                        <input type="email" name="email" class="form-control" placeholder="Employer Email" required>
                    </div>
                    <div class="form-group">
                        <label for="image">Employer Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*" required>
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Add Employer</button>
                </form>
            </div>
        </div>
    </div>
</div>


@endsection

@push('script')

<script>
    $(document).ready(function() {
    $('#employer-form').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);

        $.ajax({
            type: 'POST',
            url: '{{ url('admin/employers') }}',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Append the new employer to the list
                $('#employer-list').append(`
                    <tr>
                        <td>${response.name}</td>
                        <td>${response.email}</td>
                        <td><img src="/images/${response.image}" alt="${response.name}" width="50"></td>
                        <td><a href="/admin/employers/${response.id}" class="btn btn-primary btn-sm">View Details</a></td>
                    </tr>
                `);
                $('#addEmployerModal').modal('hide');  // Close the modal
                $('#employer-form')[0].reset();  // Reset the form fields
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
});

       
</script>

@endpush
