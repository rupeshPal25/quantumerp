@extends('layouts.app')

@section('content')
<div class="container">
    <div class="container">
        <div class="card">
            <h2>Employer Details</h2>
        </div>
        <div class="card-body">
            <div>
                <p><strong>Name:</strong> {{ $employer->name }}</p>
            </div>
            <div>
                <p><strong>Email:</strong> {{ $employer->email }}</p>
            </div>
            <div>
                @if($employer->image)
                <p><strong>Image:</strong></p>
                <img src="{{ asset('images/' . $employer->image) }}" alt="{{ $employer->name }}" width="150">
                @endif
            </div>
        </div>
    </div>

   
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Employee Details</h2>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addEmployeeModal">
                Add Employee
            </button>
        </div>
        <div class="card-body">
            @if($employer->employees->isEmpty())
                <p>No employees found.</p>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Image</th>
                        </tr>
                    </thead>
                    <tbody id="employee-list">
                        @foreach($employer->employees as $employee)
                        <tr>
                            <td>{{ $employee->name }}</td>
                            <td>{{ $employee->email }}</td>
                            <td>
                                @if($employee->image)
                                    <img src="{{ asset('employees/' . $employee->image) }}" alt="{{ $employee->name }}" width="50">
                                @else
                                    <span>No Image Available</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal" tabindex="-1" role="dialog" aria-labelledby="addEmployeeModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addEmployeeModalLabel">Add Employee</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-employee-form" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="employer-id" value="{{ $employer->id }}">
                    <div class="form-group">
                        <label for="employee_name">Employee Name</label>
                        <input type="text" name="employee_name" class="form-control" placeholder="Employee Name" required>
                    </div>
                    <div class="form-group">
                        <label for="employee_email">Employee Email</label>
                        <input type="email" name="employee_email" class="form-control" placeholder="Employee Email" required>
                    </div>
                    <div class="form-group">
                        <label for="employee_image">Employee Image</label>
                        <input type="file" name="employee_image" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Employee</button>
                </form>
            </div>
        </div>
    </div>
</div>


<a href="{{ url('admin/employers') }}" class="btn btn-primary">Back to Employer List</a>
@endsection

@push('script')
<script>
    $(document).ready(function() {
        $('#add-employee-form').on('submit', function(e) {
            e.preventDefault();

            var formData = new FormData(this);
            var employerId = $('#employer-id').val();

            $.ajax({
                type: 'POST',
                url: '/admin/employers/' + employerId + '/employees',
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    var newEmployee = `<li>${response.name} (${response.email}) 
                        ${response.image ? '<img src="/employees/' + response.image + '" width="50">' : 'No Image'}</li>`;
                    $('#employee-list').append(newEmployee);

                    // Reset the form and hide the modal after successful addition
                    $('#add-employee-form')[0].reset();
                    $('#addEmployeeModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    console.log('Error:', error);
                    console.log('Status:', status);
                    console.log('Response:', xhr.responseText);
                }
            });
        });
    });
</script>
@endpush
