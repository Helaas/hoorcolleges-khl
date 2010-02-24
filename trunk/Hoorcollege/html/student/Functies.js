var xHRObjectCommentToevoegen = null; //aanmaken van xHRObject

function submitCommentaar(){
    var commentaar = document.getElementById('tekst').value;
    // initialisatie van het xHRObject browser-onafhankelijk
    if (window.XMLHttpRequest) {
        xHRObjectCommentToevoegen = new XMLHttpRequest();
    }
    else
    if (window.ActiveXObject) {
        xHRObjectCommentToevoegen = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xHRObjectCommentToevoegen.onreadystatechange = getDataCommentToevoegen; // callback
    xHRObjectCommentToevoegen.open("GET", "verwerkCommentaarToevoegen.php?commentaar="+commentaar+"&hoorcollege=1",true);
    xHRObjectCommentToevoegen.send(null);

}




function getDataCommentToevoegen(){
    if(xHRObjectCommentToevoegen.readyState == 4 && xHRObjectCommentToevoegen.status == 200){
        //Er moet niets gebeuren
    }
}

