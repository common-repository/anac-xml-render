// JavaScript Document: tablePagerFilter.js
// Script Name: Table Pager Searcher
// Plugin URI: www.softcos.eu
// Description: Script per paginazione e ricerca in tabella HTML
// Author: Enzo Costantini (SoftCos)
// Author URI: www.softcos.eu 
// 
function tableNavigate(selfName, tableName, itemsPerPage) {
  this.table = document.getElementById(tableName); // controllare che esista!!!
  this.itemsPerPage = itemsPerPage;
  this.selfName = selfName;
  this.filteredRows = [];
  // struttura dell'array columnTypes [{a: "AVG"|"SUM"|"COUNT"|"MIN"|"MAX", t: "INT"|"FLOAT"|"CURR", d: ##}...]
  this.columnTypes = [];
  this.aggregatedValues = [];
  this.totalRow = null;
  this.subTotalRow = null;
  
  this.searchObj = [];
  this.pages = 0;
  this.inited = false;
  
  this.createTotalBar = function() {
    if (this.table.tFoot) {
      this.totalRow = this.table.tFoot.insertRow(0);
    } else {
      this.totalRow = this.table.createTFoot().insertRow(0); 
    }
    var cols =  this.table.rows[0].cells.length;    
    var innerRow = '';
    for (var i = 0; i < cols; i++) {
      innerRow += '<th>&nbsp;</th>';
    }
    this.totalRow.innerHTML = innerRow;
    for (var i = 0; i < cols; i++) {
      if (this.columnTypes[i]["t"] == "CURR"){
        this.totalRow.cells[i].className = 'number';
      }
    }
  }
    
  this.createExportBar = function(){
    var row = (this.table.tFoot) ? (this.table.tFoot.insertRow(-1)) : (this.table.createTFoot().insertRow(-1));
    var cols =  this.table.rows[0].cells.length;
    row.innerHTML = '<th id="exportBar" colspan="' + cols + '"></th>';
    row.cells[0].innerHTML = 
    'Esporta:&emsp;<a style="text-decoration: none;" download="' + document.domain + '_L190_bandi.json" href="#" onclick="return ExpFromXML(this, \'json\');">' +  
    '<button class="export_btn" title="Esporta i dati in formato JSon">JSon</button></a>';
    row.cells[0].innerHTML +=  
    '&nbsp;<a style="text-decoration: none;" download="' + document.domain + '_L190_bandi.xml" href="#" onclick="return ExpFromXML(this, \'xml\');">' +  
    '<button class="export_btn" title="Esporta i dati in formato Xml">Xml</button></a>';
  }
  
  this.createNavBar = function(){
    var row = (this.table.tHead) ? (this.table.tHead.insertRow(0)) : (this.table.createTHead().insertRow(0));
    var cols =  this.table.tBodies[0].rows[0].cells.length;
    row.innerHTML = '<tr><th id="navBar" colspan="' + cols + '">Barra di navigazione</th></tr>';
  }
  
  this.createSearchBar = function(){
    var cols =  this.table.rows[0].cells.length;
    var row = this.table.tHead.insertRow(-1);
    row.setAttribute("id", "searchBar"); 
    var th, input;
    this.searchObj = [];
    for (var i = 0; i < cols; i++) {
      th = document.createElement('th');
      row.appendChild(th);
      input = document.createElement('input');
      input.setAttribute('type', 'search');
      input.setAttribute('spellcheck', 'false');
      input.setAttribute('oninput', this.selfName + '.filterRows(this.value, ' + i + ');');
      th.appendChild(input);
      this.searchObj.push(input);
    }
  }
  
  this.createSubTotalBar = function() {
    var cols =  this.table.rows[0].cells.length;
    this.subTotalRow = this.table.tHead.insertRow(-1); 
    this.subTotalRow.style.display = 'none';  
    var innerRow = '';
    for (var i = 0; i < cols; i++) {
      innerRow += '<th>&nbsp;</th>';
    }
    this.subTotalRow.innerHTML = innerRow;
    for (var i = 0; i < cols; i++) {
      if (this.columnTypes[i]["t"] == "CURR"){
        this.subTotalRow.cells[i].className = 'number';
      }
    }
  }
  
  this.showRecords = function(from, to) {
    var rows = this.table.tBodies[0].rows;
    var l = rows.length;
    for (var i = 0; i < l; i++) {
      rows[i].style.display = 'none';
    }
    l = this.filteredRows.length;
    for (var i = 0; i < l; i++) {
      if (i >= from && i <= to) {
        rows[this.filteredRows[i]].style.display = '';
      }
    }
  }
  
  this.showPage = function(pageNumber) {
    if (!this.inited) {
      alert("Attenzione! L'oggetto non &grave; stato inizializzato...");
      return;
    }
    this.currentPage = pageNumber;
    this.showPageNav();
    var from = (pageNumber - 1) * itemsPerPage;
    var to = from + this.itemsPerPage - 1;
    this.showRecords(from, to);
  }
  
  this.first = function() {
    if (this.currentPage > 1) {
      this.showPage(1);
    }
  }
  
  this.last = function() {
    if (this.currentPage < this.pages) {
      this.showPage(this.pages);
    }
  }
  
  this.prev = function() {
    if (this.currentPage > 1) {
      this.showPage(this.currentPage - 1);
    }
  }
  
  this.next = function() {
    if (this.currentPage < this.pages) {
      this.showPage(this.currentPage + 1);
    }
  }
  
  this.processRow = function(row) {
    for (var j = 0; j < this.columnTypes.length; j++) {
      if (this.columnTypes[j]["a"] == "COUNT") {
  
        this.aggregatedValues[j]++;
  
      } else if (this.columnTypes[j]["a"] == "SUM") {
  
        this.aggregatedValues[j] += string2Num(row.cells[j].innerHTML);
  
      } else if (this.columnTypes[j]["a"] == "AVG") {
  
        this.aggregatedValues[j] = (this.aggregatedValues[j] * (i) + string2Num(row.cells[j].innerHTML)) / (i + 1);
  
      } else if (this.columnTypes[j]["a"] == "MIN") {
  
        if (i == 0) {
          this.aggregatedValues[j] = string2Num(rows[i].cells[j].innerHTML);
        } else {
          this.aggregatedValues[j] = Math.min(this.aggregatedValues[j], string2Num(row.cells[j].innerHTML));
        }
  
      } else if (this.columnTypes[j]["a"] == "MAX") {
  
        this.aggregatedValues[j] = Math.max(this.aggregatedValues[j], string2Num(row.cells[j].innerHTML));
  
      }
    }
  }
  
  this.calculateTotals = function (rowBar, subSetRows) {
    if (!rowBar) { return; }
    var rows = this.table.tBodies[0].rows;
    this.aggregatedValues = [];
    for (var i = 0; i < this.columnTypes.length; i++) {
      this.aggregatedValues.push(0);
    }
    if (subSetRows) {
      for (var i = 0; i < subSetRows.length; i++) {
        this.processRow(rows[subSetRows[i]]);
      }
    } else {
      for (var i = 0; i < rows.length; i++) {
        this.processRow(rows[i]);
      }
    }
    
    for (var j = 0; j < this.columnTypes.length; j++) {
        if (this.columnTypes[j]['a']) {
          if (this.columnTypes[j]["t"]) {
            if (this.columnTypes[j]["t"] == "CURR") {
            
              rowBar.cells[j].innerHTML = this.aggregatedValues[j].format(2, 3, ' ', ',');
            
            } else if (this.columnTypes[j]["t"] == "FLOAT") {
              if (this.columnTypes[j]["d"]) {
                rowBar.cells[j].innerHTML = this.aggregatedValues[j].format(this.columnTypes[j]["d"], 3, ' ', ',');  
              } else {
                rowBar.cells[j].innerHTML = this.aggregatedValues[j].format(2, 3, ' ', ',');
              }
            
            } else {
            
              rowBar.cells[j].innerHTML = this.aggregatedValues[j].format(0, 3, ' ', ',');
            
            }
          } 
        }
      }
    
  }
  
  this.filterRows = function(keySearch, Id) {
    Id = Id ? Id : 0;
    var rows = this.table.tBodies[0].rows;
    var l = rows.length;
    
    for (var i = 0; i < this.searchObj.length; i++) {
      if (i != Id) {
        this.searchObj[i].value = '';
      } 
    }
    
    this.aggregatedValues = [];
    for (var i = 0; i < this.columnTypes.length; i++) {
      this.aggregatedValues.push(0);
    }
    
    this.filteredRows = [];
    for (var i = 0; i < l; i++) {
// filtra le righe
      var t = rows[i].cells[Id].innerHTML.replace(/(<([^>]+)>)/ig,"");  
      var regExp = new RegExp(keySearch, 'i');
      if (regExp.test(t)) {
        this.filteredRows.push(i);
      }
    }
// calcola i totali parziali
    if (keySearch && (this.filteredRows.length > 0)) {
      this.calculateTotals(this.subTotalRow, this.filteredRows)
      this.subTotalRow.style.display = '';
    } else {
      this.subTotalRow.style.display = 'none';
    }

    this.pages = Math.ceil(this.filteredRows.length / this.itemsPerPage);;
    this.currentPage = 1;
    this.showPage(1);
  }
  
  this.init = function() {
    this.createTotalBar();
    this.createSearchBar();
    this.createSubTotalBar();
    this.createExportBar();
    this.createNavBar();
    this.calculateTotals(this.totalRow);
    this.inited = true;
    this.filterRows('');
  }
  
  this.showPageNav = function() {
    if (!this.inited) {
      return;
    }
    var firstPage = 0;
    var lastPage = 0;
    if (this.currentPage <= 2) {
      firstPage = 1;
    } else {
      firstPage = this.currentPage - 2;
    }
    if (this.currentPage >= this.pages - 2) {
      lastPage = this.pages;
    } else {
      lastPage = this.currentPage + 2;
    }
    var pagerHtml = '<span class="nav_btn first" onclick="' + this.selfName + '.first();" title="Prima pagina" >&emsp;</span>' + 
                    '<span class="nav_btn prev" onclick="' + this.selfName + '.prev();" title="Pagina precedente">&emsp;</span> | ';
    for (var page = firstPage; page <= lastPage; page++) 
        pagerHtml += '<span title="Vai a pagina ' + page + '" id="pg' + page + '" class="pg-normal" onclick="' + this.selfName + '.showPage(' + page + ');">' + page + '</span> | ';
    pagerHtml += '<span class="nav_btn next" onclick="' + this.selfName + '.next();" title="Pagina seguente">&emsp;</span>' + 
                 '<span class="nav_btn last" onclick="' + this.selfName + '.last();" title="Ultima pagina">&emsp;</span>';
    
    
    this.table.tHead.rows[0].cells[0].innerHTML = pagerHtml; 
    
//     var element = document.getElementById("navDiv");
//     element.innerHTML = pagerHtml;
    var newPageAnchor = document.getElementById('pg' + this.currentPage);
    if (newPageAnchor) newPageAnchor.className = 'pg-selected';
  }
}

/**
 * Number.prototype.format(n, x, s, c)
 * 
 * @param integer n: length of decimal
 * @param integer x: length of section
 * @param mixed   s: section delimiter
 * @param mixed   c: decimal delimiter
 */

Number.prototype.format = function(n, x, s, c) {
    var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
        num = this.toFixed(Math.max(0, ~~n));
    return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
}

String.prototype.replaceAll = function(str1, str2, ignore) {
  return this.replace(new RegExp(str1.replace(/([\/\,\!\\\^\$\{\}\[\]\(\)\.\*\+\?\|\<\>\-\&])/g, "\\$&"), (ignore ? "gi" : "g")), (typeof(str2) == "string") ? str2.replace(/\$/g, "$$$$") : str2);
}

function string2Num(n) {
  n = n.replaceAll('.', '');
  n = n.replaceAll(' ', '');
  return parseFloat(n.replaceAll(',', '.'));
}
