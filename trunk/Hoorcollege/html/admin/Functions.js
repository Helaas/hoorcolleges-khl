function aanvinken(elem, naam) {
    checkb = document.getElementsByName(naam);
    if(elem.checked == true) {
        for (i = 0; i < checkb.length; i++) {
            checkb[i].checked = true ;
            document.getElementById("disknop").disabled = false;
        }
    }
    else {
        for (i = 0; i < checkb.length; i++) {
            checkb[i].checked = false ;
            document.getElementById("disknop").disabled = true;
        }
    }
}

function checkEnabled(naam) {
    checkb = document.getElementsByName(naam);
    
    var gevr = false;

    if(checkb.checked == true) {
        gevr = true;
    }

    for (i = 0; i < checkb.length; i++) {
        if(checkb[i].checked == true) {
            gevr = true;
        }
    }
    if(gevr == true) {
        document.getElementById("disknop").disabled = false;
    }
    else {
        document.getElementById("disknop").disabled = true;
    }
}


function maakDropDown(dropDown) {
    var gekozenVak = dropDown.options[dropDown.selectedIndex].text;
    var id = dropDown.options[dropDown.selectedIndex].value; //het id van het vak dat gekozen is

    //de dropdown die aangepast moet worden
    var vanDrop = document.getElementById("vakvan");
    if(id != 'kies') {
        for(var i=0; i < vanDrop.length; i++) {
            vanDrop.remove(i);
        }
        
        /*
        var textNode = document.createTextNode('lala');
        var optie = document.createElement('option');
        optie.setAttribute('value', 'textNode');
        optie.appendChild(textNode)
        document.getElementById('vakvan').appendChild(optie);
        */
    }    
}

function redirectNaamBegintMet(dropDown) {    
    var gekozenLetters = dropDown.options[dropDown.selectedIndex].text;    
    window.location = "admin.php?actie=student&naam=" + gekozenLetters;
    //var dr = document.getElementById('selectNaamBegintMet');
    //dr.selectedIndex = 2;
}


