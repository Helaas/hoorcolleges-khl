

function autoSubmit(form)
{


  var vak=form.vak.options[form.vak.options.selectedIndex].value;
  xmlhttp = new XMLHttpRequest();
  xmlhttp.open("GET", "VerwerkDropdown.php?gevraagdVak="+vak, true);

   alert(vak);


   
}

