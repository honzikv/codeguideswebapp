const scores = ['info_score', 'complexity_score', 'efficiency_score', 'quality_score', 'overall_score'];


setTimeout(() => $('#saveResult').fadeOut('fast'), 5000); // smaze vysledek po 5 sekundach

$('#reviewForm').on('submit', (e) => {
    e.preventDefault();

    if (confirm('Save review?')) {
        $.ajax({
            type: 'post',
            url: '/savereview',
            data: $('#reviewForm').serialize(),
            processData: false,
            success: (response) => {
                const jsonResponse = JSON.parse(response);
                if ('fragment' in jsonResponse) {
                    $('#conent').replaceWith(jsonResponse.fragment);
                }
            }
        });
    }

});

$('#completeReview').click(() => {
    if (confirm('Complete review? This action cannot be reverted without a publisher')) {
        $.ajax({
            type: 'post',
            url: '/completereview',
            data: $('#reviewForm').serialize(),
            processData: false,
            success: (response) => {
                const jsonResponse = JSON.parse(response);
                if ('fragment' in jsonResponse) {
                    $('#content').replaceWith(jsonResponse.fragment);
                }
            }
        });
    }

});