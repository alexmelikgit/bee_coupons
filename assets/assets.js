class coupon{
    constructor(){
        this.form = document.querySelector(".beeform");
        this.type = document.querySelector("#couponsType");
        this.needData = ["coupon_code", "discount_type", "discount", "discount_count"]
        if(this.form){
            this.init();  
        }  
        if(this.type){
            new DataTable("#couponsTable")

            this.typeChange()
        }
    }
    init(){
        this.formEvents();
    }

    formEvents(){
        this.form.addEventListener("submit", (e)=>{
            e.preventDefault();
            this.clearErrors();
            let data = {};
            for(let i =0 ; i<this.form.length; i++){
                if(this.form[i].tagName != "BUTTON"){
                    data[this.form[i].name] = this.form[i].value;
                    if(this.form[i].name == "phone"){
                        let phoneInput = this.form[i];
                            phoneInput.addEventListener("input",(e)=>{
                            phoneInput.value = phoneInput.value.replace(/(?!^\+|[0-9])(.*)/, "");
                        })
                    }
                }
            
            }
            try{
                this.dateValidate(data);
                this.createcoupon(data);

            }catch(e){
                if(e instanceof couponError){
                    e.setErrors();
                }
            }
        })
    }
    typeChange(){
        let buttons = this.type.querySelectorAll("button");
        let input = this.type.querySelector("input[name=type]");
        this.type.addEventListener("submit", (e)=>{
            e.preventDefault();
        })
        buttons.forEach(button=>{
            button.addEventListener("click",(e)=>{
                e.preventDefault();
                input.value = button.value;
                this.type.submit();
            })
        })
    }
    clearErrors(){
        let errors = this.form.querySelectorAll(".err-msg");
        errors.forEach(error=>{
            error.innerHTML = "";
        })

    }
    /**
     * 
     * @param {Object} data 
     */
    dateValidate(data){
        let emptyData = []
        let validateError = [];
        this.needData.forEach(key=>{
            if(data[key].trim() == ""){
                emptyData.push(key);
            }
        })    
        if(data["email"].trim().length){
            let emailReg = new RegExp(/[a-z0-9]+@[a-z]+\.[a-z]{2,3}/);
            if(!emailReg.test(data['email'].trim())){
                validateError.push("email");
            }
        }
        if(data["phone"].trim().length){
            let phoneReg = new RegExp(/^((0|\+?374)(47|97|91|99|96|43|33|79|55|95|41|44|66|50|93|94|77|98)[0-9]{6}$|\+(?!374)[0-9].)/);
            if(!phoneReg.test(data["phone"].trim())){
                validateError.push("phone");
            }
        }
        if(emptyData.length){
            throw new couponError(emptyData, "empty");
        }else if(validateError.length){
            if(validateError.length)throw new couponError(validateError, "validate")
        }

    }
    createcoupon(data){
        const xml = new XMLHttpRequest();
        let action = this.form.getAttribute("id") == "couponCreate" ? "create" : "update" ; 
        console.dir(this.form);
        xml.open("POST", ajaxurl + "?action=beecoupon_create&type="+action);
        xml.send(JSON.stringify(data));
        xml.onreadystatechange = ()=>{
            if(xml.readyState === 4){
                if(xml.status === 200){
                    if(xml.responseText.length && Number.isNaN(parseInt(xml.responseText))){
                        try{
                            let errorData = JSON.parse(xml.responseText);
                            console.log(errorData);
                            throw new couponError(errorData['key'], errorData['message']);
                        }catch(e){
                            if(e instanceof couponError){
                                e.setErrors();
                            }
                        }
                    }else{
                        window.location.href += "&edit=" + parseInt(xml.responseText)
                    }
                }
            }
        }

    }
    
}

class couponError extends Error{
    constructor(key, message){
        super(message);
        this.key = key;
        this.message = message;
        this.errorMessages = {
            "empty" : "this field could't to be empty",
            "validate" : "this field is not valid",
            "exists" : "This coupon is already exists"
        }
    }
    setErrors(){
        if(typeof this.key === "string"){
                let place = document.querySelector("input[name="+this.key+"]");
                place = place.parentElement.querySelector(".err-msg");
                if(place){
                    console.log(this.message)
                    place.innerHTML = this.errorMessages[this.message];
                }else{
                    alert(this.message);
                }
        }else if(Array.isArray(this.key)){
            this.key.forEach(key=>{
                    let place = document.querySelector("input[name="+key+"]");
                    place = place.parentElement.querySelector(".err-msg");
                    place.innerHTML = this.errorMessages[this.message];
            })
        }
    }

}
function couponsEditPage(){
    let allcoupons = document.querySelector(".coupons-table");
    if(allcoupons){
        let form = document.createElement("form");
        let page = document.createElement("input");
        let edit = document.createElement("input");
        form.style.display = "none";
        form.appendChild(page);
        form.appendChild(edit);
        form.action = "";
        form.method = "GET";
        edit.name = "edit";
        page.name = "page"

        let rows = allcoupons.querySelectorAll(".coupon-row");
        rows.forEach(row => {
            row.addEventListener("click", ()=>{

                edit.value = row.id;
                page.value = "new_coupon",
                document.querySelector("body").appendChild(form)

                form.submit()
            })
        });
    }
}
couponsEditPage();
new coupon();