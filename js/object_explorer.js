/* Javascript Object Tree Screener

Copyright (c) 2007 Denis Petrov www.denispetrov.com

$Id: oe.js,v 1.8 2007/08/31 00:32:12 cvs Exp $

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/
var js_debug = {

tool_title: 'Javascript Object Tree Screener',

Version: function()
{
    return String('$Revision: 1.8 $').match(/\$Revision: (\S+)/)[1];
},

DumpObject: function(objname)
{
  var rv = '';
  var real_objname = objname;

  var obj = undefined;
  try { obj = eval(real_objname); }catch(err){}

  var objstring;

  try { objstring = String(obj); }catch(err){}

  if ( objstring.match(/JavaPackage/) )
  {
      rv += '<b>' + obj + ' : </b> <br>\n';
      return rv;
  }

  var attlist = Array();
  for ( var i in obj )
  {
    attlist.push(i);
  }

  attlist.sort();

  for ( var ii = 0; ii < attlist.length; ii++ )
  {
    var i = attlist[ii];
    var objtype = '*';
    try {
      objtype = typeof obj[i];
    } catch(err) {

    }
    rv += '[' + objtype + '] ';

    if ( objtype == 'object' )
    {
        var nodename = '';
        var innertext = '';
        var textcontent = '';
        var lengthvalue;
        // do not try to query packages that tend to bomb in various browsers
        if ( i != 'Packages' && i != 'java' && i != 'sun' && i != 'netscape' )
        {
          try {nodename = obj[i].nodeName} catch(err){}
          try {innertext = obj[i].innerText} catch(err){}
          try {textcontent = obj[i].textContent} catch(err){}
          try {lengthvalue = obj[i].length;} catch(err){}
          nodename = nodename || '';
          innertext = innertext || textcontent || '';
          if ( innertext.length > 100 )
          {
            innertext = innertext.slice(0,100)+'...';
          }
        }

        var newobjname = parseInt(i) == i ? (objname + '[' + i + ']') : (objname + '.' + i);

        rv += '<b><a href="" title="'+newobjname+'" onclick="js_debug.ShowDump(\''
              + newobjname.replace(/(\'|\")/g,"\\$1") + '\');return false">'
              + i + '</a></b> ' + nodename + (innertext?' "' + innertext + '"':'')
              + (lengthvalue != undefined && typeof lengthvalue == 'number'?' (' + i + '.length=' + lengthvalue + ')':'')
              + '<br>\n';
    }
    else if ( objtype == 'function' )
    {
      rv += '<b>' + i + ' : </b> (function)<br>\n';
    }
    else if ( objtype != '*' && objtype != 'unknown' )
    {
      var newobjname = parseInt(i) == i ? (objname + '[' + i + ']') : (objname + '.' + i);
      var val = String(obj[i]);
      val = val.replace(/</g,'&lt;');
      val = val.replace(/>/g,'&gt;');
      if ( val.length > 100 )
      {
          val = val.slice(0,100)+'...';
      }

      rv += '<a href="" title="' + newobjname
          + '" onclick="js_debug.ShowDump(\'' + newobjname.replace(/(\'|\")/g,"\\$1") + '\');return false">'
          + i + '</a> : ' + val + '<br>\n';
    }
    else
    {
      rv += '<b>' + i + ' : </b> (unknown object)<br>\n';
    }
  }
  return rv;
},


getDiv1: function()
{
    return document.getElementById('js_debug_div1');
},



ShowDump: function(objname)
{
    var real_objname = objname;

    var val = undefined;
    try {
        val = eval(real_objname);
    } catch ( err )
    {
    }

  var objtype = typeof(val);

  if ( objtype == 'undefined')
  {
      if ( val == undefined )
      {
          this.getDiv1().innerHTML = 'Object ' + objname + ' is not defined' ;
      }
      else
      {
          this.getDiv1().innerHTML = objname + ' = ' + val.replace(/</g,'&lt;').replace(/>/g,'&gt;');
      }
  }
  // OR part is a hack for the moronic IE as it reports unlisted functions as objects
  // it is twice moronic because it puts a space in front of word 'function' in the value
  else if ( objtype == 'function' || objtype == 'object' && String(val).match(/^\s*function/) )
  {
    this.getDiv1().innerHTML = objtype + ' ' + objname + ' = ' + val;
  }
  else if ( objtype == 'boolean' )
  {
    this.getDiv1().innerHTML = objtype + ' ' + objname + ' = ' + val;
  }
  else if ( objtype == 'string' )
  {
    this.getDiv1().innerHTML
     = objtype + ' ' + objname + ' = ' + val.replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>\n');
  }
  else if ( objtype == 'number' )
  {
    this.getDiv1().innerHTML = objtype + ' ' + objname + ' = ' + val;
  }
  else if ( objtype == 'object' )
  {
    this.getDiv1().innerHTML = this.DumpObject(objname);
  }

  var nmstr = String(objname);
  var nma = nmstr.split('.');
  var objnamea = '';
  var rv = '';
  for ( var i = 0; i < nma.length; i++ )
  {
      objnamea += nma[i];
      if ( i == nma.length-1 )
      {
          rv += nma[i];
      }
      else
      {
          rv += '<a href="" onclick="js_debug.ShowDump(\'' + objnamea.replace(/(\'|\")/g,"\\$1") + '\');return false">'
              + nma[i] + '</a>';
          objnamea += '.';
          rv += '.';
      }
  }
  document.getElementById('js_debug_head').innerHTML = rv;
  document.getElementById('js_debug_nameentry').value = objname;
  document.title = this.tool_title + ' V' + this.Version() + ' - ' + objname;
},


/* Adds necessary elements to the page */
ExtendPage: function()
{
    var csshref;
// obtain stylesheet url from script url if the script is added to the guest page
// this makes it independent of the host name
    var thescript = document.getElementById('js_debug_id');
    if ( thescript )
        csshref = thescript.src.replace(/\.js$/,".css");
    else   // or from the page url if the page is our own and the script is not loaded
        csshref = document.URL.replace(/\.php(\?.*)?/,".css");

    var estyle = document.createElement('link');
    estyle.rel = "stylesheet";
    estyle.href = csshref;
    estyle.type = "text/css";
    estyle.media = "screen";
    var heads = document.getElementsByTagName('head');
    if ( heads.length == 0 )
    {
        document.documentElement.insertBefore(document.newElement('HEAD'),document.body).appendChild(estyle);
    }
    else
        heads[0].appendChild(estyle);


    if ( ! document.getElementById('js_debug_objexplorer') )
    {
        var div = document.createElement('div');
        div.id = 'js_debug_objexplorer';
        div.style.color = 'black';
        div.innerHTML =
         '<form onsubmit="js_debug.ShowDump(document.getElementById(\'js_debug_nameentry\').value); return false;">'
        +'<div id="js_debug_div0">Currently Viewing: <span id="js_debug_head"></span></div>'
        +'<div id="js_debug_div2">'
        +'Name of Object to View: <input id="js_debug_nameentry" type="text" size="80"> '
        +'<input type="submit" style="background-color: silver" value="Go"'
        +' onclick="js_debug.ShowDump(document.getElementById(\'js_debug_nameentry\').value)">'
        +'<br>Standard objects: <a href="javascript:js_debug.ShowDump(\'window\')">window</a>'
        +' <a href="javascript:js_debug.ShowDump(\'document\')">document</a>'
        +'</div>'
        +'<div id="js_debug_div1"></div>'
        +'<div id="js_debug_divattrib">' + this.tool_title + ' Version ' + this.Version() + '</div>';
        +'</form>'
        document.body.appendChild(div);
    }
},

SelfDump: function()
{
    this.ExtendPage();
    this.ShowDump('window');
    var version_view = document.getElementById('js_debug_version');
    if ( version_view ) version_view.innerHTML = this.Version();
    /* this function returns before the document in js_debug_tdoc iframe is loaded */
},

OnLoad: function(e)
{
    // 'this' does not seem to work when called as an event handler
    js_debug.SelfDump();
}

};

if ( document.body )
{
    // appending to an existing page through favelet
    js_debug.ExtendPage();
    js_debug.ShowDump('window');
}
else
{
    // loading with the page in the head section
    window.onload = js_debug.OnLoad;
}
