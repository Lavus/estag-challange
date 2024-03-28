function FetchSelect(selectValues, finishFunction){
    fetch('http://localhost/ports/SelectPort.php', {
        method: 'POST',
        headers: {
            'Content-Type' : 'application/json',
            'VZFUYQREU2LGC3GNSGG6OMHPIZDTCMRBO4U6K6TL34OFPETOJUHICKGI2VC0IFESXISM3CO2U4JQIWFIHLGWH1H2PQYOZYY47VXPS31GRJETRKJJRXIT4WA' : '7YsnXD6n7DYWfqhrh0laPlOQ9KDNUJewxyvURCrI5mL1foDtPWsjRTxdBKgf3wT5QaJYo8D8hpqftMbcTtPdDQpiUwDDZg0O5E0GulikcL7ncvzfYYYlutIkqaNHTOsAvyTYsHHuuUN4Fl2qHEkoC5D1qY+OMWE='
        },
        body: JSON.stringify(selectValues)
    })
    .then((resp) => resp.json())
    .then((data) => {
        finishFunction(data)
    })
}
  
export default FetchSelect