/**
 * Funkce pro odeslani POST requestu na odstraneni guide
 * @param guideId id guide
 */
function deleteGuide(guideId) {
    if (confirm('Are you sure you want to remove this guide?')) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        formData.append('guideId', guideId);

        xhr.open('POST', '/removeguide', true);
        xhr.onload = () => {
            console.log(xhr.response);
            const jsonResponse = JSON.parse(xhr.response);
            if ('error' in jsonResponse) {
                $('#error').html(jsonResponse.error);
            } else {
                $('#content').replaceWith(jsonResponse.fragment);
            }
        };

        xhr.send(formData);
    }
}
