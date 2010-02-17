function autoSubmit(form)
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

 //alert(xmlhttp.responseXML);
              
                //haal optie uit de XML en voeg toe aan dropdown
                var elems = xmlhttp.responseXML.getElementsByTagName("Onderwerp");
 		var size = elems.length;
                while(form.onderwerp.options.length>1){
                form.onderwerp.options[1]=null;
                }
                for(i = 0; i < size; i++){
                 //onderwerp = naam van het onderwerp, id = id van het onderwerp, gebruiker krijgt de naam te zien, de var die doorgegoven word in 'onchange' is de id
                 var inh=xmlhttp.responseXML.getElementsByTagName('Onderwerp')[i].firstChild.data;
                 var id=xmlhttp.responseXML.getElementsByTagName('Id')[i].firstChild.data;
                 
                 form.onderwerp.options.add( new Option(inh,id));




}
}

function autoSubmit2(form)
{

     var vakid=form.vak.options[form.vak.options.selectedIndex].value;
     var ondid=form.onderwerp.options[form.onderwerp.options.selectedIndex].value;




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

         
              //Vul Tabel Op
               //var t1=document.getElementById('t1');
                //for (var g=0;g<size;g++){
                //var table=document.createElement('TABLE');
               //table.border='1';
                 // var tbdy=document.createElement('TBODY');
                //table.appendChild(tbdy);
                //for (var j=0;j<2;j++){
                //var tr=document.createElement('TR');
                  //tbdy.appendChild(tr);
                   //for (var k=0;k<2;k++){
                   //var td=document.createElement('TD');
                 //td.width='100';
                //var inh=xmlhttp.responseXML.getElementsByTagName('Naam')[g].firstChild.data;
                //td.appendChild(document.createTextNode(inh));
                 //tr.appendChild(td);
                     //}
                       //}
                       //t1.appendChild(table);
                     //}

                     
                //Tabel aanmaken
                var elems = xmlhttp.responseXML.getElementsByTagName('Naam');
 		var size = elems.length;

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
                   var myTable = document.getElementById("myTable");
                   var newTR = document.createElement("tr");
                   var newName = document.createElement("td");
                   newName.innerHTML = xmlhttp.responseXML.getElementsByTagName('Id')[i].firstChild.data;
                   var newPhone = document.createElement("td");
                   newPhone.innerHTML = xmlhttp.responseXML.getElementsByTagName('Naam')[i].firstChild.data;
                   newTR.appendChild(newName);
                   newTR.appendChild(newPhone);
                   myTable.appendChild(newTR);
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



