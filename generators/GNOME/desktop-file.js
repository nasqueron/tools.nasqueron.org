/*  -------------------------------------------------------------
    GNOME .desktop file generator
    - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
    Author:         Dereckson
    Dependencies:   jQuery
    Filename:       desktop-file.js
    Version:        1.0
    Created:        2014-07-27
    Licence:        BSD
    -------------------------------------------------------------    */

var File = {
    template: [
        '[Desktop Entry]',
        'Type=%%type%%',
        'Encoding=%%encoding%%',
        'Name=%%name%%',
        'Comment=%%comment%%',
        'Exec=%%exec%%',
        'Icon=%%icon%%',
        'Terminal=%%terminal%%'
    ].join('\n'),
    Generate: function () {
        var re = /%%([a-z]*)%%/mg;
        var content = this.template.replace(re, function (expr, variable) {
            var div = document.forms['data'][variable];
            if (div == null) {
                return "[Error: can't find " + variable + " value.]";
            }
            if (div instanceof NodeList) {
                return $('input[name="' + variable + '"]:checked').val();
            }
            return div.value;
        });
        document.getElementById('file').innerHTML = content;
    }
};

var CheatSheet = {
    currentElement: '',
    Print: function (element) {
        //Displays the right element in the cheatsheet area.
        if (this.currentElement == element) {
            return;
        }
        if (this.currentElement != '') {
            document.getElementById(this.currentElement).style.display = 'none';
        }
        this.currentElement = element;
        document.getElementById(element).style.display = 'block';
    }
};

File.Generate();
