$('#createGuideForm').on('submit', (e) => {
    e.preventDefault();

    const data = new FormData($('#createGuideForm')[0]);

    $.ajax({
        type: 'post',
        url: '/createguide',
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        cache: false,
        data: data,
        success: (response) => {
            console.log(response);
            const jsonResponse = JSON.parse(response);
            if ('html' in jsonResponse) {
                document.open();
                document.write(jsonResponse.html);
                document.close();
            } else {
                $('#error').html(jsonResponse.error);
            }
        },
        error: () => {
            alert('Error while communicating with server');
        }
    })
});

