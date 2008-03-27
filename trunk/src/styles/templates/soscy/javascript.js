function OpenWindow (Adresse) {
  MeinFenster = window.open(Adresse, "Zweitfenster", "width=300,height=200,scrollbars=no");
  MeinFenster.focus();
}

function emoticon(text) {
    var txtarea = document.guestbook.textarea;
   text = ' ' + text + ' ';
   if (txtarea.createTextRange && txtarea.caretPos) {
      var caretPos = txtarea.caretPos;
      caretPos.text = caretPos.text.charAt(caretPos.text.length - 1) == ' ' ? caretPos.text + text + ' ' : caretPos.text + text;
      txtarea.focus();
   } else {
      txtarea.value  += text;
      txtarea.focus();
   }
}

function checkForm() {

	formErrors = false;

	if (document.guestbook.text.value.length < 2) {
		formErrors = "Du musst zu deinem Beitrag einen Text eingeben.";
	}

	if (formErrors) {
		alert(formErrors);
		return false;
	}
}

function insert(aTag, eTag) {
  var input = document.forms['guestbook'].elements['textarea'];
  input.focus();
  /* für Internet Explorer */
  if(typeof document.selection != 'undefined') {
    /* Einfügen des Formatierungscodes */
    var range = document.selection.createRange();
    var insText = range.text;
    range.text = aTag + insText + eTag;
    /* Anpassen der Cursorposition */
    range = document.selection.createRange();
    if (insText.length == 0) {
      range.move('character', -eTag.length);
    } else {
      range.moveStart('character', aTag.length + insText.length + eTag.length);      
    }
    range.select();
  }
  /* für neuere auf Gecko basierende Browser */
  else if(typeof input.selectionStart != 'undefined')
  {
    /* Einfügen des Formatierungscodes */
    var start = input.selectionStart;
    var end = input.selectionEnd;
    var insText = input.value.substring(start, end);
    input.value = input.value.substr(0, start) + aTag + insText + eTag + input.value.substr(end);
    /* Anpassen der Cursorposition */
    var pos;
    if (insText.length == 0) {
      pos = start + aTag.length;
    } else {
      pos = start + aTag.length + insText.length + eTag.length;
    }
    input.selectionStart = pos;
    input.selectionEnd = pos;
  }
  /* für die übrigen Browser */
  else
  {
    /* Abfrage der Einfügeposition */
    var pos;
    var re = new RegExp('^[0-9]{0,3}$');
    while(!re.test(pos)) {
      pos = prompt("Einfügen an Position (0.." + input.value.length + "):", "0");
    }
    if(pos > input.value.length) {
      pos = input.value.length;
    }
    /* Einfügen des Formatierungscodes */
    var insText = prompt("Bitte geben Sie den zu formatierenden Text ein:");
    input.value = input.value.substr(0, pos) + aTag + insText + eTag + input.value.substr(pos);
  }
}