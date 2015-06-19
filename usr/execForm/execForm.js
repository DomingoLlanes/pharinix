/* 
 * Copyright (C) 2015 Pedro Pelaez <aaaaa976@gmail.com>
 * Sources https://github.com/PSF1/pharinix
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
function loadCmdDef(cmd, callback) {
    $.ajax({
        type: "POST",
        url:  PHARINIX_ROOT_URL,
        data: {
            command: "man",
            cmd: cmd,
            interface: "echoJson",
        }
    }).done(callback);    
}

function execute(query, dataType, callback) {
    $("#response").html("...");
    $.ajax({
        type: "POST",
        url:  PHARINIX_ROOT_URL,
        data: query,
        dataType: dataType,
    }).done(callback);    
}

function clearParamsTable() {
    $('#paramsTable > tbody:last').empty();
}

function addParamToTable(name, type, help, defValue) {
    if(!defValue) defValue = "";
    var html = "<tr>";
    html += "<td>";
    if (!name) {
        html += '<input class="form-control" name="pname[]" type="text">';
    } else {
        html += '<a href="#" data-toggle="tooltip" title="'+help+'"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> ';
        html += '<input name="pname[]" type="hidden" value="'+name+'">';
        html += '<b>'+name+'</b> <span class="badge">'+type+'</span>';
    }
    html += "</td>";
    html += "<td>";
    html += '<input class="form-control" name="pvalue[]" type="text" value="'+defValue+'">';
    html += "</td>";
    html += "</tr>";
    if (help) {
        
    }
    
    $('#paramsTable > tbody:last').append(html);
}

function addInterfaceToTable(name, type, help, defValue) {
    if(!defValue) defValue = "";
    var html = "<tr>";
    html += "<td>";
    
    html += '<a href="#" data-toggle="tooltip" title="'+help+'"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> ';
    html += '<input name="pname[]" type="hidden" value="'+name+'">';
    html += '<b>'+name+'</b> <span class="badge">'+type+'</span>';
        
    html += "</td>";
    html += "<td>";
    html += '<select class="form-control" name="pvalue[]" >';
    html += '<option value="echoHtml" '+(defValue=='echoHtml'?"selected":"")+'>HTML</option>';
    html += '<option value="echoJson" '+(defValue=='echoJson'?"selected":"")+'>JSON</option>';
//    html += '<option value="echoXml" '+(defValue=='echoXml'?"selected":"")+'>XML</option>';
    html += '</select>';
    html += "</td>";
    html += "</tr>";
    if (help) {
        
    }
    
    $('#paramsTable > tbody:last').append(html);
}

$(document).ready(function(){
    $.ajax({
        type: "POST",
        url:  PHARINIX_ROOT_URL,
        data: {
            command: "getCommandList",
            interface: "echoJson",
        }
    }).done(function ( data ) {
        var opts = "";
        $("#cmdList").append('<option></option>');
        $.each(data.commands, function(i, item){
            $("#cmdList").append('<option>'+item+'</option>');
        });
    });
    
    $("#cmdList").change(function(){
        var cmd = $("#cmdList").val();
        loadCmdDef(cmd, function(data){
            var cmdHelp = data.help[cmd];
            $("#cmdHelp").html(cmdHelp.description);
            clearParamsTable();
            addInterfaceToTable("interface", "string", "Required server MIME type interface to use","echoJson");
            $.each(cmdHelp.type.parameters, function(name, type){
                if (type != "args") {
                    addParamToTable(name, type, cmdHelp.parameters[name]);
                }
            });
        });
    });
    
    $("#addRow").click(function(){
        addParamToTable();
    });
    
    $("#executeCmd").click(function(){
        var frm = $("#remoteApi").serializeArray();
        var query = {
            command: frm[0].value,
            interface: "echoJson",
        };
        for(var i = 1; i < frm.length; i = i+2) {
            if (frm[i+1].value != "") {
                query[frm[i].value] = frm[i+1].value;
            }
        }
        var dataType = "json";
        switch(query.interface) {
            case "echoJson":
                dataType = null;
            default:
                dataType = "text";
                break;
        }
        execute(query, dataType, function(data){
            var resp = data;
            switch(query.interface) {
                case "echoJson":
                    resp = JSON.stringify(JSON.parse(data), null, '\t');
                    resp = resp.replace(/\</g, '&lt;');
                case "echoXml":
                    resp = "<pre>" + resp + "</pre>";
                    break;
            }
            $("#response").html(resp);
        });
    });
});