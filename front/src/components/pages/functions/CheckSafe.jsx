function CheckSafe(table){
    let check = true
    Object.keys(table).map((keyvalue, indexkey) => (
        ((table[keyvalue]['code'][1] == "Broken")&&(
            check = false
        ))
    ))
    return(check)
}
  
export default CheckSafe