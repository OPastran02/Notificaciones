String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};


var Index = function () {

  open = function(verb, url, data, target) {
    var form = document.createElement("form");
    form.action = url;
    form.method = verb;
    form.target = target || "_self";
    if (data) {
      for (var key in data) {
        var input = document.createElement("textarea");
        input.name = key;
        input.value = typeof data[key] === "object" ? JSON.stringify(data[key]) : data[key];
        form.appendChild(input);
      }
    }
    form.style.display = 'none';
    document.body.appendChild(form);
    form.submit();
  };
  

  var VistaPrevia = function () {
      var link = routesNotificaciones.myroutes.pedidopdf;
      if (editor.getValue() == "")
      {        
          var data = "No hay cuerpo para mostrar";
      }
      else
      {
          var data = editor.getValue().replaceAll("/","[[[").replaceAll("%0A%0","");          
      }      
      open('POST', link, {cuerpo:data }, '_blank');
  }

  var setCuerpo = function(){
      var cp = $('#cuerpo').val();
      editor.setValue(cp, true);
  }


  return {

      VistaPrevia: function(){
          VistaPrevia();
      },

      setCuerpo: function(){
          setCuerpo();
      },

  };

}();

UTF8 = {
  encode: function(s){
    for(var c, i = -1, l = (s = s.split("")).length, o = String.fromCharCode; ++i < l;
      s[i] = (c = s[i].charCodeAt(0)) >= 127 ? o(0xc0 | (c >>> 6)) + o(0x80 | (c & 0x3f)) : s[i]
    );
    return s.join("");
  },
  decode: function(s){
    for(var a, b, i = -1, l = (s = s.split("")).length, o = String.fromCharCode, c = "charCodeAt"; ++i < l;
      ((a = s[i][c](0)) & 0x80) &&
      (s[i] = (a & 0xfc) == 0xc0 && ((b = s[i + 1][c](0)) & 0xc0) == 0x80 ?
      o(((a & 0x03) << 6) + (b & 0x3f)) : o(128), s[++i] = "")
    );
    return s.join("");
  }
};