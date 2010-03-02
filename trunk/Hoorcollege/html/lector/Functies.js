function autoSubmit(form,var1)
{


    var vakid=form.vak.options[form.vak.options.selectedIndex].value;

    //reset velden
    var TableDiv=document.getElementById('TableDiv');
    if (TableDiv != null){
        while ( TableDiv.firstChild ){
            TableDiv.removeChild( TableDiv.firstChild );
        }
        var ondDiv=document.getElementById('onderwerpform');
        while ( ondDiv.firstChild ){
            ondDiv.removeChild( ondDiv.firstChild );
        }
    }

    var xmlhttp;
    try {
        // Mozilla / Safari / IE7
        xmlhttp = new XMLHttpRequest();
    } catch (e) {
        // IE
        var XMLHTTP_IDS = new Array('MSXML2.XMLHTTP.5.0',
            'MSXML2.XMLHTTP.4.0',
            'MSXML2.XMLHTTP.3.0',
            'MSXML2.XMLHTTP',
            'Microsoft.XMLHTTP' );
        var success = false;
        for (var i=0;i < XMLHTTP_IDS.length && !success; i++) {
            try {
                xmlhttp = new ActiveXObject(XMLHTTP_IDS[i]);
                success = true;
            } catch (e) {}
        }
        if (!success) {
            throw new Error('Unable to create XMLHttpRequest.');
        }
    }



    //xmlhttp request om via php een xml pagina aan te maken op basis van het meegegoven vakid
    xmlhttp.open("GET", "VerwerkDropdown.php?gevraagdVak="+vakid, false);
    xmlhttp.send(null);



    //haal optie uit de XML en voeg toe aan dropdown
    var elems = xmlhttp.responseXML.getElementsByTagName("Onderwerp");
    var size = elems.length;

    //div dynamisch opvullen met een select gebaseerd op het gekozen vak
    document.getElementById("kiesond").innerHTML='Kies een onderwerp:';
    var brk=document.createElement('br');
    document.getElementById("kiesond").appendChild(brk);
    var sel=document.createElement('select');
    sel.name='Ond';
    sel.id='ond';
    if(var1=='autoSubmit2'){
        sel.onchange=function(){
            autoSubmit2(this.form);
        };
    }
    var opt1= document.createElement("option");
    opt1.text='--Selecteer een onderwerp--';
    opt1.value=0;
    sel.options.add(opt1);
    document.getElementById("kiesond").appendChild(sel);
    while(sel.options.length>1){
        sel.options[1]=null;
    }
    for(i = 0; i < size; i++){
        //onderwerp = naam van het onderwerp, id = id van het onderwerp, gebruiker krijgt de naam te zien, de var die doorgegoven word in 'onchange' is de id
        var inh=xmlhttp.responseXML.getElementsByTagName('Onderwerp')[i].firstChild.data;
        var id=xmlhttp.responseXML.getElementsByTagName('Id')[i].firstChild.data;

        sel.options.add( new Option(inh,id));
    }

    var knop= document.createElement('input');
    knop.setAttribute('type','button');
    knop.setAttribute('name','CreateOnd');
    knop.setAttribute('value','Voeg een nieuw onderwerp toe');
    if (TableDiv == null) knop.setAttribute('value','Of voeg een nieuw onderwerp toe');
    knop.onclick = voegOndToe;
    document.getElementById('kiesond').appendChild( document.createTextNode( '\u00A0\u00A0\u00A0' ) );
    document.getElementById('kiesond').appendChild(knop);

}

function autoSubmit2(form)
{

    var vakid=form.vak.options[form.vak.options.selectedIndex].value;
    var ondid=form.Ond.options[form.Ond.options.selectedIndex].value;
    var vak=form.vak.options[form.vak.options.selectedIndex].text;
    var ond=form.Ond.options[form.Ond.options.selectedIndex].text;




    var xmlhttp;

    try {
        // Mozilla / Safari / IE7
        xmlhttp = new XMLHttpRequest();
    } catch (e) {
        // IE
        var XMLHTTP_IDS = new Array('MSXML2.XMLHTTP.5.0',
            'MSXML2.XMLHTTP.4.0',
            'MSXML2.XMLHTTP.3.0',
            'MSXML2.XMLHTTP',
            'Microsoft.XMLHTTP' );
        var success = false;
        for (var i=0;i < XMLHTTP_IDS.length && !success; i++) {
            try {
                xmlhttp = new ActiveXObject(XMLHTTP_IDS[i]);
                success = true;
            } catch (e) {}
        }
        if (!success) {
            throw new Error('Unable to create XMLHttpRequest.');
        }
    }


    //xmlhttp request om via php een xml pagina aan te maken met gegevens over de hoorcolleges die bij dit vak en onderwerp passen
    xmlhttp.open("GET", "VerwerkDropDown2.php?gevraagdVak="+vakid+"&gevraagdOnd="+ondid, false);
    xmlhttp.send(null);

    var TableDiv = document.getElementById("TableDiv");
    if (TableDiv != null){


        //Huidige tabel leegmaken om hem te vervangen met een nieuwe
        while ( TableDiv.firstChild ){
            TableDiv.removeChild( TableDiv.firstChild );
        }
        //als onderwerp of vak niet geselecteerd is, geen tabel maken

        if(ondid!=0 && vakid!=0){

            //grootte xml bestand opvragen
            var elems = xmlhttp.responseXML.getElementsByTagName('Naam');
            var size = elems.length;


            //Tabel met hoorcolleges genereren adhv Ajaxx

            //tabel
            var myTable = document.createElement("table");
            myTable.setAttribute('width','700');
            myTable.setAttribute('border','0');
            myTable.setAttribute('cellspacing','0');
            myTable.setAttribute('cellpadding','0');
            myTable.setAttribute('font-size','13px');

            //Tr vakvermelding
            var TR1 = document.createElement("tr");
            var TD1 = document.createElement("td");
            TD1.setAttribute('className',"title-section");
            TD1.setAttribute('bgcolor',"#CFE7CF");
            TD1.setAttribute('colSpan',"7");
            var bvak = document.createElement("b");
            bvak.appendChild(document.createTextNode(vak));
            TD1.appendChild(bvak);
            TR1.appendChild(TD1);

            myTable.appendChild(TR1);

            //Tr onderwerpvermelding + wijzig en edit opties
            var TR2 = document.createElement("tr");
            var TD2 = document.createElement("td");
            //whitespace td
            TD2.appendChild(document.createTextNode('\u00A0'));
            TR2.appendChild(TD2);

            var TD3= document.createElement("td");
            TD3.setAttribute('bgcolor',"#FFF5D2");
            TD3.setAttribute('colSpan',"2");
            var bfield=document.createElement("b");
            var ufield=document.createElement("u");
            ufield.appendChild(document.createTextNode(ond));
            bfield.appendChild(ufield);
            var p=document.createElement("p");
            p.appendChild(bfield);
            TD3.appendChild(p);
            TD3.setAttribute('width','46%');
            TR2.appendChild(TD3);
            //edit onderwerp
            var TD4= document.createElement("td");
            TD4.setAttribute('bgcolor',"#FFF5D2");
            var link = document.createElement("a");
            link.appendChild(document.createTextNode('Wijzig onderwerp'));
            link.setAttribute('href',"editOnderwerp.php?gevraagdond="+ondid+"&gevraagdondnaam="+ond);
            TD4.appendChild(link);
            TD4.setAttribute('colSpan',"2");
            TR2.appendChild(TD4);
            //remove onderwerp
            var TD5= document.createElement("td");
            TD5.setAttribute('bgcolor',"#FFF5D2");
            TD5.setAttribute('colSpan',"2");
            var link2 = document.createElement("a");
            link2.appendChild(document.createTextNode('Verwijder onderwerp'));
            link2.setAttribute('href',"DeleteOnderwerp.php?gevraagdond="+ondid);
            link2.setAttribute('onclick', "return confirm('Bent u zeker dat u het onderwerp \""+ond+"\" wilt verwijderen?')");
            TD5.appendChild(link2);
            TR2.appendChild(TD5);
            var TD10=document.createElement('td');
            TD10.setAttribute('bgcolor',"#FFF5D2");
            TD10.appendChild(document.createTextNode(''));
            TR2.appendChild(TD10);
            TD10=document.createElement('td');
            TD10.setAttribute('bgcolor',"#FFF5D2");
            TD10.appendChild(document.createTextNode(''));
            TR2.appendChild(TD10);

            myTable.appendChild(TR2);

            //Tr voor namen kolommen
            var TR3 = document.createElement("tr");
            var TDw = document.createElement("td");
            var TDw2= document.createElement("td");
            //whitespace td
            TDw.appendChild(document.createTextNode('\u00A0'));
            TDw2.appendChild(document.createTextNode('\u00A0'));
            TR3.appendChild(TDw);
            TR3.appendChild(TDw2);



            //kolom1
            var TD6= document.createElement("td");
            TD6.setAttribute('bgcolor',"#F0F0F0");
            var boldfield=document.createElement("b");
            var ufield=document.createElement("em");
            ufield.appendChild(document.createTextNode('Hoorcollege'));
            boldfield.appendChild(ufield);
            p=document.createElement("p");
            p.appendChild(boldfield);
            TD6.appendChild(p);
            TR3.appendChild(TD6);
            //kolom2
            TD6= document.createElement("td");
            TD6.setAttribute('bgcolor',"#F0F0F0");
            boldfield=document.createElement("b");
            ufield=document.createElement("em");
            ufield.appendChild(document.createTextNode('Bekijken'));
            boldfield.appendChild(ufield);
            TD6.appendChild(boldfield);
            TR3.appendChild(TD6);
            //kolom3
            TD6= document.createElement("td");
            TD6.setAttribute('bgcolor',"#F0F0F0");
            boldfield=document.createElement("b");
            ufield=document.createElement("em");
            ufield.appendChild(document.createTextNode('Wijzigen'));
            boldfield.appendChild(ufield);
            TD6.appendChild(boldfield);
            TD6.setAttribute('width','10%');
            TR3.appendChild(TD6);
            //kolom4
            TD6= document.createElement("td");
            TD6.setAttribute('bgcolor',"#F0F0F0");
            boldfield=document.createElement("b");
            ufield=document.createElement("em");
            ufield.appendChild(document.createTextNode('Verwijder'));
            boldfield.appendChild(ufield);
            TD6.appendChild(boldfield);
            TR3.appendChild(TD6);
            //kolom5
            TD6= document.createElement("td");
            TD6.setAttribute('bgcolor',"#F0F0F0");
            boldfield=document.createElement("b");
            ufield=document.createElement("em");
            ufield.appendChild(document.createTextNode('Resultaten'));
            boldfield.appendChild(ufield);
            TD6.appendChild(boldfield);
            TR3.appendChild(TD6);


            myTable.appendChild(TR3);

            //vul tabel op met hoorcolleges uit de xml file
            if(size>0){
                for(i=0;i<size;i++){
                    var newTR = document.createElement("tr");
                    var newField = document.createElement("td");
                    p=document.createElement("p");
                    p.appendChild(document.createTextNode(xmlhttp.responseXML.getElementsByTagName('Naam')[i].firstChild.data));
                    newField.appendChild(p);

                    newField.setAttribute('bgcolor',"#F0F0F0");
                    var newTd = document.createElement("td");
                    newTd.appendChild(document.createTextNode('\u00A0'));
                    newTd.setAttribute('colSpan',"2");
                    newTR.appendChild(newTd);
                    newTR.appendChild(newField);

                    var id=xmlhttp.responseXML.getElementsByTagName('Id')[i].firstChild.data;
                    newField = document.createElement("td");
                    link = document.createElement("a");
                    link.appendChild(document.createTextNode('Bekijk'));
                    link.setAttribute('href',"hoorcollege.php?hoorcollege="+id);
                    newField.setAttribute('bgcolor',"#F0F0F0");
                    newField.appendChild(link);
                    newTR.appendChild(newField);


                    newField = document.createElement("td");
                    link = document.createElement("a");
                    link.appendChild(document.createTextNode('Wijzig'));
                    link.setAttribute('href',"wijzigHoorcollege.php?id="+id);
                    newField.appendChild(link);
                    newField.setAttribute('bgcolor',"#F0F0F0");
                    newTR.appendChild(newField);

              
                    newField = document.createElement("td");
                    newField.setAttribute('bgcolor',"#F0F0F0");
                    link = document.createElement("a");
                    link.appendChild(document.createTextNode('Verwijder'));
                    link.setAttribute('href',"DeleteHoorcollege.php?gevraagdhoorcoll="+id);
                    link.setAttribute('onclick', "return confirm('Bent u zeker dat u het hoorcollege \""+xmlhttp.responseXML.getElementsByTagName('Naam')[i].firstChild.data+"\" wilt verwijderen?')");
                    newField.appendChild(link);
                    newTR.appendChild(newField);


                    newField = document.createElement("td");
                    newField.setAttribute('bgcolor',"#F0F0F0");
                    link = document.createElement("a");
                    link.appendChild(document.createTextNode('Resultaten'));
                    link.setAttribute('href',"BekijkResultatenHoorcollege.php?gevraagdhoorcoll="+id+"&vak="+vakid);
                    newField.appendChild(link);
                    newTR.appendChild(newField);
                    myTable.appendChild(newTR);
                }
            }
            else{
                //geen hoorcolleges

                var newTR = document.createElement("tr");
                var newField = document.createElement("td");

                p=document.createElement("p");
                p.appendChild(document.createTextNode('Dit onderwerp bevat nog geen hoorcolleges'));
                newField.appendChild(p);
                newField.setAttribute('bgcolor',"#F0F0F0");
                var newTd = document.createElement("td");
                newTd.appendChild(document.createTextNode('\u00A0'));
                newTd.setAttribute('colSpan',"2");
                newTR.appendChild(newTd);
                newTR.appendChild(newField);

                newField = document.createElement("td");
                newField.appendChild(document.createTextNode(''));
                newField.setAttribute('bgcolor',"#F0F0F0");
                newTR.appendChild(newField);


                newField = document.createElement("td");
                newField.appendChild(document.createTextNode(''));
                newField.setAttribute('bgcolor',"#F0F0F0");
                newTR.appendChild(newField);


                newField = document.createElement("td");
                newField.appendChild(document.createTextNode(''));
                newField.setAttribute('bgcolor',"#F0F0F0");
                newTR.appendChild(newField);

                newField = document.createElement("td");
                newField.appendChild(document.createTextNode(''));
                newField.setAttribute('bgcolor',"#F0F0F0");
                newTR.appendChild(newField);

                myTable.appendChild(newTR);

            }
            TableDiv.appendChild(myTable);
        }








    }
}


function voegOndToe(){

    while ( document.getElementById("onderwerpform").firstChild ){
        document.getElementById("onderwerpform").removeChild( document.getElementById("onderwerpform").firstChild );
    }

    var vakid= document.Form.vak.options[document.Form.vak.options.selectedIndex].value;
    var ondid= document.Form.Ond.options[document.Form.Ond.options.selectedIndex].value;

    var form = document.createElement('form');
    form.name=('OndForm');
    //Veld om de naam van het onderwerp in te geven
    var iveld= document.createElement('input');
    iveld.setAttribute('type','text');
    iveld.setAttribute('name','veld1');
    iveld.setAttribute('value','Geef hier de naam van het onderwerp in..');
    iveld.setAttribute('size','38');
    iveld.onkeyup=function(){
        valideerInput(this.form);
    };
    form.appendChild(iveld);

    //hidden field dat onderwerid bijhoudt
    var hveld1= document.createElement('input');
    hveld1.setAttribute('type','hidden');
    hveld1.setAttribute('name','onderwerpID');
    hveld1.setAttribute('value',ondid);
    form.appendChild(hveld1);

    //hidden field dat vakid bijhoudt
    var hveld2= document.createElement('input');
    hveld2.setAttribute('type','hidden');
    hveld2.setAttribute('name','vakID');
    hveld2.setAttribute('value',vakid);
    form.appendChild(hveld2);

    form.setAttribute('method', 'POST');
    form.setAttribute('action', 'VoegOnderwerpToe.php');
    var subm= document.createElement('input');
    subm.setAttribute('type', 'submit');
    subm.setAttribute('name','submitknop');
    subm.setAttribute('value','Voeg Toe!');
    subm.disabled=true;

    form.appendChild(subm);
    document.getElementById("onderwerpform").appendChild(form);

}

function voegCatToe(){

    while ( document.getElementById("createCategorie").firstChild ){
        document.getElementById("createCategorie").removeChild( document.getElementById("createCategorie").firstChild );
    }

    var form = document.createElement('form');
    form.name=('CatForm');
    //Veld om de naam van het onderwerp in te geven
    var iveld= document.createElement('input');
    iveld.setAttribute('type','text');
    iveld.setAttribute('name','veld1');
    iveld.setAttribute('value','Geef hier de naam van de categorie in..');
    iveld.setAttribute('size','38');
    iveld.onkeyup=function(){
        valideerInput(this.form);
    };
    form.appendChild(iveld);

    form.setAttribute('method', 'POST');
    form.setAttribute('action', 'VoegCategorieToe.php');
    var subm= document.createElement('input');
    subm.setAttribute('type', 'submit');
    subm.setAttribute('name','submitknop');
    subm.setAttribute('value','Voeg Toe!');
    subm.disabled=true;

    form.appendChild(subm);
    document.getElementById("createCategorie").appendChild(form);
}

function deleteForm() {




    var form=document.getElementById("onderwerpform");



    document.removeChild(form);

}

function valideerInput(form){
    var reg = new RegExp("^[a-zA-Z0-9\+\#\ \_]+$");
    var inp= form.veld1.value;
    var MijnDiv=document.getElementById("foutmelding")

    if(reg.test(inp)){
        while ( MijnDiv.firstChild ){
            MijnDiv.removeChild( MijnDiv.firstChild );
        }
        form.submitknop.disabled=false;
    }
    else{
        form.submitknop.disabled=true;
        while ( MijnDiv.firstChild ){
            MijnDiv.removeChild( MijnDiv.firstChild );
        }
        var err=document.createElement('p');
        err.id='fout';
        var txt= document.createTextNode("Het onderwerp mag geen speciale tekens bevatten en moet ingevuld zijn.");
        err.appendChild(txt);
        MijnDiv.appendChild(err);
    }
}

function valideerInputFilename(form){
    var reg = new RegExp("^[a-zA-Z0-9\+\#\ \_]+$");
    var inp= form.filenaam.value;
    var MijnDiv=document.getElementById("foutmelding")

    if(reg.test(inp)){
        while ( MijnDiv.firstChild ){
            MijnDiv.removeChild( MijnDiv.firstChild );
        }
        form.uploadknop.disabled=false;
    }
    else{
        form.uploadknop.disabled=true;
        while ( MijnDiv.firstChild ){
            MijnDiv.removeChild( MijnDiv.firstChild );
        }
        var err=document.createElement('p');
        err.id='fout';
        var txt= document.createTextNode("De bestandsnaam mag geen speciale tekens bevatten en moet ingevuld zijn.");
        err.appendChild(txt);
        MijnDiv.appendChild(err);
    }
}

function maakHoorcollegePopup(type){
    var geselecteerd = document.getElementById("keuze_"+type).value;
    newwindow=window.open('bibliotheekPopup.php?type='+type+'&geselecteerd='+geselecteerd,'biblio','height=550,width=750');
    if (window.focus) {
        newwindow.focus()
    }
    return false;

}

function popupNaarHoofdpagina(type){
    var naam = "technische fout";

    if (type == "txt") naam = "tekst";
    if (type == "mp3") naam = "audio";
    if (type == "flv") naam = "video";

    var verder = false;
    var button;
    var radioButtons = document.body.getElementsByTagName('input')
    for (i=0; i<radioButtons.length; i++) {
        if (radioButtons[i].checked){
            verder = true;
            button = radioButtons[i];
        }
    }

    if(verder){
        if (button.value>0){
            window.opener.document.getElementById("feedback_"+type).innerHTML = "Geselecteerde " + naam + ' : <strong>"' + document.getElementById("naam_"+button.value).innerHTML +'"</strong>.';
            window.opener.document.getElementById("button_"+type).value="Wijzig keuze";
            window.opener.document.getElementById("keuze_"+type).value = button.value
        } else {
            window.opener.document.getElementById("feedback_"+type).innerHTML = "Nog geen " + naam + " geselecteerd.";
            window.opener.document.getElementById("button_"+type).value="Blader in bibliotheek";
            window.opener.document.getElementById("keuze_"+type).value = "-1";
        }
        close();
    } else {
        alert("U moet een keuze maken");
    }


}

function selecteerItemPopup(item){
    var radioButtons = document.body.getElementsByTagName('input');
    for (i=0; i<radioButtons.length; i++) {
        if(radioButtons[i].value == item) radioButtons[i].checked=true;
    }
}


function GetXmlHttpObject(){
    if (window.XMLHttpRequest){
        // code for IE7+, Firefox, Chrome, Opera, Safari
        return new XMLHttpRequest();
    }
    if (window.ActiveXObject){
        // code for IE6, IE5
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
    return null;
}

function isNumeriek(value){
    var anum=/(^\d+$)|(^\d+\.\d+$)/
    if (anum.test(value))
        return true;
    return false;
}

var xmlhttpVak;
var xmlhttpStudent;
var xmlhttpBibItem;
var xmlhttpVulStudent;

function vakDropdown(id){
    xmlhttpVak=GetXmlHttpObject();

    if (xmlhttpVak==null){
        alert ("Your browser does not support AJAX!");
        return;
    }

    var lijstMetStudenten = document.getElementById("lijstStudent");
    if ( lijstMetStudenten.hasChildNodes() ){
        while ( lijstMetStudenten.childNodes.length >= 1 ){
            lijstMetStudenten.removeChild( lijstMetStudenten.firstChild );
        }
    }

    xmlhttpVak.onreadystatechange=stateChangedVak;
    xmlhttpVak.open("GET","maakHoorcollegeXML.php?f=dropdown&id="+id,true);
    xmlhttpVak.send(null);
}

function stateChangedVak()
{
    if (xmlhttpVak.readyState==4){
        var antwoord = xmlhttpVak.responseXML;
        var vakken = antwoord.getElementsByTagName('vak');
        var select = document.getElementById("selectGroep");
        var vaknaam = document.getElementById("Vak").options[document.getElementById("Vak").selectedIndex].innerHTML;
        var studentDiv = document.getElementById("divStudent");
        select.selectedIndex = 0;

        if (vaknaam != "--Selecteer een vak--"){
            studentDiv.innerHTML = "voor <b>"+vaknaam+"</b>";
        } else {
            studentDiv.innerHTML = "";
        }
        allesSelecteren();
        verwijderStudent();

        /**
         * Leeg maken
         */
        select.options.length = 0;
        select.options[select.options.length] = new Option("--- Selecteer filter ---","niks");
        select.options[select.options.length] = new Option("Iedereen","alles");

        for (var i=0; i<vakken.length;i++){
            var id = vakken[i].childNodes[0].childNodes[0].nodeValue;
            var naam = vakken[i].childNodes[1].childNodes[0].nodeValue
            select.options[select.options.length] = new Option(naam, id);
        }

        select.options[select.options.length] = new Option("Studenten zonder groep","zondergroep");

    }
}

function studentDropdown(id){
    var gekozenVak = document.getElementById("Vak").options[document.getElementById("Vak").selectedIndex].value;
    if (isNumeriek(gekozenVak)){
        xmlhttpStudent=GetXmlHttpObject();

        if (xmlhttpStudent==null){
            alert ("Your browser does not support AJAX!");
            return;
        }

        xmlhttpStudent.onreadystatechange=stateChangedStudent;
        if (id == "alles"){
            xmlhttpStudent.open("GET","maakHoorcollegeXML.php?f=studentenAlles&vakid="+gekozenVak,true);
        } else {
            if (id=="zondergroep"){
                xmlhttpStudent.open("GET","maakHoorcollegeXML.php?f=studentenZonderGroep&vakid="+gekozenVak,true);
            } else {
                xmlhttpStudent.open("GET","maakHoorcollegeXML.php?f=studenten&vakid="+gekozenVak+"&groepid="+id,true);
            }
        }
        xmlhttpStudent.send(null);

    } else {
        alert('U moet eerst een vak selecteren.');
    }

}

function stateChangedStudent()
{
    if (xmlhttpStudent.readyState==4){
        var antwoord = xmlhttpStudent.responseXML;
        var vakken = antwoord.getElementsByTagName('student');
        var select = document.getElementById("lijstStudent");

        /**
         * Leeg maken
         */
        select.options.length = 0;


        for (var i=0; i<vakken.length;i++){
            var id = vakken[i].childNodes[0].childNodes[0].nodeValue;
            var naam = vakken[i].childNodes[1].childNodes[0].nodeValue
            select.options[select.options.length] = new Option(naam, id);
        }

    }
}

function selecteerStudent(){
    var kieslijst = document.getElementById("lijstStudent");
    var geselecteerd = document.getElementById("lijstStudentGeselecteerd");

    for (var i = 0; i < kieslijst.options.length; i++) {
        if (kieslijst.options[i].selected){
            geselecteerd.options[geselecteerd.options.length] = new Option(kieslijst.options[i].innerHTML, kieslijst.options[i].value);
        }
    }
}

function verwijderStudent(){
    var geselecteerd = document.getElementById("lijstStudentGeselecteerd");
    var lengte = geselecteerd.options.length
    for (var i = 0; i < lengte; i++) {
        if (geselecteerd.options[i]!=null && geselecteerd.options[i].selected){
            geselecteerd.removeChild(geselecteerd.options[i]);
            i--;
        }
    }
}

function allesSelecteren(){
    var form = document.getElementById("lijstStudentGeselecteerd");
    for (var i=0; i<form.options.length; i++){
        form.options[i].selected = true;
    }
}



var volgorde = new Array()
volgorde[1]="flv";
volgorde[2]="txt";
volgorde[3]="mp3";

var ids = new Array()
ids[1]=0;
ids[2]=0;
ids[3]=0;

var teller = 1;

/**
 * Nodig bij wijzigen van hoorcollege en bij foute invoer bij het maken
 */
function zetHoorcollegeWaarden(onderwerpid, flvid, mp3id, txtid,studenten){
    teller = 1;
    vakDropdown(document.getElementById("Vak")[document.getElementById('Vak').selectedIndex].value);
    autoSubmit(document.body.getElementsByTagName('form')[0],'autoSubmit2')
    zetOnderwerp(onderwerpid);
    ids[1]=flvid;
    ids[2]=txtid;
    ids[3]=mp3id;
    zetBibItems();
    vulGeselecteerdeStudenten(studenten);
}

function zetOnderwerp(id){
    var onderwerp = document.getElementById("ond");
    for (var i = 0; i < onderwerp.options.length; i++) {
        if(onderwerp.options[i].value == id) onderwerp.options[i].selected = true;
    }

}

function zetBibItems(){
    xmlhttpBibItem=GetXmlHttpObject();

    if (xmlhttpBibItem==null){
        alert ("Your browser does not support AJAX!");
        return;
    }

    xmlhttpBibItem.onreadystatechange=stateChangedBibItem;
    xmlhttpBibItem.open("GET","maakHoorcollegeXML.php?f=bibitem&bibid="+ids[teller],true);
    xmlhttpBibItem.send(null);
}

function stateChangedBibItem()
{
    if (xmlhttpBibItem.readyState==4){
        var antwoord = xmlhttpBibItem.responseText;
        var naam = "technische fout";

        if (volgorde[teller] == "txt") naam = "tekst";
        if (volgorde[teller] == "mp3") naam = "audio";
        if (volgorde[teller] == "flv") naam = "video";

        if (ids[teller]>0){
            document.getElementById("feedback_"+volgorde[teller]).innerHTML = "Geselecteerde " + naam + ' : <strong>"' + antwoord +'"</strong>.';
            document.getElementById("button_"+volgorde[teller]).value="Wijzig keuze";
            document.getElementById("keuze_"+volgorde[teller]).value = ids[teller];
        } else {
            document.getElementById("feedback_"+volgorde[teller]).innerHTML = "Nog geen " + naam + " geselecteerd.";
            document.getElementById("button_"+volgorde[teller]).value="Blader in bibliotheek";
            document.getElementById("keuze_"+volgorde[teller]).value = "-1";
        }

        if (teller < 3){
            teller++;
            zetBibItems();
        }
    }
}

function vulGeselecteerdeStudenten(studenten){
    xmlhttpVulStudent=GetXmlHttpObject();

    if (xmlhttpVulStudent==null){
        alert ("Your browser does not support AJAX!");
        return;
    }

    xmlhttpVulStudent.onreadystatechange=stateChangedVulStudenten;
    xmlhttpVulStudent.open("GET","maakHoorcollegeXML.php?f=studentenVanIds&studenten="+studenten,true);
    xmlhttpVulStudent.send(null);
}

function stateChangedVulStudenten()
{
    if (xmlhttpVulStudent.readyState==4){
        var antwoord = xmlhttpVulStudent.responseXML.getElementsByTagName('student');
        var select = document.getElementById("lijstStudentGeselecteerd");

        for (var i=0; i<antwoord.length;i++){
            var id = antwoord[i].childNodes[0].childNodes[0].nodeValue;
            var naam = antwoord[i].childNodes[1].childNodes[0].nodeValue
            select.options[select.options.length] = new Option(naam, id);
        }
    }
}

function verwijderCommentaar(id){
    var xHRObjectCommentVerwijderen;
    if (window.XMLHttpRequest) {
        xHRObjectCommentVerwijderen = new XMLHttpRequest();
    }
    else
    if (window.ActiveXObject) {
        xHRObjectCommentVerwijderen = new ActiveXObject("Microsoft.XMLHTTP");
    }

    xHRObjectCommentVerwijderen.onreadystatechange = getDataCommentVerwijderen; // callback
    xHRObjectCommentVerwijderen.open("GET", "verwerkCommentaarVerwijderen.php?id="+id);
    xHRObjectCommentVerwijderen.send(null);
}

function getDataCommentVerwijderen(){
    if(xHRObjectCommentToevoegen.readyState == 4 && xHRObjectCommentToevoegen.status == 200){
//lege callback
}
}


function maakBibliotheekItemsPopup(){
    newwindow=window.open('bibliotheekPopupAlleItems.php','biblio','height=550,width=750');
    if (window.focus) {
        newwindow.focus()
    }
    return false;
}

function VerwerkGroepSelectie(form){

    var groepid= document.Form.Groepen.options[document.Form.Groepen.options.selectedIndex].value;
    var hoorcollid = document.Form.hoorcollid.value;



    //genereer xmlhttp object
    var xmlhttp=GetXmlHttpObject();
    //Request
    xmlhttp.open("GET", "VerwerkGroepSelectie.php?gevraagdGroep="+groepid+"&gevraagdhoorcoll="+hoorcollid, false);
    xmlhttp.send(null);

    //Div waar content inkomt
    var studentDiv = document.getElementById("studentDiv");

    //grootte van ontvangen xml bestand opvragen
    var studenten = xmlhttp.responseXML.getElementsByTagName('student');
    var size = studenten.length;

  
    //Huidige div leegmaken
    while ( studentDiv.firstChild ){
        studentDiv.removeChild( studentDiv.firstChild );
    }
           //error div leegmaken
           var foutdiv=document.getElementById('foutmelding');
           while ( foutdiv.firstChild ){
                    foutdiv.removeChild( foutdiv.firstChild );
                }

    // if geldige groep gekozen, haal info studenten op en geef weer
    if(groepid!=0){


        //voor elke <student>
        for(var i=0;i<size;i++){


            //tabel
            var myTable = document.createElement("table");
            myTable.setAttribute('width','700');
            myTable.setAttribute('border','0');
            myTable.setAttribute('cellspacing','0');
            myTable.setAttribute('cellpadding','0');
            myTable.setAttribute('font-size','13px');

            //Tr naamvermelding
            var TR1 = document.createElement("tr");
            var TD1 = document.createElement("td");
            TD1.setAttribute('className',"title-section");
            TD1.setAttribute('bgcolor',"#CFE7CF");
            TD1.setAttribute('colSpan',"5");
            var naam=xmlhttp.responseXML.getElementsByTagName('naam')[i].firstChild.data;
            var bvak = document.createElement("b");
            bvak.appendChild(document.createTextNode(naam));
            TD1.appendChild(bvak);
            TR1.appendChild(TD1);

            //enkel indien er vragen in het hoorcollege zitten
            if(studenten[i].getElementsByTagName('rootvraag').length!=0){
                //tr voor kolomnamen
                var TR2 = document.createElement("tr");
                //Indent
                var newTd = document.createElement("td");
                newTd.appendChild(document.createTextNode('\u00A0'));
                newTd.setAttribute('colSpan',"0");
                TR2.appendChild(newTd);


                //kolom1
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#FFFBD6");
                var boldfield=document.createElement("b");
                var ufield=document.createElement("em");
                ufield.appendChild(document.createTextNode('Vraag'));
                boldfield.appendChild(ufield);
                TD1.setAttribute('width','25%');
                TD1.appendChild(boldfield);
                TR2.appendChild(TD1);
                //kolom2
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#FFFBD6");
                boldfield=document.createElement("b");
                ufield=document.createElement("em");
                ufield.appendChild(document.createTextNode('Oplossing'));
                boldfield.appendChild(ufield);
                TD1.setAttribute('width','25%');
                TD1.appendChild(boldfield);
                TR2.appendChild(TD1);
                //kolom3
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#FFFBD6");
                boldfield=document.createElement("b");
                ufield=document.createElement("em");
                ufield.appendChild(document.createTextNode('Antwoord'));
                boldfield.appendChild(ufield);
                TD1.appendChild(boldfield);
                TD1.setAttribute('width','25%');
                TR2.appendChild(TD1);
                //kolom4
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#FFFBD6");
                boldfield=document.createElement("b");
                ufield=document.createElement("em");
                ufield.appendChild(document.createTextNode('Resultaat'));
                boldfield.appendChild(ufield);
                TD1.appendChild(boldfield);
                TD1.setAttribute('width','25%');
                TR2.appendChild(TD1);

                myTable.appendChild(TR1);
                myTable.appendChild(TR2);
            }

            //als geen vragen toegekend zijn aan dit hoorcollege, enkel de tr met studentnaam tonen.
            if(studenten[i].getElementsByTagName('rootvraag').length==0){
                myTable.appendChild(TR1);
            }


            //Vul tabel op met XML gegevens
            var vragen=studenten[i].getElementsByTagName('rootvraag');
            if(vragen.length==0){
                foutdiv=document.getElementById('foutmelding');
                while ( foutdiv.firstChild ){
                    foutdiv.removeChild( foutdiv.firstChild );
                }

                var err=document.createElement('p');
                err.id='fout';
                var txt= document.createTextNode("Dit hoorcollege bevat geen vragen.");
                err.appendChild(txt);
                err.appendChild(document.createElement('br'));
                err.appendChild(document.createElement('br'));
                foutdiv.appendChild(err);
            }
            for(var j=0;j<vragen.length;j++){

                var TR3=document.createElement("tr");

                var newTd = document.createElement("td");
                newTd.appendChild(document.createTextNode('\u00A0\u00A0\u00A0'));
                newTd.setAttribute('colSpan',"0");
                TR3.appendChild(newTd);

                //Vraag
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                p=document.createElement("p");
                p.appendChild(document.createTextNode(vragen[j].getElementsByTagName('Vraag')[0].firstChild.data));
                TD1.appendChild(p);
                TR3.appendChild(TD1);

                //Oplossing
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                p=document.createElement("p");
                p.appendChild(document.createTextNode(vragen[j].getElementsByTagName('juistantwoord')[0].firstChild.data));
                TD1.appendChild(p);
                TR3.appendChild(TD1);

                //Antwoord
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                p=document.createElement("p");
                p.appendChild(document.createTextNode(vragen[j].getElementsByTagName('gegevenantwoord')[0].firstChild.data));
                TD1.appendChild(p);
                TR3.appendChild(TD1);


                //Resultaat
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                p=document.createElement("p");
                p.appendChild(document.createTextNode(vragen[j].getElementsByTagName('correct')[0].firstChild.data));
                TD1.appendChild(p);
                TR3.appendChild(TD1);

                myTable.appendChild(TR3);
            }


               myTable.appendChild(document.createElement('br'));
             

                //VBC Sectie
                var vbc=studenten[i].getElementsByTagName('VBC');
                var vbcIsIngeschakeld=studenten[i].getElementsByTagName('VBCNietUitgevoerd');
                var TRvbc=document.createElement("tr");

                //Indent
                var newTd = document.createElement("td");
                newTd.appendChild(document.createTextNode('\u00A0\u00A0\u00A0'));
                newTd.setAttribute('colSpan',"0");
                TRvbc.appendChild(newTd);
                //titel
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#FFFBD6");
                p=document.createElement("b");
                p.appendChild(document.createTextNode('Video Bekeken Controle'));
                TD1.appendChild(p);
                TD1.setAttribute('colspan',0);
                TRvbc.appendChild(TD1);
                myTable.appendChild(TRvbc);

                //als vbc verplicht voor deze student
                if(vbc.length!=0){
                    
                //Kolomnamen
                 TRvbc=document.createElement("tr");

                //Indent
                var newTd = document.createElement("td");
                newTd.appendChild(document.createTextNode('\u00A0\u00A0\u00A0'));
                newTd.setAttribute('colSpan',"0");
                TRvbc.appendChild(newTd);

                //kolom1
            
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                var boldfield=document.createElement("b");
                var ufield=document.createElement("em");
                ufield.appendChild(document.createTextNode('Te Bekijken'));
                boldfield.appendChild(ufield);
                p=document.createElement("p");
                p.appendChild(boldfield);
                TD1.setAttribute('width','25%');
                TD1.appendChild(p);
                TRvbc.appendChild(TD1);
                //kolom2
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                boldfield=document.createElement("b");
                ufield=document.createElement("em");
                ufield.appendChild(document.createTextNode('Aantal getoonde hoofden'));
                boldfield.appendChild(ufield);
                TD1.setAttribute('width','25%');
                TD1.appendChild(boldfield);
                TRvbc.appendChild(TD1);
                //kolom3
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                boldfield=document.createElement("b");
                ufield=document.createElement("em");
                ufield.appendChild(document.createTextNode('Aantal geklikte hoofden'));
                boldfield.appendChild(ufield);
                TD1.appendChild(boldfield);
                TD1.setAttribute('width','25%');
                TRvbc.appendChild(TD1);
                //kolom4
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                boldfield=document.createElement("b");
                ufield=document.createElement("em");
                ufield.appendChild(document.createTextNode('Score'));
                boldfield.appendChild(ufield);
                TD1.appendChild(boldfield);
                TD1.setAttribute('width','25%');
                TRvbc.appendChild(TD1);

                  myTable.appendChild(TRvbc);

                 //Data
                 TRvbc=document.createElement("tr");

                //Indent
                var newTd = document.createElement("td");
                newTd.appendChild(document.createTextNode('\u00A0\u00A0\u00A0'));
                newTd.setAttribute('colSpan',"0");
                TRvbc.appendChild(newTd);

                //kolom1
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                boldfield.appendChild(ufield);
                p=document.createElement("p");
                p.appendChild(document.createTextNode(vbc[0].getElementsByTagName('teBekijken')[0].firstChild.data));
                TD1.setAttribute('width','25%');
                TD1.appendChild(p);
                TRvbc.appendChild(TD1);
                //kolom2
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                boldfield.appendChild(ufield);
                p=document.createElement("p");
                var antget =vbc[0].getElementsByTagName('AantalGetoond')[0].firstChild.data;
                p.appendChild(document.createTextNode(antget));
                TD1.setAttribute('width','25%');
                TD1.appendChild(p);
                TRvbc.appendChild(TD1);
                //kolom3
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                boldfield.appendChild(ufield);
                p=document.createElement("p");
                var antgek =vbc[0].getElementsByTagName('AantalGeklikt')[0].firstChild.data;
                p.appendChild(document.createTextNode(antgek));
                TD1.setAttribute('width','25%');
                TD1.appendChild(p);
                TRvbc.appendChild(TD1);
                //kolom4
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                boldfield.appendChild(ufield);
                p=document.createElement("p");
                var score= (antgek/antget)*100;
                if(antget!=0){
                p.appendChild(document.createTextNode(Math.round(score)+'%'));
                }
                else {p.appendChild(document.createTextNode('-'));}
                TD1.setAttribute('width','25%');
                TD1.appendChild(p);
                TRvbc.appendChild(TD1);

                myTable.appendChild(TRvbc);
                }

            else {
                TRvbc=document.createElement("tr");
                
                //Indent
                var newTd = document.createElement("td");
                newTd.appendChild(document.createTextNode('\u00A0\u00A0\u00A0'));
                newTd.setAttribute('colSpan',"0");
                TRvbc.appendChild(newTd);

               if(vbcIsIngeschakeld.length==0){
                //VBC uitgeschakeld voor deze student
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                p=document.createElement('p');
                p.appendChild(document.createTextNode('VBC is voor deze student niet ingeschakeld.'));
                TD1.setAttribute('colspan','4');
                TD1.appendChild(p);
                TRvbc.appendChild(TD1);
                myTable.appendChild(TRvbc);
               }
               else{
                //VBC is ingeschakeld maar de student heeft het hoorcollege nog niet bekeken
                TD1= document.createElement("td");
                TD1.setAttribute('bgcolor',"#F0F0F0");
                p=document.createElement('p');
                p.appendChild(document.createTextNode('De student heeft het hoorcollege nog niet bekeken.'));
                TD1.setAttribute('colspan','4');
                TD1.appendChild(p);
                TRvbc.appendChild(TD1);
                myTable.appendChild(TRvbc);
               }
            }
        





            document.getElementById('studentDiv').appendChild(myTable);
            document.getElementById('studentDiv').appendChild(document.createElement('br'));
            document.getElementById('studentDiv').appendChild(document.createElement('br'));
            document.getElementById('studentDiv').appendChild(document.createElement('br'));


        }

      if(size==0){
                var foutdiv=document.getElementById('foutmelding');
                while ( foutdiv.firstChild ){
                    foutdiv.removeChild( foutdiv.firstChild );
                }

                var err=document.createElement('p');
                err.id='fout';
                var txt= document.createTextNode("Deze categorie bevat geen studenten die dit hoorcollege volgen.");
                err.appendChild(txt);
                err.appendChild(document.createElement('br'));
                err.appendChild(document.createElement('br'));
                foutdiv.appendChild(err);
      }







    }


}

var xmlhtppSelectie;
var xmlhttpDel;
var xmlhttpDelVraag;

function zetGeselecteerdeMPVraag(vraag, selectie){
    xmlhtppSelectie=GetXmlHttpObject();

    if (xmlhtppSelectie==null){
        alert ("Your browser does not support AJAX!");
        return;
    }

    //xmlhtppSelectie.onreadystatechange=zetSelectieState; //callback niet nodig
    xmlhtppSelectie.open("GET","activeerMC.php?actie=select&zetGeselecteerdVraag="+ vraag +"&zetGeselecteerdAnt="+selectie,true);
    xmlhtppSelectie.send(null);
}

function delGeselecteerdeMPOptie(vraag, selectie){
    xmlhttpDel=GetXmlHttpObject();

    if (xmlhttpDel==null){
        alert ("Your browser does not support AJAX!");
        return;
    }

    xmlhttpDel.onreadystatechange=delSelectieState; //callback niet nodig
    xmlhttpDel.open("GET","activeerMC.php?actie=del&zetGeselecteerdVraag="+ vraag +"&zetGeselecteerdAnt="+selectie);
    xmlhttpDel.send(null);
}

function delSelectieState(){
    if(xmlhttpDel.readyState == 4){
        window.location = window.location.href;
    }
}

function delGeselecteerdeMPVraag(vraag){
    xmlhttpDelVraag=GetXmlHttpObject();

    if (xmlhttpDelVraag==null){
        alert ("Your browser does not support AJAX!");
        return;
    }

    xmlhttpDelVraag.onreadystatechange=delVraagSelectieState; //callback niet nodig
    xmlhttpDelVraag.open("GET","activeerMC.php?actie=delVraag&zetGeselecteerdVraag="+ vraag);
    xmlhttpDelVraag.send(null);
}

function delVraagSelectieState(){
    if(xmlhttpDelVraag.readyState == 4){
        window.location = window.location.href;
    }
}