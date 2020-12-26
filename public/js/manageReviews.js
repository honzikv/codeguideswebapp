function redirectAndShowError() {
    alert('Error while communicating with the server');
    window.location.href = '/';
}

function createReview(userSelectionId, rowId, guideId, guideName) {
    const optionValue = $('#' + userSelectionId + ' option:selected').val();
    const optionText = $('#' + userSelectionId + ' option:selected').html();
    if (confirm('Assign '+ optionText + ' to create review for guide \'' + guideName + '\' ?')) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        formData.append('userId', optionValue);
        formData.append('guideId', guideId);

        xhr.open('POST', '/assignreview', true);
        xhr.onload = () => {
            const jsonResponse = JSON.parse(xhr.response);
            $('#content').replaceWith(jsonResponse.fragment);
        };

        xhr.onerror = () => {
            redirectAndShowError();
        };

        xhr.send(formData);
    }
}

function deleteReview(reviewId, guideId) {
    if (confirm('Are you sure you want to remove this review?')) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        formData.append('reviewId', reviewId);
        formData.append('guideId', guideId)

        xhr.open('POST', '/deletereview', true);
        xhr.onload = () => {
            const jsonResponse = JSON.parse(xhr.response);
            $('#content').replaceWith(jsonResponse.fragment);
        };

        xhr.onerror = () => {
            redirectAndShowError();
        };

        xhr.send(formData);
    }
}

function releaseGuide(guideId) {
    if (confirm('Are you sure you want to release this guide?')) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();

        formData.append('guideId', guideId);
        xhr.open('POST', '/releaseguide', true);
        xhr.onload = () => {
            const jsonResponse = JSON.parse(xhr.response);
            $('#content').replaceWith(jsonResponse.fragment);
        };

        xhr.onerror = () => {
            redirectAndShowError();
        };

        xhr.send(formData);
    }
}

function rejectGuide(guideId) {
    if (confirm('Are you sure you want to reject this guide?')) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();

        formData.append('guideId', guideId);
        xhr.open('POST', '/rejectguide', true);
        xhr.onload = () => {
            const jsonResponse = JSON.parse(xhr.response);
            $('#content').replaceWith(jsonResponse.fragment);
        };

        xhr.onerror = () => {
            redirectAndShowError();
        };

        xhr.send(formData);
    }
}