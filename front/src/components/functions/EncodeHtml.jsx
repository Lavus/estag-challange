function EncodeHtml(text){
    let newText = ""
    let stringText = String(text)
    var regex = new RegExp("^.{1,}$")
    if (regex.test(stringText)){
        for (let index = 0; index < stringText.length; index++) {
            newText += "&#"+stringText.charCodeAt(index)+";"
        }
    }
    return(newText)
}

export default EncodeHtml