var datagrid = {}, index = 0, size = 97, generateDataGrid, data, datos;
 var datastore = {
              "1": [
                {
                  "value": "103",
                  "label": "103"
                },
                {
                  "value": "HQ336336HGG45JH5G45H4H5G4JHG54",
                  "label": "HQ336336HGG45JH5G45H4H5G4JHG54"
                },
                {
                  "value": "2004-10-19",
                  "label": "2004-10-19"
                },
                {
                  "value": "6066.78",
                  "label": "6066.78"
                }
              ],
              "2": [
                {
                  "value": "103",
                  "label": "103"
                },
                {
                  "value": "JM555205JDASD897AS7DAS87D9AS7D7AS98D7AS98D7AS87D9A",
                  "label": "JM555205JDASD897AS7DAS87D9AS7D7AS98D7AS98D7AS87D9A"
                },
                {
                  "value": "2003-06-05",
                  "label": "2003-06-05"
                },
                {
                  "value": "14571.44",
                  "label": "14571.44"
                }
              ],
              "3": [
                {
                  "value": "103",
                  "label": "103"
                },
                {
                  "value": "OM314933",
                  "label": "OM314933"
                },
                {
                  "value": "2004-12-18",
                  "label": "2004-12-18"
                },
                {
                  "value": "1676.14",
                  "label": "1676.14"
                }
              ],
              "4": [
                {
                  "value": "112",
                  "label": "112"
                },
                {
                  "value": "BO864823",
                  "label": "BO864823"
                },
                {
                  "value": "2004-12-17",
                  "label": "2004-12-17"
                },
                {
                  "value": "14191.12",
                  "label": "14191.12"
                }
              ],
              "5": [
                {
                  "value": "112",
                  "label": "112"
                },
                {
                  "value": "HQ55022",
                  "label": "HQ55022"
                },
                {
                  "value": "2003-06-06",
                  "label": "2003-06-06"
                },
                {
                  "value": "32641.98",
                  "label": "32641.98"
                }
              ]
            };
generateDataGrid = function (){
  for(var i = 0 ; i < size ; i+=1){
    datagrid[(i+1).toString()] = datastore[(Math.floor((Math.random() * 5) + 1)).toString()];
  }
  return datagrid;
}
window.onload = function () {
    var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
        lineNumbers: true,
        matchBrackets: true,
        autoCloseBrackets: true,
        mode: "application/ld+json",
        viewportMargin: Infinity
    });
    editor.setSize(350, 500);
    document.getElementById("file").onchange = function (e) {
        var r = new FileReader();
        r.readAsText(this.files[0], "UTF-8");
        r.onload = function (e) {
            editor.setValue(e.target.result);
            $(".pmdynaform-container").remove();
        };
        this.form.reset();
    };
    $(".CodeMirror").hide();
    document.getElementById("test").onclick = function () {
      datos = generateDataGrid();
      $(".pmdynaform-container").remove();
      var uno = new Date(), dos, tiempo, m, s;
      isd = 0;
      console.clear();
data = {
  "name": "3333333",
  "description": "",
  "items": [
    {
      "type": "form",
      "id": "54490038555f1f074790940093155208",
      "name": "3333333",
      "description": "",
      "mode": "edit",
      "script": "",
      "language": "en",
      "externalLibs": "",
      "items": [
        [
          {
            "type": "grid",
            "variable": "grid",
            "id": "grid",
            "name": "grid",
            "label": "grid_1",
            "hint": "",
            "columns": [
              {
                "type": "text",
                "variable": "",
                "dataType": "",
                "id": "text0000000007",
                "name": "text0000000007",
                "label": "text_10",
                "defaultValue": "",
                "placeholder": "",
                "hint": "",
                "required": false,
                "textTransform": "none",
                "validate": "",
                "validateMessage": "",
                "maxLength": 1000,
                "formula": "",
                "mode": "parent",
                "operation": "",
                "dbConnection": "workflow",
                "dbConnectionLabel": "PM Database",
                "sql": "",
                "options": [],
                "columnWidth": "10",
                "width": 100,
                "title": "text_10"
              },
              {
                "type": "text",
                "variable": "",
                "dataType": "",
                "id": "text0000000008",
                "name": "text0000000008",
                "label": "text_11",
                "defaultValue": "",
                "placeholder": "",
                "hint": "",
                "required": false,
                "textTransform": "none",
                "validate": "",
                "validateMessage": "",
                "maxLength": 1000,
                "formula": "",
                "mode": "parent",
                "operation": "",
                "dbConnection": "workflow",
                "dbConnectionLabel": "PM Database",
                "sql": "",
                "options": [],
                "columnWidth": "10",
                "width": 100,
                "title": "text_11"
              },
              {
                "type": "text",
                "variable": "",
                "dataType": "",
                "id": "text0000000009",
                "name": "text0000000009",
                "label": "text_12",
                "defaultValue": "",
                "placeholder": "",
                "hint": "",
                "required": false,
                "textTransform": "none",
                "validate": "",
                "validateMessage": "",
                "maxLength": 1000,
                "formula": "text0000000007+text0000000008",
                "mode": "parent",
                "operation": "",
                "dbConnection": "workflow",
                "dbConnectionLabel": "PM Database",
                "sql": "",
                "options": [],
                "columnWidth": "10",
                "width": 100,
                "title": "text_12"
              }
            ],
            "data": {
                1: [
                    {
                        "value" : "1",
                        "label" : "1"
                    },
                                            {
                        "value" : "1",
                        "label" : "1"
                    },
                    {
                        "value" : "1",
                        "label" : "1"
                    }
                ],
                2: [
                    {
                        "value" : "1",
                        "label" : "1"
                    },
                                            {
                        "value" : "1",
                        "label" : "1"
                    },
                    {
                        "value" : "1",
                        "label" : "1"
                    }
                ],
                3: [
                    {
                        "value" : "1",
                        "label" : "1"
                    },
                                            {
                        "value" : "1",
                        "label" : "1"
                    },
                    {
                        "value" : "1",
                        "label" : "1"
                    }
                ]        
            },
            "rows" : "3",
            "mode": "parent",
            "layout": "responsive",
            "pageSize": "0",
            "addRow": true,
            "deleteRow": true,
            "title": "grid_1",
            "colSpan": 12
          }
        ]
      ],
      "variables": []
    }
  ]
}
      window.project = new PMDynaform.core.Project({
          data: data, //JSON.parse(editor.getValue()),
          renderTo: document.getElementById("container"),
          submitRest: true,
          keys: {
              server: "http://ronald3.pmos.colosa.net/", //"http://michelangelo.pmos3.colosa.net/",
              projectId: "498574572555a0fa9474b40040026428", //"25084755253f3a016907523058545566",
              workspace: "testAdmin" //"workflow3"
          },
          token: {
              accessToken: "4b3fd851be263b320f10731b0d2aa444c3fdf8e9" //"db0498b53483bb840e996a27d23ace1d49f1e35b"
          },
          renderTo : document.body
      });
      $(".pmdynaform-container").css({
          "float":"left",
          "width" : "100%"
      });
      dos = new Date();
      tiempo = (dos - uno) / 1000;
      m = Math.floor(tiempo / 60);
      tiempo -= m;
      s = Math.floor(tiempo);
      tiempo -= s;
      $('#lista').append('<li>Add ' + size + ' datos de fila ' + m  + 'm '+ s + 's ' + Math.round(tiempo * 1000) + 'ms</li>');
    };
    document.getElementById("run_script").onclick = function () {
      for (i = 0 ; i < window.project.forms[0].items.asArray().length ; i+=1) {
        //fields.push($("#"+window.project.forms[i].items.asArray()[i].model.get("id"));
        console.log($("#"+window.project.forms[0].items.asArray()[i].model.get("id")).getText()) ;
      }
    }

    document.getElementById("format").onclick = function () {
        var a = editor.getCursor(true);
        var b = editor.getCursor(false);
        if (a === b) {
            a = {line: 0, ch: 0};
            b = {line: editor.doc.lastLine(), ch: editor.getValue().length};
        }
        editor.autoFormatRange(a, b);
    };
    document.getElementById("desktop").onclick = function () {
        $(".pmdynaform-container").css({"width": "1024px","float" : "left"});
    };
    document.getElementById("tablet").onclick = function () {
        $(".pmdynaform-container").css({"width": "800px", "float" : "left"});
    };
    document.getElementById("smartphone").onclick = function () {
        $(".pmdynaform-container").css({"width": "400px","float" : "left"});
    };
};