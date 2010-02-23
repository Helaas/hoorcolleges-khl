var xHRObjectCommentToevoegen = null; //aanmaken van xHRObject
var aantal= 0;
var gebruikers=new Array();
var commentaren=new Array();

function submitCommentaar(){
    var commentaar = document.getElementById('tekst').value;
    alert(commentaar);
    // initialisatie van het xHRObject browser-onafhankelijk
    if (window.XMLHttpRequest) {
        xHRObjectCommentToevoegen = new XMLHttpRequest();
    }
    else
    if (window.ActiveXObject) {
        xHRObjectCommentToevoegen = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xHRObjectCommentToevoegen.onreadystatechange = getDataCommentToevoegen; // callback
    xHRObjectCommentToevoegen.open("GET", "verwerkCommentaarToevoegen.php?commentaar="+commentaar,true);
    xHRObjectCommentToevoegen.send(null);

}




function getDataCommentToevoegen(){
    
    if(xHRObjectCommentToevoegen.readyState == 4 && xHRObjectCommentToevoegen.status == 200){alert("Komt in Callback");
        gebruikers = xHRObjectCommentToevoegen.responseXML.getElementsByTagName("Gebruiker");
        commentaren = xHRObjectCommentToevoegen.responseXML.getElementsByTagName("Tekst");
        aantal = commentaren.length;
        alert("Aantal: " + aantal);
        for(i = 0; i < aantal; i++){
            var commentElement = document.createElement('div');

            var commentGebruiker = document.createElement('p');
            commentGebruiker.textContent = gebruikers[i].textContent + " schrijft:";
            commentElement.appendChild(commentGebruiker);

            var commentText = document.createElement('p');
            commentText.textContent = commentaren[i].textContent;
            commentElement.appendChild(commentText);

            document.getElementById("commentaren").appendChild(commentElement);

        }

    }
}

