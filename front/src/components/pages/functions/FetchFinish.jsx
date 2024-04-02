function FetchFinish(selectValues, finishFunction){
    fetch('http://localhost/ports/FinishPort.php', {
        method: 'POST',
        headers: {
            'Content-Type' : 'application/json',
            'U6LFWUJVWRYZUBEMSZGTTCNZW6CSHKXN2M6S9SPC8GMFE3F4QYZGVRKQ9HVVFAELXP8NH2OYJI7WAZ6EE1PHZXTKFDEACSU2BYPZEEJLD2NGFLK2JQH0OJKJ50GAUBBF0T1ASNOQIDH64MLDXBQLHW57ORHKV7GBC2FDVLVINYKOXRY' : 'XGp0h9o3wh+tsdH5k8pI0qC0lCECxE9vkSlMclBf6JaEFypK1jd4TbdodmSKQmS6h7nP2WLG8QNIPd1Ul3sFSSzefWmogWmXy4revR/lnzdACQ1wByBmbco1tZ6vWonxazTgF5M+Xcw3smT4nsEwUX7Ddzeqj2dcgONIUZHtn46fj3M61CaRRHneznDuq+GMZ5Qk6Xv6Nu2qbKFABca8WydA2Wi+'
        },
        body: JSON.stringify(selectValues)
    })
    .then((resp) => resp.json())
    .then((data) => {
        finishFunction(data)
    })
}
  
export default FetchFinish