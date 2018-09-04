
/* pagination goto*/

function myFunction() {
    var customVal = parseInt(document.getElementById("jumpTo").value);
    var maxVal = parseInt(document.getElementById("jumpTo").max);
    var current = document.getElementsByClassName("cust-page-numbers current")[0].text;
    // console.log(current);
    var urlValue = document.getElementById("hiddenTxt").value;

   if(maxVal >= customVal){
    window.location = urlValue + customVal ;
   }
   else{
    document.getElementById("jumpTo").value = current;
   }
  
}

/* !pagination goto*/