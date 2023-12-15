function sortDropdownList(ddl){

    var options = [].slice.apply(ddl.options, [0]);
    ddl.innerHTML = "";
    var sorted = options.sort(function(a,b){     
       return +(a.innerText) - +(b.innerText);
    });

    for(var i = 0; i < sorted.length; i++){
      ddl.options.add(sorted[i]);
    }  

}
    
var parentSelect = document.getElementById("product");
var childSelect = document.getElementById("power");
var options = [].slice.apply(childSelect, [0]);
var emptyOption = options[0];
childSelect.innerHTML = "";

parentSelect.addEventListener("change", function(e){

    var selectedId = parentSelect.options[parentSelect.selectedIndex].id;
    childSelect.innerHTML = "";
    childSelect.options.add(emptyOption);
    for(var i = 0; i < options.length; i++){

        if( options[i].getAttribute("data-name") == selectedId ){

           childSelect.options.add(options[i]);

        }
    }

    sortDropdownList(childSelect);

});