// JavaScript Document: xmlExporter.js
// Script Name: XML Exporter
// Description: Script per esportare in XML o JSON dei dati da un file Anac XML
// Author: Enzo Costantini (SoftCos)
// Author URI: www.softcos.eu 
//
function xmlExport(Sender, b64XmlStr, type, filterRows) {
  if (!Sender || (b64XmlStr == '')) {
    return false;
  }
  if (!filterRows) {
    filterRows = [];
  }
  type = type.trim();
  var Exts = 'json xml';
  if (Exts.indexOf(type) < 0) {
    type = 'json';
  }

  var XmlStr = Base64.decode(b64XmlStr);
  if (XmlStr == '') {
    return false;
  }

  var xmlDoc = createXmlDoc(XmlStr);
  if (!xmlDoc) {
    return false;
  }

  var iNodeList = xmlDoc.getElementsByTagName("lotto");
    
  var annoRiferimento = xmlDoc.documentElement.getElementsByTagName('annoRiferimento')[0].childNodes[0].nodeValue; 
  var urlFile = xmlDoc.documentElement.getElementsByTagName('urlFile')[0].childNodes[0].nodeValue;

  var xmlDocDest = createXmlDoc('<DatiLegge190/>');
  if (xmlDocDest) {
    appendTextNode(xmlDocDest, xmlDocDest.documentElement, "annoRiferimento", annoRiferimento);
    appendTextNode(xmlDocDest, xmlDocDest.documentElement, "urlFile", urlFile);
    appendTextNode(xmlDocDest, xmlDocDest.documentElement, "dataEsportazione", new Date().toJSON());
    appendTextNode(xmlDocDest, xmlDocDest.documentElement, "numeroLotti", filterRows.length);
    var iRoot = appendTextNode(xmlDocDest, xmlDocDest.documentElement, "lotti");

    for (var i = 0; i < filterRows.length; i++) {
      iNode = iNodeList[filterRows[i]].cloneNode(true);
      iRoot.appendChild(iNode);
    }
    
    if (type == 'xml') {
      var xml_data = new XMLSerializer().serializeToString(xmlDocDest);
      xml_data = '<?xml version="1.0" encoding="utf-8" standalone="yes"?>' + xml_data;
      xml_data = formatXML(xml_data);
      var base64data = "base64," + Base64.encode(xml_data);
      Sender.href = 'data:application/xml;' + base64data;
      return true;
    } else if (type == 'json') {
      json_data = formatJSON(xml2json(xmlDocDest, ''));
      var base64data = "base64," + Base64.encode(json_data);
      Sender.href = 'data:application/json;' + base64data;
      return true;
    }  
  } 
  return false;
}

function createXmlDoc(_XmlStr){
  var xmlDoc;
  if (window.DOMParser)
  {
    parser = new window.DOMParser();
    xmlDoc = parser.parseFromString(_XmlStr,"application/xml");
    return xmlDoc;
  }
  else 
  {
    xmlDoc = new window.ActiveXObject("Microsoft.XMLDOM");
    xmlDoc.async = false;
    xmlDoc.loadXML(_XmlStr);
    return xmlDoc;
  }
  return null;
}

function appendTextNode(iDoc, iParent, nodeName, nodeText){
  var iNode = iDoc.createElement(nodeName);
  if(nodeText){
    var iText = iDoc.createTextNode(nodeText);
    iNode.appendChild(iText);
  }
  iParent.appendChild(iNode);
  return iNode;
}

function formatXML(xmlStr, r) {
    var r = r ? r : '  ';
    var formatted = '';
    var reg = /(>)(<)(\/*)/g;
    xmlStr = xmlStr.replace(reg, '$1\r\n$2$3');
    var pad = 0;
    var xmlArray = xmlStr.split('\r\n'); 
    for(index = 0; index < xmlArray.length; index++){
      var node = xmlArray[index];
      var indent = 0;
      if (node.match( /.+<\/\w[^>]*>$/ )) {
            indent = 0;
        } else if (node.match( /^<\/\w/ )) {
            if (pad != 0) {
                pad -= 1;
            }
        } else if (node.match( /^<\w[^>]*[^\/]>.*$/ )) {
            indent = 1;
        } else {
            indent = 0;
        }
        var padding = '';
        for (var i = 0; i < pad; i++) {
            padding += r;
        }
        formatted += padding + node + '\r\n';
        pad += indent;    
    }
  return formatted;
}

function formatJSON(text, step) {
  var step = step ? step : '  ';
  if (typeof JSON === 'undefined') return text;
  if (typeof text === "string") return JSON.stringify(JSON.parse(text), null, step);
  if (typeof text === "object") return JSON.stringify(text, null, step);
  return text;
}

function xml2json(e,n){var t={toObj:function(e){var n={};if(1==e.nodeType){if(e.attributes.length)for(var i=0;i<e.attributes.length;i++)n["@"+e.attributes[i].nodeName]=(e.attributes[i].nodeValue||"").toString();if(e.firstChild){for(var r=0,o=0,a=!1,l=e.firstChild;l;l=l.nextSibling)1==l.nodeType?a=!0:3==l.nodeType&&l.nodeValue.match(/[^ \f\n\r\t\v]/)?r++:4==l.nodeType&&o++;if(a)if(2>r&&2>o){t.removeWhite(e);for(var l=e.firstChild;l;l=l.nextSibling)3==l.nodeType?n["#text"]=t.escape(l.nodeValue):4==l.nodeType?n["#cdata"]=t.escape(l.nodeValue):n[l.nodeName]?n[l.nodeName]instanceof Array?n[l.nodeName][n[l.nodeName].length]=t.toObj(l):n[l.nodeName]=[n[l.nodeName],t.toObj(l)]:n[l.nodeName]=t.toObj(l)}else e.attributes.length?n["#text"]=t.escape(t.innerXml(e)):n=t.escape(t.innerXml(e));else if(r)e.attributes.length?n["#text"]=t.escape(t.innerXml(e)):n=t.escape(t.innerXml(e));else if(o)if(o>1)n=t.escape(t.innerXml(e));else for(var l=e.firstChild;l;l=l.nextSibling)n["#cdata"]=t.escape(l.nodeValue)}e.attributes.length||e.firstChild||(n=null)}else 9==e.nodeType?n=t.toObj(e.documentElement):alert("unhandled node type: "+e.nodeType);return n},toJson:function(e,n,i){var r=n?'"'+n+'"':"";if(e instanceof Array){for(var o=0,a=e.length;a>o;o++)e[o]=t.toJson(e[o],"",i+"	");r+=(n?":[":"[")+(e.length>1?"\n"+i+"	"+e.join(",\n"+i+"	")+"\n"+i:e.join(""))+"]"}else if(null==e)r+=(n&&":")+"null";else if("object"==typeof e){var l=[];for(var d in e)l[l.length]=t.toJson(e[d],d,i+"	");r+=(n?":{":"{")+(l.length>1?"\n"+i+"	"+l.join(",\n"+i+"	")+"\n"+i:l.join(""))+"}"}else r+="string"==typeof e?(n&&":")+'"'+e.toString()+'"':(n&&":")+e.toString();return r},innerXml:function(e){var n="";if("innerHTML"in e)n=e.innerHTML;else for(var t=function(e){var n="";if(1==e.nodeType){n+="<"+e.nodeName;for(var i=0;i<e.attributes.length;i++)n+=" "+e.attributes[i].nodeName+'="'+(e.attributes[i].nodeValue||"").toString()+'"';if(e.firstChild){n+=">";for(var r=e.firstChild;r;r=r.nextSibling)n+=t(r);n+="</"+e.nodeName+">"}else n+="/>"}else 3==e.nodeType?n+=e.nodeValue:4==e.nodeType&&(n+="<![CDATA["+e.nodeValue+"]]>");return n},i=e.firstChild;i;i=i.nextSibling)n+=t(i);return n},escape:function(e){return e.replace(/[\\]/g,"\\\\").replace(/[\"]/g,'\\"').replace(/[\n]/g,"\\n").replace(/[\r]/g,"\\r")},removeWhite:function(e){e.normalize();for(var n=e.firstChild;n;)if(3==n.nodeType)if(n.nodeValue.match(/[^ \f\n\r\t\v]/))n=n.nextSibling;else{var i=n.nextSibling;e.removeChild(n),n=i}else 1==n.nodeType?(t.removeWhite(n),n=n.nextSibling):n=n.nextSibling;return e}};9==e.nodeType&&(e=e.documentElement);var i=t.toJson(t.toObj(t.removeWhite(e)),e.nodeName,"	");return"{\n"+n+(n?i.replace(/\t/g,n):i.replace(/\t|\n/g,""))+"\n}"}
