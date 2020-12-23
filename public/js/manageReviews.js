function redirectAndShowError() {
    alert('Error while communicating with the server');
    window.location.href = '/';
}

function createReview(userSelectionId, rowId, guideId, guideName) {
    const optionValue = $('#' + userSelectionId + ' option:selected').val()
    const optionText = $('#' + userSelectionId + ' option:selected').html()
    if (confirm('Assign '+ optionText + ' to create review for guide \'' + guideName + '\' ?')) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        formData.append('reviewerId', optionValue);
        formData.append('guideId', guideId);

        xhr.open('POST', '/assignreview', true);
        xhr.onload = () => {
            const result = JSON.parse(xhr.response);

            if (result.result === 'error') {
                const error = result.error;
                alert('An Error occurred: ' + error);
            }
            window.location.href = '/';
        }

        xhr.onerror = () => {
            redirectAndShowError();
        }

        xhr.send(formData);
    }
}