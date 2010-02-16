function autoSubmit(form)
{


  var vakid=form.vak.options[form.vak.options.selectedIndex].value;
  xmlhttp = new XMLHttpRequest();
  xmlhttp.onreadystatechange=Verwerk;
  //xmlhttp request om via php een xml pagina aan te maken op basis van het meegegoven vakid
  xmlhttp.open("GET", "VerwerkDropdown.php?gevraagdVak="+vakid, true);
  xmlhttp.send(null);

   alert(xmlhttp.responseXML);

  


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

function autoSubmit2(form)
{


  var Onderwerp=form.onderwerp.options[form.onderwerp.options.selectedIndex].value;
  alert(Onderwerp);



}
function Verwerk()
{
if(xmlhttp.readyState == 4 && xmlHttp.status == 200)
{
}
}

