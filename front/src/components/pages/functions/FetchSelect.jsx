function FetchSelect(setTable,setRemoveLoading,selectValues){
    fetch('http://localhost/ports/SelectPort.php', {
        method: 'POST',
        headers: {
            'Content-Type' : 'application/json',
            'I2S2ZUZHGSBPSSKJMYN1DOO8T678WI6ZBKPE4OWTWN7VJPQGJZFBLS5H3WY950O9K6NT' : 'OekKPZNxf0YW0HHZULncSinkaM1cjEif6bbp7ETHRu2TtxCRFSlND6rSHkpb4I1bWPm4CS3wDAk='
        },
        body: JSON.stringify(selectValues)
    })
    .then((resp) => resp.json())
    .then((data) => {
        setTable(data)
        setRemoveLoading(true)
    })

}
  
export default FetchSelect