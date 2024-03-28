import DecodeHtml from "../../functions/DecodeHtml";

function ValidateCamps(type, data, camps, table = []){
    let validated = 'true'
    let message = ''


    function CheckName(name,excludeName){
        let returnCheck = true
        Object.keys(table).map(keyValue => { 
            if ( ( table[keyValue]['name'] != excludeName ) && ( DecodeHtml(table[keyValue]['name']) == name ) ){
                returnCheck = false
            }
        })
        return ( returnCheck )
    }
    
    function CheckCamps(){
        let regexName = new RegExp("^[A-Z]+[a-zA-ZÀ-ú]{2}.{0,222}$")
        let regexAmount = new RegExp("^[0-9]{1,}$")
        let regexTax = new RegExp("^[0-9]{1,4}([.]+[0-9]{1,2}){0,1}$")
        let regexPrice = new RegExp("^[0-9]{1,10}([.]+[0-9]{1,2}){0,1}$")
        let countIgual = 0
        for(let index = 0;index < camps.length; index++){
            if (camps[index] == 'name'){
                if (regexName.test(data[camps[index]])){
                    if (type == 'Alter'){
                        if (!(CheckName(data[camps[index]],table[data['id']]['name']))){
                            return("There's already another category within this name, please add more information with the name or change the name or return the name to the same as before.")
                        } else if (data[camps[index]] == DecodeHtml(table[data['id']][camps[index]]) ){
                            countIgual++
                        }
                    } else if (type == 'Insert'){
                        if (!(CheckName(data[camps[index]],''))){
                            return("There's already a category within this name, please add more information with the name or change the name.")
                        }
                    }
                } else {
                    return('false')
                }
            } else if (camps[index] == 'amount'){
                if (regexAmount.test(data[camps[index]])){
                    if (type == 'Alter'){
                        if (data[camps[index]] == DecodeHtml(table[data['id']][camps[index]]) ){
                            countIgual++
                        }
                    } else if (type == 'Insert'){
                        if (data[camps[index]] < 1){
                            return('false')
                        }
                    }
                } else {
                    return('false')
                }
            } else if (camps[index] == 'tax'){
                if (regexTax.test(data[camps[index]])){
                    if (type == 'Alter'){
                        if (data[camps[index]] == (DecodeHtml(table[data['id']][camps[index]])).slice(0, -1) ){
                            countIgual++
                        }
                    }
                } else {
                    return('false')
                }
            } else if (camps[index] == 'price'){
                if (regexPrice.test(data[camps[index]])){
                    if (type == 'Alter'){
                        if (data[camps[index]] == (DecodeHtml(table[data['id']][camps[index]])).slice(0, 1) ){
                            countIgual++
                        }
                    }
                } else {
                    return('false')
                }
            } else {
                return('false')
            }
        }
        if (countIgual == camps.length){
            return('Nothing was changed.')
        }
        return ('true')
    }

    if (type == 'Alter'){
        if (table.hasOwnProperty(data['id'])) {
            let checkAlter = CheckCamps()
            if (checkAlter == 'true'){
                message = 'OK'
            } else if (checkAlter == 'false'){
                validated = 'false'
                message = "There's some problem with the request, please try again."
            } else {
                validated = 'check'
                message = checkAlter
            }
        } else {
            validated = 'false'
            message = "There's some problem with the request, please try again."
        }
    } else if (type == 'Insert'){
        let checkInsert = CheckCamps()
        if (checkInsert == 'true'){
            message = 'OK'
        } else if (checkInsert == 'false'){
            validated = 'false'
            message = "There's some problem with the request, please try again."
        } else {
            validated = 'check'
            message = checkInsert
        }
    }
    return([validated,message])
}

export default ValidateCamps