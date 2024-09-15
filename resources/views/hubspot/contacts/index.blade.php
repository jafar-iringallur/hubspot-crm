<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('HubSpot Contacts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container">
            <div class="card">
                <div class="card-header" style="text-align: end">
                
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addContactModal">
                        <i class="bi bi-plus"></i> Add
                    </button>
                    <button type="button" class="btn btn-primary" id="importContactsButton">
                        <i class="bi bi-download"></i> Import from HubSpot
                    </button>
                </div>
                <div class="card-body">
                   
                    <!-- Table of Contacts -->
                    <div class="container">
                        <table class="table table-striped mt-4" id="contactsTable">
                            <thead>
                                <tr>
                                    <th scope="col">First Name</th>
                                    <th scope="col">Last Name</th>
                                    <th scope="col">Email</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Contact Modal -->
    <div class="modal fade" id="addContactModal" tabindex="-1" role="dialog" aria-labelledby="addContactModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addContactModalLabel">Add Contact</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="addContactForm" action="{{ route('hubspot.contacts.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="accountId" value="{{$accountId}}">
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" class="form-control" id="firstname" name="firstname" required>
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" class="form-control" id="lastname" name="lastname" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary" id="addContactbtn">Add Contact</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Update Contact Modal -->
<div class="modal fade" id="updateContactModal" tabindex="-1" role="dialog" aria-labelledby="updateContactModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="updateContactModalLabel">Update Contact</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="updateContactForm">
                @csrf
                @method('PUT') <!-- To make it a PUT request -->
                <div class="modal-body">
                    <input type="hidden" id="updateContactId" name="accountId" value="{{$accountId}}">
                    <div class="form-group">
                        <label for="update_firstname">First Name</label>
                        <input type="text" class="form-control" id="update_firstname" name="firstname" required>
                    </div>
                    <div class="form-group">
                        <label for="update_lastname">Last Name</label>
                        <input type="text" class="form-control" id="update_lastname" name="lastname" required>
                    </div>
                    <div class="form-group">
                        <label for="update_email">Email</label>
                        <input type="email" class="form-control" id="update_email" name="email" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary" id="updateContactbtn">Update Contact</button>
                </div>
            </form>
        </div>
    </div>
</div>


<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script>

   $(document).ready(function() {

    $('#contactsTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('hubspot.contacts.getContacts', $accountId) }}",
            columns: [
                {data: 'firstname', name: 'firstname'},
                {data: 'lastname', name: 'lastname'},
                {data: 'email', name: 'email'},
                {data: null, name: 'action', orderable: false, searchable: false, render: function (data, type, row) {
                    let updateButton = `<button class="btn btn-warning btn-sm update-contact" 
                                        data-id="${row.id}" 
                                        data-first-name="${row.firstname}" 
                                        data-last-name="${row.lastname}" 
                                        data-email="${row.email}">
                                        <i class="bi bi-pencil"></i></button>`;
                    
                    let deleteButton = `<button class="btn btn-danger ml-2 btn-sm delete-contact" 
                                         data-id="${row.id}">
                                         <i class="bi bi-trash"></i></button>`;
                    
                    return updateButton + deleteButton;
                }},
            ]
        });
 
    $('#addContactForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            beforeSend: function() {
                $('#addContactbtn').prop('disabled', true);
                $('#addContactbtn').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...'
                    );
            },
            complete: function() {
                $('#addContactbtn').prop('disabled', false);
                $('#addContactbtn').html('Add Contact');
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Contact added successfully.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    $('#contactsTable').DataTable().ajax.reload();
                });
                $('#addContactModal').modal('hide');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to add contact.',
                });
            }
        });
    });

    
    $('#importContactsButton').click(function() {
        var accountId = @json($accountId);
        $.ajax({
            url: "{{ route('hubspot.contacts.import') }}",
            method: 'POST',
            data: {
                _token: "{{ csrf_token() }}",
                accountId: accountId
            },
            beforeSend: function() {
                $('#importContactsButton').prop('disabled', true);
                $('#importContactsButton').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...'
                    );
            },
            complete: function() {
                $('#importContactsButton').prop('disabled', false);
                $('#importContactsButton').html('<i class="bi bi-download"></i> Import from HubSpot');
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Contacts imported successfully.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    $('#contactsTable').DataTable().ajax.reload();
                });
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to import contacts.',
                });
            }
        });
    });

    $(document).on('click', '.update-contact', function() {
        const contactId = $(this).data('id');
        const firstName = $(this).data('first-name');
        const lastName = $(this).data('last-name');
        const email = $(this).data('email');
        $('#updateContactId').val(contactId);
        $('#update_firstname').val(firstName);
        $('#update_lastname').val(lastName);
        $('#update_email').val(email);

        $('#updateContactModal').modal('show');
    });

    $('#updateContactForm').submit(function(e) {
        e.preventDefault();

        const contactId = $('#updateContactId').val();
        const formData = $(this).serialize();

        $.ajax({
            url: `/hubspot/contacts/${contactId}`,
            method: 'PUT',
            data: formData,
            beforeSend: function() {
                $('#updateContactbtn').prop('disabled', true);
                $('#updateContactbtn').html(
                    '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>Loading...'
                    );
            },
            complete: function() {
                $('#updateContactbtn').prop('disabled', false);
                $('#updateContactbtn').html('Update Contact');
            },
            success: function(response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Contact updated successfully.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    $('#contactsTable').DataTable().ajax.reload();
                });
                $('#updateContactModal').modal('hide');
            },
            error: function(xhr) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update contact.',
                });
            }
        });
    });

    $(document).on('click', '.delete-contact', function() {
        const contactId = $(this).data('id');

        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            cancelButtonColor: '#3085d6',
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/hubspot/contacts/${contactId}`,
                    method: 'DELETE',
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Contact has been deleted.',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            $('#contactsTable').DataTable().ajax.reload();
                        });
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete contact.',
                        });
                    }
                });
            }
        });
    });
});
    </script>
</x-app-layout>
