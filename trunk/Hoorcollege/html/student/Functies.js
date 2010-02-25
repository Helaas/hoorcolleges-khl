var xHRObjectCommentToevoegen = null; //aanmaken van xHRObject
var commentaar;
var gebruikersnaam;
var hoorcollege;
function submitCommentaar(){
    commentaar = document.getElementById('tekst').value;
    gebruikersnaam = document.getElementById('naamGebruiker').value;
    hoorcollege = document.getElementById('idHoorcollege').value;
    // initialisatie van het xHRObject browser-onafhankelijk
    if (window.XMLHttpRequest) {
        xHRObjectCommentToevoegen = new XMLHttpRequest();
    }
    else
    if (window.ActiveXObject) {
        xHRObjectCommentToevoegen = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xHRObjectCommentToevoegen.onreadystatechange = getDataCommentToevoegen; // callback
    xHRObjectCommentToevoegen.open("GET", "verwerkCommentaarToevoegen.php?commentaar="+commentaar+"&hoorcollege="+hoorcollege,true);
    xHRObjectCommentToevoegen.send(null);

}




function getDataCommentToevoegen(){
    if(xHRObjectCommentToevoegen.readyState == 4 && xHRObjectCommentToevoegen.status == 200){
        var commentaarDiv = document.createElement('div');
        commentaarDiv.id = "commentaar";

        var commentaarGebruiker = document.createElement('p');
        commentaarGebruiker.textContent = gebruikersnaam + " zegt:";
        commentaarDiv.appendChild(commentaarGebruiker);

        var commentaarText = document.createElement('p');
        commentaarText.textContent = commentaar;
        commentaarDiv.appendChild(commentaarText);

        document.getElementById("commentaren").appendChild(commentaarDiv);
    }
}

