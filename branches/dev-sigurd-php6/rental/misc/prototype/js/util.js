cmp = function(a, b) {
    if(a < b)
        return -1;
    else if (a == b)
        return 0;
    else
        return 1;
}

unique = function(array) {
    var tmp = [];
    for(var i=0; i< array.length; i++) {
        if(tmp.indexOf(array[i]) == -1) {
            tmp.push(array[i]);
        }
    }
    return tmp;
}
