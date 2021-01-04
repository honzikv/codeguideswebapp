// pri submitu formulare pro registraci prepise originalni event a provede ajax post
$('#registerForm').on('submit', (e) => {
    e.preventDefault();

    $.ajax({
        type: 'post',
        url: '/register',
        data: $('#registerForm').serialize(),
        processData: false,
        success: (response) => {
            const jsonResponse = JSON.parse(response);
            if ('html' in jsonResponse) {
                document.open();
                document.write(jsonResponse.html);
                document.close();
            } else {
                $('#password').val('');
                $('#error').html(jsonResponse.error);
            }
        },
        error: () => {
            alert('Error while communicating with server');
        }
    });
});