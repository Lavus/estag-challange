function FetchUpdate(selectValues, finishFunction){
    fetch('http://localhost/ports/UpdatePort.php', {
        method: 'POST',
        headers: {
            'Content-Type' : 'application/json',
            'JPIZGRPSNRMFNYUWVPZ7RKFWLNMVUCNXSGO3FEQEVAOQUJRHEAONY4FGWEICD9KARVOKHHZYOC3PAQNZNRN6LDSMGNRMDNCAR0PPOPG6CCJ2UVRUBAQ' : 'SlCo/rpvAFCWsfljh2VGhCCrt4CnBCuoZf5gobtIh7KFLH1Z+ZteqDc+ARImfH9M9B1cdlMje7UkqUXjpIKhazGkKyBD3Xebzr1yLsk4O6RGK0CRDMWgz9dmhZ77tNlr2oiwAyXVb8PX4EV+vi/VSD1Vj8SgE6I='
        },
        body: JSON.stringify(selectValues)
    })
    .then((resp) => resp.json())
    .then((data) => {
        finishFunction(data,'update')
    })
}
  
export default FetchUpdate