
        function ClearPlaceHolder (input) {
            if (input.value == input.defaultValue) {
                input.value = "";
                input.style.color='#000';
            }
        }
        function SetPlaceHolder (input) {
            if (input.value == "") {    
            		input.style.color='#aaa';        		
                input.value = input.defaultValue;
            }
        }
        function LoadPlaceHolder () {
          document.getElementsByClassName("userField").value = document.getElementById("userField").defaultValue;
        }        
        window.onload = function() {
   		 	  document.getElementsByClassName('userField').value = "UserName";
  		  }



function showModal(){
	var ventana = document.getElementById('modalWindow');
	ventana.style.marginTop = "30px";
	ventana.style.display = 'block';	
}

function hideModal(){
	var ventana = document.getElementById('modalWindow');
	ventana.style.display = 'none';	
}	