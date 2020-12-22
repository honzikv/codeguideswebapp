function redirectAndShowError() {
    alert('Error while communicating with the server');
    window.location.href = '/';
}
/**
 * Poslani requestu na zabanovani uzivatele
 * @param userId id uzivatele
 * @param username username uzivatele
 * @param action akce - ban nebo unban (pouze pro popup)
 */
function banUser(userId, username, action) {
    if (confirm('Are you sure you want to ' + action + ' user ' + username + ' ?')) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        formData.append('userId', userId);
        xhr.open('POST', '/ban', true);

        xhr.onload = () => {
            const result = JSON.parse(xhr.response);
            $('#banButton' + result.id).html(result.banned === '1' ? 'Unban' : 'Ban');
            $('#user' + result.id + 'Username').html(result.banned === '1' ?
                result.username + ' (banned)' : result.username);
        };

        xhr.onerror = () => {
            redirectAndShowError();
        };

        xhr.send(formData);
    }
}

/**
 * Poslani requestu na zmenu role uzivatele
 * @param userId id uzivatele
 * @param username username uzivatele
 * @param dropdown dropdown menu objekt
 */
function changeRole(userId, username, dropdown) {
    const option_text = dropdown.options[dropdown.selectedIndex].text;
    const option_value = dropdown.options[dropdown.selectedIndex].value;

    if (confirm('Are you sure you want to change role of the user ' + username + ' to ' + option_text + '?')) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        formData.append('userId', userId);
        formData.append('role', option_value);

        xhr.open('POST', '/changerole', true);
        xhr.onload = () => {
            const result = JSON.parse(xhr.response);
            $('#user' + result.id + 'Role').html(result.role);
        };

        xhr.onerror = () => {
            redirectAndShowError();
        };

        xhr.send(formData);
    }
}

/**
 * Smazani uzivatele
 * @param userId id uzivatele
 */
function deleteUser(userId, username) {
    if (confirm('Are you sure you want to delete user ' + username + ' ? This action is irreversible!')) {
        const xhr = new XMLHttpRequest();
        const formData = new FormData();
        formData.append('userId', userId);
        xhr.open('POST', '/deleteuser', true);
        xhr.onload = () => {
            const result = JSON.parse(xhr.response);
            console.log(result);
            alert(result.message);
            location.reload();
        };

        xhr.onerror = () => {
            redirectAndShowError();
        };

        xhr.send(formData);
    }
}