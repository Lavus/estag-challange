$(document).ready(function() {
    if (document.querySelectorAll(".refreshthepagenowindex").length > 0){
        window.location.href = "index.php";
    } else if (document.querySelectorAll(".refreshthepagenowproducts").length > 0){
        window.location.href = "products.php";
    } else if (document.querySelectorAll(".refreshthepagenowcategories").length > 0){
        window.location.href = "categories.php";
    } else if (document.querySelectorAll(".refreshthepagenowhistory").length > 0){
        window.location.href = "history.php";
    }
    if ( (document.querySelectorAll(".rederror").length > 0) && (document.getElementById("alert") === null) ){
        alert("There's "+document.querySelectorAll(".rederror").length+" product(s) not avaliable at moment, the product line is bold and red.");
    } else if ( (document.querySelectorAll(".rederror").length > 0) && (document.getElementById("alert") !== null) && (document.getElementById("confirmfrompurchase") !== null)){
        window.location.href = "index.php";
    }
    if (document.getElementById("alert") !== null){
        if (document.getElementById("confirmfrompurchase") === null){
            let nobutton = document.getElementById("nobutton");
            setTimeout(function(){ nobutton.innerHTML="NO 9s" }, 1000);
            setTimeout(function(){ nobutton.innerHTML="NO 8s" }, 2000);
            setTimeout(function(){ nobutton.innerHTML="NO 7s" }, 3000);
            setTimeout(function(){ nobutton.innerHTML="NO 6s" }, 4000);
            setTimeout(function(){ nobutton.innerHTML="NO 5s" }, 5000);
            setTimeout(function(){ nobutton.innerHTML="NO 4s" }, 6000);
            setTimeout(function(){ nobutton.innerHTML="NO 3s" }, 7000);
            setTimeout(function(){ nobutton.innerHTML="NO 2s" }, 8000);
            setTimeout(function(){ nobutton.innerHTML="NO 1s" }, 9000);
            setTimeout(function(){
                if (document.getElementById("confirmfromdeletecategory") !== null){
                     window.location.href = "categories.php";
                } else if (document.getElementById("confirmfromdeleteproduct") !== null){
                    window.location.href = "products.php";
                } else if (document.getElementById("confirmfromdeletecart") !== null){
                    window.location.href = "index.php";
                }
            }, 10000);
        }
        $('body').on('click', '#nobutton', function (event) {
            if (document.getElementById("confirmfromdeletecategory") !== null){
                 window.location.href = "categories.php";
            } else if (document.getElementById("confirmfromdeleteproduct") !== null){
                window.location.href = "products.php";
            } else if (document.getElementById("confirmfromdeletecart") !== null){
                window.location.href = "index.php";
            } else if (document.getElementById("confirmfrompurchase") !== null){
                window.location.href = "index.php";
            }
        });
        document.getElementById("cart_total_price").innerHTML = document.getElementById("yesbutton").value;
    }
    $('body').on('click', '.dropdown-content div', function (event) {
        var value = event.target.getAttribute("data-value");
        var name = event.target.innerHTML;
        var content = document.querySelector("body div.dropdown div.dropbtn div.dropdowntext");
        content.className = "dropdowntext dropdownselected"
        content.innerHTML = name;
        content.setAttribute("title",name);
        if (document.getElementById("tablecart") !== null){
            document.getElementById("changeidhidden").value = value;
            document.getElementById("formchange").submit();
        } else if (document.getElementById("tableproduct") !== null){
            document.getElementById("categoryidhidden").value = value;
        }
    });

    if (document.getElementById("tablecategories") !== null){
        $("#formcategories").submit(function(event) {
            if (!(checkvaliditycategories())){
                event.preventDefault();
            }
        });
        $("#formaltercategories").submit(function(event) {
            if (!(checkvaliditycategories("alter"))){
                event.preventDefault();
            }
        });
    }

    if (document.querySelectorAll(".extrabt.delete").length == 0){
        $("#formfinisher").submit(function(event) {
            event.preventDefault();
        });
    }

    if (document.getElementById("tablecart") !== null){
        $("#formcart").submit(function(event) {
            if (!(checkvaliditycart())){
                event.preventDefault();
            }
        });

        $('body').on('click', '#finish', function (event) {
            if (document.querySelectorAll(".extrabt.delete").length >= 1){
                if (document.querySelectorAll(".rederror").length > 0){
                    event.preventDefault();
                    alert("There's "+document.querySelectorAll(".rederror").length+" product(s) not avaliable in your cart, please remove then, before finishing your purchase.");
                }
            } else {
                event.preventDefault();
            }
        });
    }

    if (document.getElementById("tableproduct") !== null){
        $("#formproduct").submit(function(event) {
            if (!(checkvalidityproduct())){
                event.preventDefault();
            }
        });
        $("#formalterproduct").submit(function(event) {
            if (!(checkvalidityproduct("alter"))){
                event.preventDefault();
            }
        });
    }

    $('body').on('click', 'div.main div.textdropleft', function (event) {
        var myTopnav = document.getElementById("myTopnav");
        myTopnav.className = "topnav";

        var iconleft = document.querySelector("body div.main div.textdropleft div.icon");
        var divleft = document.querySelector("body div.main div.left");

        var iconright = document.querySelector("body div.main div.textdropright div.icon");
        var divright = document.querySelector("body div.main div.right");

        if (iconleft.className === "icon") {
            iconleft.className += " hidden";
        } else {
            iconleft.className = "icon";
        }
        iconright.className = "icon";

        if (divleft.className === "left") {
            divleft.className += " show";
        } else {
            divleft.className = "left";
        }
        divright.className = "right";
    });

    $('body').on('click', 'div.main div.textdropright', function (event) {
        var myTopnav = document.getElementById("myTopnav");
        myTopnav.className = "topnav";

        var iconright = document.querySelector("body div.main div.textdropright div.icon");
        var divright = document.querySelector("body div.main div.right");

        var iconleft = document.querySelector("body div.main div.textdropleft div.icon");
        var divleft = document.querySelector("body div.main div.left");

        if (iconright.className === "icon") {
            iconright.className += " hidden";
        } else {
            iconright.className = "icon";
        }
        iconleft.className = "icon";

        if (divright.className === "right") {
            divright.className += " show";
        } else {
            divright.className = "right";
        }
        divleft.className = "left";
    });

    $('body').on('click', 'div#myTopnav a.icon', function (event) {
        var iconleft = document.querySelector("body div.main div.textdropleft div.icon");
        var divleft = document.querySelector("body div.main div.left");
        var iconright = document.querySelector("body div.main div.textdropright div.icon");
        var divright = document.querySelector("body div.main div.right");

        iconright.className = "icon";
        iconleft.className = "icon";
        divright.className = "right";
        divleft.className = "left";

        var myTopnav = document.getElementById("myTopnav");
        if (myTopnav.className === "topnav") {
            myTopnav.className += " responsive";
        } else {
            myTopnav.className = "topnav";
        }
    });
});

function checkvalidityproduct(alter) {
    if (document.getElementById("category").innerHTML == ""){
        alert("Please add a category, before trying to register a product.");
        return(false);
    } else {
        if (typeof(alter) === 'undefined'){
            var cart = document.getElementById('formproduct');
        } else if (alter == "alter") {
            var cart = document.getElementById('formalterproduct');
        }
        if (cart.checkValidity()) {
            if (document.getElementById("categoryidhidden").value == "null"){
                alert("Before adding the product, please select a category.");
                return(false);
            } else {
                if ((isInt(document.getElementById("categoryidhidden").value)) && (isInt(document.getElementById("amount").value))){
                    let rege = new RegExp("^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$");
                    if (rege.test(document.getElementById('productname').value)){
                        let reg = new RegExp("^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$");
                        if (reg.test(document.getElementById('unitprice').value) && (Number(document.getElementById('unitprice').value) >= 0.01 ) && (Number(document.getElementById('amount').value) >= 1 )){
                            if (typeof(alter) === 'undefined'){
                                if ( (document.getElementById('tableproduct').innerHTML).indexOf('"'+document.getElementById('productname').value+'"') == -1 ){
                                    return(true);
                                } else {
                                    alert("There's already a product within this name, please add more information with the name or change the name.");
                                    document.getElementById("productname").focus();
                                    return(false);
                                }
                            } else if (alter == "alter") {
                                if ( document.getElementById('productname').value != document.getElementById('oldname').value){
                                    if ( (document.getElementById('tableproduct').innerHTML).indexOf('"'+document.getElementById('productname').value+'"') == -1 ){
                                        return(true);
                                    } else {
                                        alert("There's already another product within this name, please add more information with the name or change the name or return the name to the same as before.");
                                        document.getElementById("productname").focus();
                                        return(false);
                                    }
                                } else {
                                    return(true);
                                } 
                            }
                        } else {
                            alert("There's some problem with the request, please try again.");
                            window.location.href = "products.php";
                            return(false);
                        }
                    } else {
                        alert("There's some problem with the request, please try again.");
                        window.location.href = "products.php";
                        return(false);
                    }
                } else {
                    alert("There's some problem with the request, please try again.");
                    window.location.href = "products.php";
                    return(false);
                }
            }
        } else {
            return(false);
        }
    }
}

function checkvaliditycategories(alter) {
    if (typeof(alter) === 'undefined'){
        var cart = document.getElementById('formcategories');
    } else if (alter == "alter") {
        var cart = document.getElementById('formaltercategories');
    }
    if (cart.checkValidity()) {
        let rege = new RegExp("^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$");
        if (rege.test(document.getElementById('categoryname').value)){
            let reg = new RegExp("^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$");
            if (reg.test(document.getElementById('tax').value) && (Number(document.getElementById('tax').value) >= 0 )){
                if (typeof(alter) === 'undefined'){
                    if ( (document.getElementById('tablecategories').innerHTML).indexOf('"'+document.getElementById('categoryname').value+'"') == -1 ){
                        return(true);
                    } else {
                        alert("There's already a category within this name, please add more information with the name or change the name.");
                        document.getElementById("categoryname").focus();
                        return(false);
                    }
                } else if (alter == "alter") {
                    if ( document.getElementById('categoryname').value != document.getElementById('oldname').value){
                        if ( (document.getElementById('tablecategories').innerHTML).indexOf('"'+document.getElementById('categoryname').value+'"') == -1 ){
                            return(true);
                        } else {
                            alert("There's already another category within this name, please add more information with the name or change the name or return the name to the same as before.");
                            document.getElementById("categoryname").focus();
                            return(false);
                        }
                    } else {
                        return(true);
                    } 
                }
            } else {
                alert("There's some problem with the request, please try again.");
                window.location.href = "categories.php";
                return(false);
            }
        } else {
            alert("There's some problem with the request, please try again.");
            window.location.href = "categories.php";
            return(false);
        }
    } else {
        return(false);
    }
}

function isInt(value) {
    return !isNaN(value) && parseInt(Number(value)) == value && !isNaN(parseInt(value, 10));
}

function checkvaliditycart() {
    if (document.getElementById("product").innerHTML == ""){
        alert("Please wait for a product to be avaliable.");
        return (false);
    } else if (document.getElementById("productidhidden") == null){
        alert("Please select a product and the amount.");
        return (false);
    } else if (document.getElementById("amount").disabled){
        alert("There's no stock left for the product, please select another product, or wait until the product is avaliable.");
        return (false);
    } else {
        var cart = document.getElementById('formcart');
        productidvalue = document.getElementById("productidhidden").value;
        if (cart.checkValidity()) {
            if ( (isInt(productidvalue)) && (isInt(document.getElementById("amount").value)) ){
                return (true);
            } else {
                alert("There's some problem with the request, please try again.");
                window.location.href = "index.php";
                return (false);
            }
        } else {
            return (false);
        }
    }
}

