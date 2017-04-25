@extends('layouts.app')

@section('content')
    <h2>Phone Book

    </h2><button type="button" class="btn btn-success" data-toggle="modal" data-target="#myModal">
        Create
    </button>
    <table id="contacts-table" class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Phone</th>
            <th>Photo</th>
            <th>Action</th>
        </tr>
        </thead>
        <tbody>
        @foreach($contacts as $contact)
            <tr>
                <td data-name="name">{{ $contact->name }}</td>
                <td data-name="phone">{{ $contact->phone }}</td>
                <td><img src="/storage/{{ $contact->photo }}" class="photo" alt=""></td>

                <td>
                    <button data-id="{{ $contact->id }}" type="button" class="btn btn-primary btn-update">
                        Update
                    </button>
                    <button data-id="{{ $contact->id }}" type="button" class="btn btn-danger btn-delete">
                        Delete
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @include('partials.create_form')
    @include('partials.update_form')
@endsection
@section('scripts')
    <script>
        $(document).ready(function () {
            var id, tr;
            var update_modal = $('#update');

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function rowHTML(data) {
                var row = "<tr>";
                row += '<td>' + data.name + '</td>';
                row += '<td>' + data.phone + '</td>';
                row += '<td><img src="/storage/' + data.photo + '" class="photo" alt=""></td>';
                row += '<td>' +
                    '<button data-id="' + data.id + '" type="button" class="btn btn-primary btn-update">Update</button>' +
                    '<button data-id="' + data.id + '" type="button" class="btn btn-danger btn-delete">Delete</button>' +
                    '</td>';
                row += '<tr>';

                return row;
            }

            $('#create_form_submit').on('click', function () {
                var form = $('#create_form');
                var name = $('#name').val();
                var phone = $('#phone').val();

                var photo = $('#photo').prop('files')[0];

                var form_data = new FormData();

                form_data.append('name', name);
                form_data.append('phone', phone);
                form_data.append('photo', photo);

                $.ajax({
                    url: '{{ url("contacts/store") }}', // point to server-side PHP script
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (data) {
                        var row = rowHTML(data);
                        $('#contacts-table tbody').append(row);

                        $('#myModal').modal('hide');
                    },
                    error: function (xhr) {
                        if (xhr.status == 422) {
                            var errors_responce = JSON.parse(xhr.responseText);

                            var errors = '';
                            $.each(errors_responce, function (key, value) {
                                for (var i = 0; i < value.length; i++) {
                                    errors += '<p class="form-error">' + value[i] + '</p>';
                                }
                            });

                            $('.errors').html(errors);
                        } else {
                            alert('An error occurred while processing the request.');
                        }
                    }
                });
            });

            $('#contacts-table').on('click', '.btn-delete', function () {
                var id = $(this).attr('data-id');
                console.log(id);
                var that = $(this);

                $.ajax({
                    url: '{{ url("contacts/delete") }}' + '/' + id, // point to server-side PHP script
                    dataType: 'text',  // what to expect back from the PHP script, if anything
                    cache: false,
                    contentType: false,
                    processData: false,
                    type: 'get',
                    success: function () {
                        that.closest('tr').hide();
                    }
                });
            });

            $('#contacts-table').on('click', '.btn-update', function () {
                id = $(this).attr('data-id');
                tr = $(this).closest('tr');

                tr.find('[data-name]').each(function(){
                    var name = $(this).attr('data-name');
                    update_modal.find('[name="'+ name +'"]').val(
                        $(this).text().trim()
                    );
                });
                update_modal.modal('show');
            });

            $('#update_form_submit').on('click', function () {
                var form = $('#update_form');

                var form_data = new FormData(form[0]);
                form_data.append('_method', 'put');

                $.ajax({
                    url: '{{ url("contacts/update") }}/' + id, // point to server-side PHP script
                    dataType: 'json',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (res) {
                        var row = rowHTML(res.data);
                        tr.replaceWith(row);

                        update_modal.modal('hide');
                    },
                    error: function (xhr) {
                        if (xhr.status == 422) {
                            var errors_responce = JSON.parse(xhr.responseText);

                            var errors = '';
                            $.each(errors_responce, function (key, value) {
                                for (var i = 0; i < value.length; i++) {
                                    errors += '<p class="form-error">' + value[i] + '</p>';
                                }
                            });

                            $('.errors').html(errors);
                        } else {
                            alert('An error occurred while processing the request.');
                        }
                    }
                });
            });
        });
    </script>
    <link rel="stylesheet" href="/css/style.css">
@endsection
