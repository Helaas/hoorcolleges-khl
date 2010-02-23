
function submitCommentaar(){
    alert("Alert int submitCommentaar");
    var commentaar = commentaar.value;

    var xHRObjectCommentToevoegen = null; //aanmaken van xHRObject
    // initialisatie van het xHRObject browser-onafhankelijk
    if (window.XMLHttpRequest) {
        xHRObjectCommentToevoegen = new XMLHttpRequest();
    }
    else
    if (window.ActiveXObject) {
        xHRObjectCommentToevoegen = new ActiveXObject("Microsoft.XMLHTTP");
    }
    xHRObjectCommentToevoegen.onreadystatechange = getDataCommentToevoegen; // callback
    xHRObjectCommentToevoegen.open("POST", "verwerkCommentaarToevoegen.php",true);
    xHRObjectCommentToevoegen.setRequestHeader('Content-Type', 'text/xml');
    xHRObjectCommentToevoegen.send("commentaar = '"+ commentaar + "'");

    
  
}




function getDataCommentToevoegen(){
    alert("Komt in Callback");
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

