// Pri vytvoreni guide
$('#createGuideForm').on('submit', (e) => {
    e.preventDefault();

    const data = new FormData($('#createGuideForm')[0]); // data formulare

    $.ajax({
        type: 'post',
        url: '/createguide',
        enctype: 'multipart/form-data',
        processData: false,
        contentType: false,
        cache: false,
        data: data,
        success: (response) => {
            const jsonResponse = JSON.parse(response);
            if ('html' in jsonResponse) { // pokud html v response, prepiseme
                document.open();
                document.write(jsonResponse.html);
                document.close();
            } else { // jinak zobrazime chybu
                $('#error').html(jsonResponse.error);
            }
        },
        error: () => {
            alert('Error while communicating with server');
        }
    })
});

