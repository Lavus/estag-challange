function FetchDelete(selectValues, finishFunction){
    fetch('http://localhost/ports/DeletePort.php', {
        method: 'POST',
        headers: {
            'Content-Type' : 'application/json',
            'DGKQPLCQX1IZAO0D7VD9SJYROFSBGSEOQXXHYIXQCQFT2XODQPE8FDRHHJDWY3L5WNBAU6JLA7U44HPXKJDOJ2JBQZCCEK7Y37CC0PILUUMHVTVDYZI5W' : 'eh63/uT/+iQqmpgn3lQWB8ehzIk6Pol+dBqmQhBubW+S1KkNsossNfJIqE+VIHR0w9qHsvgsuthTYR1LW0MsyhGoXUXCWhhE404j9B6yISSHgBRGWBpaY+vGgeEQaRkNiaZwRgJKFc94AX6CBAblSssXviFuO2k='
        },
        body: JSON.stringify(selectValues)
    })
    .then((resp) => resp.json())
    .then((data) => {
        finishFunction(data,'delete')
    })
}
  
export default FetchDelete