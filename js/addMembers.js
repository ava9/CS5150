var counter = 1;
var limit = 100;
function addInput(divName){
     if (counter == limit)  {
          alert("You have reached the limit of adding " + counter + " inputs");
     }
     else {
          var newdiv = document.createElement('div');
          newdiv.innerHTML = "Band Member " + (counter + 1) + " <br><input type='email' class='form-control' name='myInputs[]' placeholder='friend@gmail.com'>";
          document.getElementById(divName).appendChild(newdiv);
          counter++;
     }
}