
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

        xhr.onreadystatechange = function() {
            if( xhr.readyState===4 ){
                console.log( xhr.responseText );
            }
        };

        xhr.send(formData);
    }

}