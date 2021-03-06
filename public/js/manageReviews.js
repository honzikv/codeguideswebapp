/**
 * Presmeruje se na '/' a ukaze chybu
 */
function redirectAndShowError() {
    alert('Error while communicating with the server');
    window.location.href = '/';
}

/**
 * Odeslani vytvoreni recenze
 * @param userSelectionId select element id
 * @param guideId id guide
 * @param guideName nazev guide (pro confirm)
 */
function createReview(userSelectionId, guideId, guideName) {
    const optionValue = $('#' + userSelectionId + ' option:selected').val();
    const optionText = $('#' + userSelectionId + ' option:selected').html();
    if (confirm('Assign ' + optionText + ' to create review for guide \'' + guideName + '\' ?')) {
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

/**
 * Odeslani pozadavku pro odstraneni review
 * @param reviewId id review
 * @param guideId id guide
 */
function deleteReview(reviewId, guideId) {
    if (confirm('Are you sure you want to remove this review?')) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        formData.append('reviewId', reviewId);
        formData.append('guideId', guideId)

        xhr.open('POST', '/deletereview', true);
        xhr.onload = () => {
            const jsonResponse = JSON.parse(xhr.response);
            if ('error' in jsonResponse) {
                $('#error').html(jsonResponse.error);
            } else {
                $('#content').replaceWith(jsonResponse.fragment);
            }
        };

        xhr.onerror = () => {
            redirectAndShowError();
        };

        xhr.send(formData);
    }
}

/**
 * Odeslani pozadavku pro vydani guide
 * @param guideId id guide
 */
function releaseGuide(guideId) {
    if (confirm('Are you sure you want to release this guide?')) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();

        formData.append('guideId', guideId);
        xhr.open('POST', '/releaseguide', true);
        xhr.onload = () => {
            console.log(xhr.response);
            const jsonResponse = JSON.parse(xhr.response);
            if ('error' in jsonResponse) {
                $('#error').html(jsonResponse.error);
            } else {
                $('#content').replaceWith(jsonResponse.fragment);
            }
        };

        xhr.onerror = () => {
            redirectAndShowError();
        };

        xhr.send(formData);
    }
}

/**
 * Odeslani pozadavku pro odmitnuti review
 * @param guideId id guide
 */
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