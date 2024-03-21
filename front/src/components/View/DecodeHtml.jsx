function DecodeHtml (text) {
    var txta = document.createElement("textarea")
    txta.innerHTML = text
    return (txta.value)
}

export default DecodeHtml