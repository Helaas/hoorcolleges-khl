

function submitCommentaar(form){
    var commentaar = form.commentaar.value;
    var xHRObjectCommentToevoegen = new XMLHttpRequest();
    xHRObjectCommentToevoegen.open("POST", "commentaarToevoegen.php",true);
    xHRObjectCommentToevoegen.onreadystatechange = getDataCommentToevoegen; // callback
    xHRObjectCommentToevoegen.send(null);
}




function getDataCommentToevoegen(){
    if (xHRObjectCommentToevoegen.readyState == 4 && xHRObjectCommentToevoegen.status == 200) {
        var serverResponse = xHRObjectCommentToevoegen.responseXML;
        var gebruikers = serverResponse.getElementsByTagName("Gebruiker");
        var commentaren = serverResponse.getElementsByTagName("Tekst");
        var aantal = commentaren.length;

        for(i = 0; i < aantal; i++){
            var commentElement = document.createElement('div');

            var commentGebruiker = document.createElement('p');
            commentGebruiker.textContent = gebruikers[i];
            commentElement.appendChild(commentGebruiker);

            var commentText = document.createElement('p');
            commentText.textContent = commentaren[i];
            commentElement.appendChild(commentText);

            document.getElementById("commentaren").appendChild(commentElement);

        }
    }
}

