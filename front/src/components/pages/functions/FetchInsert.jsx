function FetchInsert(selectValues, finishFunction){
    fetch('http://localhost/ports/InsertPort.php', {
        method: 'POST',
        headers: {
            'Content-Type' : 'application/json',
            'FJUYJDJMHYG1WAKXKANHDHA8WU9FCDS8M6YG2ZNLJHWXFSQSEHFCTVOIXTQ78B5JSECDPWF8XMTSHIZYV4IYONXBWFIUIE2ZUAJRQQ7RDLGJM3H7C8CA44' : 'Falw1qKPKZYufBz0r2S1avMZ16BeNHPn3/nqJzg2IyDHF+XtM4x9cBMTOvG++LTO3wCbTEJXEocIO+xfjPCEunNGKu8DvjQzXG29DSSiuQsPnwVV+/cHwnNh6MFLg3KvNC4k3v9uhXZkRMBaRIglt2FnKt3gLssn'
        },
        body: JSON.stringify(selectValues)
    })
    .then((resp) => resp.json())
    .then((data) => {
        finishFunction(data,'insert')
    })
}
  
export default FetchInsert