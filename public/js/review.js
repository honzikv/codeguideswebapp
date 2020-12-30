// posleme request na server pomoci JQuery AJAX (xmlhttprequest)
$(document).on('submit','#reviewForm', (e) => {
    e.preventDefault(); // zablokujeme vychozi event
    if (confirm('Save review?')) { // pokud confirm odesleme request a pri success update dat (jinak nic)
        $.ajax({
            type: 'post',
            url: '/savereview',
            data: $('#reviewForm').serialize(),
            processData: false,
            success: (response) => {
                const jsonResponse = JSON.parse(response);
                if ('fragment' in jsonResponse) {
                    $('#content').replaceWith(jsonResponse.fragment); // prepsani formulare
                    // smaze vysledek po 5 sekundach
                    setTimeout(() => $('#saveResult').fadeOut('fast'), 5000);
                } else {
                    $('#error').html(jsonResponse.error);
                }
            }
        });
    }

});