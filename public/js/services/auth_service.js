$(document).ready(function () {

    // When opening the login modal
    $('#loginModal').on('show.bs.modal', function () {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        $('#login_email').val('');
        $('#login_password').val('');
    });

    // Login AJAX Request
    $(document).on('click', '#loginModal button.btn-dark', function (e) {
        e.preventDefault();

        let email = $('#login_email').val();
        let password = $('#login_password').val();

        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $.ajax({
            url: LOGIN_API_URL,
            method: 'POST',
            data: {
                email: email,
                password: password,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            beforeSend: function () {
            },
            success: function (response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Login failed. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        let input = $(`[id="login_${field}"]`);
                        input.addClass('is-invalid').after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    }
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON.message || 'Login failed. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            },
        });
    });

    // When opening the login modal
    $('#registerModal').on('show.bs.modal', function () {
        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();
        $('#signup_name').val('');
        $('#signup_email').val('');
        $('#signup_phone').val('');
        $('#signup_password').val('');
        $('#signup_password_confirmation').val('');
        $('#signup_role').val('');
    });

    $(document).on('click', '#registerModal button.btn-dark', function (e) {
        e.preventDefault();

        const registerData = {
            name: $('#signup_name').val(),
            email: $('#signup_email').val(),
            phone_number: $('#signup_phone_number').val(),
            password: $('#signup_password').val(),
            password_confirmation: $('#signup_password_confirmation').val(),
            role: $('#signup_role').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $('.form-control').removeClass('is-invalid');
        $('.invalid-feedback').remove();

        $.ajax({
            url: REGISTER_API_URL,
            method: 'POST',
            data: registerData,
            success: function (response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Registration successful! Please do login.',
                        icon: 'success',
                        confirmButtonText: 'Ok'
                    });
                    $('#registerModal').modal('hide');
                    $('#loginModal').modal('show');
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: response.message || 'Registration failed. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            },
            error: function (xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    for (let field in errors) {
                        let input = $(`[id="signup_${field}"]`);
                        input.addClass('is-invalid').after(`<div class="invalid-feedback">${errors[field][0]}</div>`);
                    }
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: xhr.responseJSON.message || 'Registration failed. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'Ok'
                    });
                }
            },
        });
    });
});
