/* 
 * Pharinix Copyright (C) 2015 Pedro Pelaez <aaaaa976@gmail.com>
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

/**
 * http://stackoverflow.com/questions/4810841/how-can-i-pretty-print-json-using-javascript
 * http://jsfiddle.net/KJQ9K/554/
 * @param JSON json
 * @returns string HTML markup
 */
function jsonSyntaxHighlight(json) {
    json = JSON.stringify(json, undefined, 4);
    json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function(match) {
        var cls = 'number';
        if (/^"/.test(match)) {
            if (/:$/.test(match)) {
                cls = 'key';
            } else {
                cls = 'string';
            }
        } else if (/true|false/.test(match)) {
            cls = 'boolean';
        } else if (/null/.test(match)) {
            cls = 'null';
        }
        return '<span class="' + cls + '">' + match + '</span>';
    });
}
    
function loadCmdDef(cmd, callback) {
    var data;
    var isRemote = (getRemoteHostUrl() != PHARINIX_ROOT_URL);
    if (!isRemote) {
        data = {
            command: "man",
            cmd: cmd,
            interface: "echoJson",
        };
    } else {
        data = {
            command: "remoteAPICall",
            interface: "echoJson",
            host: getRemoteHostUrl(),
            rcmd: "man",
            iface: "echoJson",
            cmd: cmd,
        };
    }
    $.ajax({
        type: "POST",
        url:  PHARINIX_ROOT_URL,
        data: data
    }).done(callback);    
}

function execute(query, dataType, callback) {
    $("#response").html("...");
    $.ajax({
        type: "POST",
        url:  getRemoteHostUrl(),
        data: query,
        dataType: dataType,
    }).done(callback);    
}

function clearParamsTable() {
    $.each($('#paramsTable > tbody:last'), function(i, item){
        $.each($(item).find('tr'), function(i, sub){
            var fixme = $(sub).find('[name^="pfix"]');
            if ($(fixme).length && $(fixme).prop('checked')) {
                // Retain it
            } else {
                $(sub).remove();
            }
        });
    });
//    $('#paramsTable > tbody:last').empty();
}

function addParamToTable(name, type, help, defValue) {
    if(!defValue) defValue = "";
    var html = "<tr>";
    html += "<td>";
    // Hidde a input field to set the parameter name
    if (!name) {
        html += '<input name="pfix[]" style="float:left;" type="checkbox" data-toggle="tooltip" title="'+__('Fix me')+'" value="1">';
        html += '<input class="form-control" style="width:90%;float:left;" name="pname[]" type="text">';
    } else {
        html += '<a href="#" data-toggle="tooltip" title="'+help+'"><span class="glyphicon glyphicon-question-sign" aria-hidden="true"></span></a> ';
        html += '<input name="pname[]" type="hidden" value="'+name+'">';
        html += '<b>'+name+'</b> <span class="badge">'+type+'</span>';
    }
    html += "</td>";
    html += "<td>";
    // Show input control by type
    switch (type) {
        case 'file':
            html += '<input class="form-control" name="pvalue[]" type="file">';
            break;
        default:
            html += '<input class="form-control" name="pvalue[]" type="text" value="'+defValue+'">';
    }
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
    html += '<option value="echoText" '+(defValue=='echoText'?"selected":"")+'>Text</option>';
    html += '<option value="echoJson" '+(defValue=='echoJson'?"selected":"")+'>JSON</option>';
    html += '<option value="echoSimpleXML" '+(defValue=='echoSimpleXML'?"selected":"")+'>Simple XML</option>';
//    html += '<option value="echoXml" '+(defValue=='echoXml'?"selected":"")+'>XML</option>';
    html += '</select>';
    html += "</td>";
    html += "</tr>";
    if (help) {
        
    }
    
    $('#paramsTable > tbody:last').prepend(html);
}

function refreshCommandList() {
    $("#cmdHelp").html('');
    clearParamsTable();
    
    var data;
    var isRemote = (getRemoteHostUrl() != PHARINIX_ROOT_URL);
    if (!isRemote) {
        data = {
            command: "getCommandList",
            interface: "echoJson",
        };
    } else {
        data = {
            command: "remoteAPICall",
            interface: "echoJson",
            host: getRemoteHostUrl(),
            rcmd: "getCommandList",
            iface: "echoJson",
        };
    }
    $.ajax({
        type: "POST",
        url:  PHARINIX_ROOT_URL,
        data: data,
    }).done(function ( data ) {
        var opts = "";
        $("#cmdList").html('');
        $("#cmdList").append('<option></option>');
        $.each(data.commands, function(i, item){
            $("#cmdList").append('<option>'+item+'</option>');
        });
    });
}

function getRemoteHostUrl() {
    var resp = $("#urlHost").val();
    if (resp == '') {
        resp = PHARINIX_ROOT_URL;
    }
    return resp;
}

$(document).ready(function(){
    refreshCommandList();

    $("#urlHost").on('change', function() {
        refreshCommandList();
    });
    
    $("#cmdList").change(function(){
        var cmd = $("#cmdList").val();
        loadCmdDef(cmd, function(data){
            var cmdHelp;
            if (data.ok === false) {
                cmdHelp = {
                    description: data.msg,
                    echo: false,
                    type: {
                        parameters: {
                            name: 'any',
                            type: 'args',
                        }
                    }
                };
            } else {
                cmdHelp = data.help[cmd];
            }
            $("#cmdHelp").html(cmdHelp.description);
            clearParamsTable();
            var defInterface = "echoJson";
            if (cmdHelp.echo) {
                defInterface = "echoHtml";
            }
            addInterfaceToTable("interface", "string", __("Required server MIME type interface to use"),defInterface);
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
    $("#addFileRow").click(function(){
        addParamToTable(null, 'file');
    });
    
    $("#executeCmd").click(function(){
        $("#response").html("...");
        var isRemote = (getRemoteHostUrl() != PHARINIX_ROOT_URL);
        // Build form
        // http://stackoverflow.com/a/8758614
        var frm = $("#remoteApi :input").not('[name^="pfix"]');
        var formData = new FormData();
        var interface = frm[3].value;
        if (!isRemote) {
            formData.append("command", $("#cmdList option:selected").text());
        } else {
            formData.append("command", 'remoteAPICall');
            formData.append("host", getRemoteHostUrl());
            formData.append("rcmd", $("#cmdList option:selected").text());
            formData.append("iface", interface);
        }
        for(var i = 2; i < frm.length; i = i+2) {
            if (frm[i+1].type == 'file') {
                var fileInput = frm[i].value;
                $.each(frm[i+1].files, function(i, file) {
                    formData.append(fileInput, file);
                });
            } else if (frm[i+1].value != "") {
                formData.append(frm[i].value, frm[i+1].value);
            }
        }
        
        // Send data
        var dataType = "json";
        switch(interface.interface) {
            case "echoSimpleXML":
                dataType = null;
                break;
            case "echoJson":
                dataType = null;
            default:
                dataType = "text";
                break;
        }
        remoteApiCall(PHARINIX_ROOT_URL, formData, function(data){
            var resp = data;
            console.log(resp);
            $('#response').removeClass();
            switch(interface) {
                case "echoJson":
                    $('#response').addClass('json_hightlight');
                    resp = jsonSyntaxHighlight(data);
                case "echoText":
                    resp = "<pre>" + resp + "</pre>";
                    break;
                case "echoSimpleXML":
                    var raw = (new XMLSerializer().serializeToString(resp));
                    raw = raw.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    resp = "<pre class=\"brush: xml\">" + raw + "</pre>";
                    break;
            }
            if (resp == '') {
                resp = '<div class="alert alert-danger">';
                resp += __("I can't get response.");
                resp += " " + __("Please, try with other interface type.");
                resp += '</div>';
            }
            $("#response").html(resp);
            SyntaxHighlighter.all('response');
        });
    });
});

// http://alexgorbatchev.com/SyntaxHighlighter/scripts/main.js
function path()
{
	var args = arguments,
		result = []
		;
		
	for(var i = 0; i < args.length; i++)
		result.push(args[i].replace('@', PHARINIX_ROOT_URL + 'usr/syntaxhighlighter/'));
		
	return result
};

SyntaxHighlighter.autoloader.apply(null, path(
	'applescript			@shBrushAppleScript.js',
	'actionscript3 as3		@shBrushAS3.js',
	'bash shell				@shBrushBash.js',
	'coldfusion cf			@shBrushColdFusion.js',
	'cpp c					@shBrushCpp.js',
	'c# c-sharp csharp		@shBrushCSharp.js',
	'css					@shBrushCss.js',
	'delphi pascal			@shBrushDelphi.js',
	'diff patch pas			@shBrushDiff.js',
	'erl erlang				@shBrushErlang.js',
	'groovy					@shBrushGroovy.js',
	'java					@shBrushJava.js',
	'jfx javafx				@shBrushJavaFX.js',
	'js jscript javascript	@shBrushJScript.js',
	'perl pl				@shBrushPerl.js',
	'php					@shBrushPhp.js',
	'text plain				@shBrushPlain.js',
	'py python				@shBrushPython.js',
	'powershell ps posh		@shBrushPowerShell.js',
	'ruby rails ror rb		@shBrushRuby.js',
	'sass scss				@shBrushSass.js',
	'scala					@shBrushScala.js',
	'sql					@shBrushSql.js',
	'vb vbnet				@shBrushVb.js',
	'xml xhtml xslt html	@shBrushXml.js'
));