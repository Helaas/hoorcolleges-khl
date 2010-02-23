function autoSubmit(form,var1)
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


  xmlhttp.onreadystatechange=Verwerk;
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
                 sel.onchange=function(){autoSubmit2(this.form);};
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


  xmlhttp.onreadystatechange=Verwerk;
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
                 sel.onchange=function(){GenHoorcollButton(this.form);};
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


function autoSubmit2(form)
{

     var vakid=form.vak.options[form.vak.options.selectedIndex].value;
     var ondid=form.Ond.options[form.Ond.options.selectedIndex].value;




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

  xmlhttp.onreadystatechange=Verwerk;
  //xmlhttp request om via php een xml pagina aan te maken met gegevens over de hoorcolleges die bij dit vak en onderwerp passen
  xmlhttp.open("GET", "VerwerkDropdown2.php?gevraagdVak="+vakid+"&gevraagdOnd="+ondid, false);
  xmlhttp.send(null);

                 var myTable = document.getElementById("myTable");


                 //Huidige tabel leegmaken om hem te vervangen met een nieuwe
                 while ( myTable.firstChild ){myTable.removeChild( myTable.firstChild );}

                   //grootte xml bestand opvragen
                   var elems = xmlhttp.responseXML.getElementsByTagName('Naam');
 		   var size = elems.length;
                 
                  if(size>0){
                   var TR1 = document.createElement("tr");
                   var TH1 = document.createElement("th");

                   TH1.id='TitelId';
                   TR1.appendChild(TH1);


                   var TH2 = document.createElement("th");
                   TH2.id='TitelCollege';
                   TR1.appendChild(TH2);

                   myTable.appendChild(TR1);

                  



                  //aanmaken titel kolom 1
                   var titel=document.createElement('p');
                   titel.innerHTML='Id';
                   document.getElementById("TitelId").appendChild(titel);

                   //aanmaken titel colom 2
                   var titel2=document.createElement('p');
                   titel2.innerHTML='Hoorcollege';
                   document.getElementById("TitelCollege").appendChild(titel2);




                   
                   
                   //tabel opvullen met gegevens uit de xml
                   for(i=0;i<size;i++){
                   var newTR = document.createElement("tr");
                   var newId = document.createElement("td");
                   newId.innerHTML = xmlhttp.responseXML.getElementsByTagName('Id')[i].firstChild.data;
                   var newName = document.createElement("td");
                   newName.innerHTML = xmlhttp.responseXML.getElementsByTagName('Naam')[i].firstChild.data;
                   newTR.appendChild(newId);
                   newTR.appendChild(newName);
                   myTable.appendChild(newTR);
                   }
                  }

                   else{
                   var nieuweTR = document.createElement("tr");
                   var msg = document.createElement("td");
                   msg.appendChild(document.createTextNode('Dit onderwerp heeft geen hoorcolleges'));
                   nieuweTR.appendChild(msg);
                   myTable.appendChild(nieuweTR);

                   }



               
                   

}





function Verwerk()
{

if(xmlhttp.readyState == 4 && xmlHttp.status == 200)
{
    alert('reached')
               //haal optie uit de XML en voeg toe aan dropdown
                var elems = xmlhttp.responseXML.getElementsByTagName("Onderwerp");
 		var size = elems.length;
                form.onderwerp.options[0]=null;
                for(i = 0; i < size; i++){

                 //onderwerp = naam van het onderwerp, id = id van het onderwerp, gebruiker krijgt de naam te zien, de var die doorgegoven word in 'onchange' is de id
                 var inh=xmlhttp.responseXML.getElementsByTagName('Onderwerp')[i].firstChild.data;
                 var id=xmlhttp.responseXML.getElementsByTagName('Id')[i].firstChild.data;
                 form.onderwerp.options.add( new Option(inh,id));
}
}
}


function voegOndToe(){

 while ( document.getElementById("onderwerpform").firstChild ){document.getElementById("onderwerpform").removeChild( document.getElementById("onderwerpform").firstChild );}

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
                iveld.onkeyup=function(){valideerInput(this.form);};
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

     while ( document.getElementById("createCategorie").firstChild ){document.getElementById("createCategorie").removeChild( document.getElementById("createCategorie").firstChild );}

                var form = document.createElement('form');
                form.name=('CatForm');
                //Veld om de naam van het onderwerp in te geven
                var iveld= document.createElement('input');
                iveld.setAttribute('type','text');
                iveld.setAttribute('name','veld1');
                iveld.setAttribute('value','Geef hier de naam van de categorie in..');
                iveld.setAttribute('size','38');
                iveld.onkeyup=function(){valideerInput(this.form);};
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
while ( MijnDiv.firstChild ){MijnDiv.removeChild( MijnDiv.firstChild );}
form.submitknop.disabled=false;
}
else{
    form.submitknop.disabled=true;
    while ( MijnDiv.firstChild ){MijnDiv.removeChild( MijnDiv.firstChild );}
     var err=document.createElement('p');
     err.id='fout';
     var txt= document.createTextNode("Het onderwerp mag geen speciale tekens bevatten en moet ingevuld zijn.");
     err.appendChild(txt);
     MijnDiv.appendChild(err);
}
}



function GenHoorcollButton(form){

   var vakid= document.Form.vak.options[document.Form.vak.options.selectedIndex].value;
   var ondid= document.Form.Ond.options[document.Form.Ond.options.selectedIndex].value;


                      var Hdiv=document.getElementById("HoorcollegeToevoegen")
                      //while ( Hdiv.firstChild ){Hdiv.removeChild( Hdiv.firstChild );}
                      var txt=document.createElement('p');
                      var inh= document.createTextNode("------ Hier komt de functionaliteit voor het toevoegen van een hoorcollege aan het gekozen onderwerp ------");
                      txt.appendChild(inh);
                      Hdiv.appendChild(txt);
                


}


