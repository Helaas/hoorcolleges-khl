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
