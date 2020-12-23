$('#loginForm').on('submit', (e) => {
    e.preventDefault();

    $.ajax({
        type: 'post',
        url: '/login',
        data: $('#loginForm').serialize(),
        success: (response) => {
            const jsonResponse = JSON.parse(response);
            if ('html' in jsonResponse) {
                document.open();
                document.write(jsonResponse.html);
                document.close();
            } else {
                $('#password').val('');
                $('#error')
                    .html(jsonResponse.error);
            }
        },
        error: () => {
            alert('Error while communicating with server');
        }
    })
});

