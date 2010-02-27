function autoSubmit(form,var1)
{


    var vakid=form.vak.options[form.vak.options.selectedIndex].value;

    //reset velden
    var TableDiv=document.getElementById('TableDiv');
  while ( TableDiv.firstChild ){
        TableDiv.removeChild( TableDiv.firstChild );
    }
        var ondDiv=document.getElementById('onderwerpform');
  while ( ondDiv.firstChild ){
        ondDiv.removeChild( ondDiv.firstChild );
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
    knop.onclick = voegOndToe;
    document.getElementById('kiesond').appendChild( document.createTextNode( '\u00A0\u00A0\u00A0' ) );
    document.getElementById('kiesond').appendChild(knop);

}

function autoSubmitBeheer(form)
{


    var vakid=form.vak.options[form.vak.options.selectedIndex].value;


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
    sel.onchange=function(){
        GenHoorcollDropdown(this.form);
    };
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

    var knop2= document.createElement('input');
    knop2.setAttribute('type','button');
    knop2.setAttribute('name','EditOnd');
    knop2.setAttribute('value','Edit');
    knop2.setAttribute('onclick',"GoTo3('editOnderwerp.php?')");
    document.getElementById('kiesond').appendChild( document.createTextNode( '\u00A0\u00A0\u00A0\u00A0\u00A0' ) );
    document.getElementById('kiesond').appendChild(knop2);

    
    var knop= document.createElement('input');
    knop.setAttribute('type','button');
    knop.setAttribute('name','DeleteOnd');
    knop.setAttribute('value','Verwijder');
    knop.setAttribute('onclick',"GoTo2('DeleteOnderwerp.php?')");
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
    xmlhttp.open("GET", "VerwerkDropdown2.php?gevraagdVak="+vakid+"&gevraagdOnd="+ondid, false);
    xmlhttp.send(null);

    var TableDiv = document.getElementById("TableDiv");


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
        TD1.setAttribute('colSpan',"6");
        var bvak = document.createElement("b");
        bvak.appendChild(document.createTextNode(vak));
        TD1.appendChild(bvak);
        TR1.appendChild(TD1);

        myTable.appendChild(TR1);

        //Tr ondervermelding + wijzig en edit opties
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
        TR2.appendChild(TD4);
        //remove onderwerp
        var TD5= document.createElement("td");
        TD5.setAttribute('bgcolor',"#FFF5D2");
        TD5.setAttribute('colSpan',"1");
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


       myTable.appendChild(TR2);

       //Tr voor namen kolommen
        var TR3 = document.createElement("tr");
        var TDw = document.createElement("td");
        var TDw2= document.createElement("td");
        //whitespace td
        TDw.appendChild(document.createTextNode('\u00A0'));
        TDw2.appendChild(document.createTextNode('\u00A0'));
        TR3.appendChild(TDw);TR3.appendChild(TDw2);

 

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
        TD6.setAttribute('width','20%');
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

                   newField = document.createElement("td");
                   link = document.createElement("a");
                   link.appendChild(document.createTextNode('Details'));
                   link.setAttribute('href',"details.php");
                   newField.setAttribute('bgcolor',"#F0F0F0");
                   newField.appendChild(link);
                   newTR.appendChild(newField);


                   newField = document.createElement("td");
                   link = document.createElement("a");
                   link.appendChild(document.createTextNode('Wijzig'));
                   link.setAttribute('href',"editHoorcollege.php");
                   newField.appendChild(link);
                   newField.setAttribute('bgcolor',"#F0F0F0");
                   newTR.appendChild(newField);

                   var id=xmlhttp.responseXML.getElementsByTagName('Id')[i].firstChild.data;
                   newField = document.createElement("td");
                   newField.setAttribute('bgcolor',"#F0F0F0");
                   link = document.createElement("a");
                   link.appendChild(document.createTextNode('Verwijder'));
                   link.setAttribute('href',"DeleteHoorcollege.php?gevraagdhoorcoll="+id);
                   link.setAttribute('onclick', "return confirm('Bent u zeker dat u het hoorcollege \""+xmlhttp.responseXML.getElementsByTagName('Naam')[i].firstChild.data+"\" wilt verwijderen?')");
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

                   myTable.appendChild(newTR);

    }
        TableDiv.appendChild(myTable);
   
    

 



               
                   
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



function GenHoorcollDropdown(form){

    var vakid= document.Form.vak.options[document.Form.vak.options.selectedIndex].value;
    var ondid= document.Form.Ond.options[document.Form.Ond.options.selectedIndex].value;

if(ondid!=0){
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
    xmlhttp.open("GET", "VerwerkDropdown2.php?gevraagdVak="+vakid+"&gevraagdOnd="+ondid, false);
    xmlhttp.send(null);



    //haal optie uit de XML en voeg toe aan dropdown
    var elems = xmlhttp.responseXML.getElementsByTagName("Naam");
    var size = elems.length;
    
    var hoorcolldiv=document.getElementById("HoorcollegeToevoegen")
    //div dynamisch opvullen met een select gebaseerd op het gekozen onderwerp
    while ( hoorcolldiv.firstChild ){
        hoorcolldiv.removeChild( hoorcolldiv.firstChild );
    }
    var txtnode= document.createTextNode('Kies een hoorcollege:');
    document.getElementById("HoorcollegeToevoegen").appendChild(txtnode);


    var brk=document.createElement('br');
    document.getElementById("HoorcollegeToevoegen").appendChild(brk);
    var sel2=document.createElement('select');
    sel2.name='Hoorcollegeselect';
    var opt= document.createElement("option");
    opt.text='--Selecteer een hoorcollege--';
    opt.value=0;
    sel2.options.add(opt);
    document.getElementById("HoorcollegeToevoegen").appendChild(sel2);
    while(sel2.options.length>1){
        sel2.options[1]=null;
    }
    for(i = 0; i < size; i++){
        //onderwerp = naam van het onderwerp, id = id van het onderwerp, gebruiker krijgt de naam te zien, de var die doorgegoven word in 'onchange' is de id
        var inh=xmlhttp.responseXML.getElementsByTagName('Naam')[i].firstChild.data;
        var id=xmlhttp.responseXML.getElementsByTagName('Id')[i].firstChild.data;
        sel2.options.add( new Option(inh,id));
    }

    var knop= document.createElement('input');
    knop.setAttribute('type','button');
    knop.setAttribute('name','DeleteHoorcollege');
    knop.setAttribute('value','Delete');
    knop.setAttribute('onclick',"GoTo('DeleteHoorcollege.php?')");
    document.getElementById('HoorcollegeToevoegen').appendChild( document.createTextNode( '\u00A0\u00A0\u00A0' ) );
    document.getElementById('HoorcollegeToevoegen').appendChild(knop);
                
}
}
function GoTo(url)
{

    var hoorcollid= document.Form.Hoorcollegeselect.options[document.Form.Hoorcollegeselect.options.selectedIndex].value;
    var hoorcollnaam= document.Form.Hoorcollegeselect.options[document.Form.Hoorcollegeselect.options.selectedIndex].text;
    if(hoorcollid!=0){
        var confirmed = confirm("Bent u zeker dat u het hoorcollege \""+hoorcollnaam+"\" wilt verwijderen?");
        if (confirmed){
            window.location.href = url+"gevraagdhoorcoll="+hoorcollid;
        }
    }
}

//goto voor onderwerp deleten
function GoTo2(url)
{
    var ondid= document.Form.Ond.options[document.Form.Ond.options.selectedIndex].value;
    var ondnaam= document.Form.Ond.options[document.Form.Ond.options.selectedIndex].text;
    
    if(ondid!=0){
        var confirmed = confirm("Bent u zeker dat u het onderwerp \""+ondnaam+"\" wilt verwijderen?");
        if (confirmed){
            window.location.href = url+"gevraagdond="+ondid;
        }
    }
}

//goto voor onderwerp editen
function GoTo3(url)
{
    var ondid= document.Form.Ond.options[document.Form.Ond.options.selectedIndex].value;
    var ondnaam= document.Form.Ond.options[document.Form.Ond.options.selectedIndex].text;

    if(ondid!=0){
            window.location.href = url+"gevraagdond="+ondid+"&gevraagdondnaam="+ondnaam;

    }
}

function maakHoorcollegePopup(type){
        var geselecteerd = document.getElementById("keuze_"+type).value;
    	newwindow=window.open('bibliotheekPopup.php?type='+type+'&geselecteerd='+geselecteerd,'biblio','height=550,width=750');
	if (window.focus) {newwindow.focus()}
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

var xmlhttp;

function vakDropdown(id){
    xmlhttp=GetXmlHttpObject();

    if (xmlhttp==null){
      alert ("Your browser does not support AJAX!");
      return;
     }

    xmlhttp.onreadystatechange=stateChanged;
    xmlhttp.open("GET","maakHoorcollegeXML.php?f=dropdown&id="+id,true);
    xmlhttp.send(null);
}

function stateChanged()
{
    if (xmlhttp.readyState==4){
        var antwoord = xmlhttp.responseXML;
        var vakken = antwoord.getElementsByTagName('vak');
        for (var i=0; i<vakken.length;i++){
            var id = vakken[i].childNodes[0].childNodes[0].nodeValue;
            var naam = vakken[i].childNodes[1].childNodes[0].nodeValue
            alert(naam+id);
        }
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