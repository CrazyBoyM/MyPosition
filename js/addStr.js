function addStr(s){
    myField = document.getElementById('text');
    if (document.selection) {
        myField.focus();
        sel = document.selection.createRange();
        sel.text = s;
        myField.focus();
    }
    else if (myField.selectionStart || myField.selectionStart == '0') {
        var startPos = myField.selectionStart;
        var endPos = myField.selectionEnd;
        var cursorPos = startPos;
        myField.value = myField.value.substring(0, startPos)
        + s
        + myField.value.substring(endPos, myField.value.length);
        cursorPos += s.length;
        myField.focus();
        myField.selectionStart = cursorPos;
        myField.selectionEnd = cursorPos;
    }
    else{
        myField.value += s;
        myField.focus();
    }
}