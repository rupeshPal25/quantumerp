@extends('layouts.app') 
@section('content')
<table>
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Company</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody id="employer-list">
        @foreach($employers as $employer)
            <tr>
                <td>{{ $employer->name }}</td>
                <td>{{ $employer->email }}</td>
                <td>{{ $employer->company }}</td>
                <td><a href="{{ url('admin/employers/'.$employer->id) }}">View Details</a></td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Add Employer Form -->
<form id="employer-form">
    @csrf
    <input type="text" name="name" placeholder="Employer Name" required>
    <input type="email" name="email" placeholder="Employer Email" required>
    <input type="text" name="company" placeholder="Company" required>
    <button type="submit">Add Employer</button>
</form>
@endsection
@push('script')
<script>
$(document).ready(function() {
    // Bind the form submission event
    $('#employer-form').on('submit', function(e) {
        console.log('Form submitted'); 
        e.preventDefault();

        // Make the AJAX request
        $.ajax({
            type: 'POST',
            url: '{{ url('admin/employers') }}', // Use the URL correctly
            data: $(this).serialize(), // Serialize the form data
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Add CSRF token to headers
            },
            success: function(response) {
                // Append the new employer to the list
                $('#employer-list').append(`
                    <tr>
                        <td>${response.name}</td>
                        <td>${response.email}</td>
                        <td>${response.company}</td>
                        <td><a href="/admin/employers/${response.id}">View Details</a></td>
                    </tr>
                `);
            },
            error: function(xhr) {
                // Handle the error
                console.log(xhr.responseText);
            }
        });
    });
});
</script>
@endpush    