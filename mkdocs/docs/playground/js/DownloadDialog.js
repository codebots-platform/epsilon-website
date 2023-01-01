import Handlebars from "handlebars/lib/handlebars";
import JSZip from "jszip";
import { language, secondProgramPanel } from "./Playground.js";

class DownloadDialog {
    show(event) {
        event.preventDefault();
        var self = this;
        Metro.dialog.create({
            title: "Download",
            content: "<p>You can download this example and run it locally through Gradle or Maven. Please choose your preferred format below. </p><br/><select id='format' data-role='select'><option value='gradle'>Gradle</option><option value='maven'>Maven</option></select>",
            actions: [
               {
                    caption: "Download",
                    cls: "js-dialog-close success",
                    onclick: function(){
                        var zip = new JSZip();
                        var extension = language == "flock" ? "mig" : language;
                        zip.file("program." + extension, programPanel.getEditor().getValue());
                        if (language == "egx") zip.file("template.egl", secondProgramPanel.getEditor().getValue());

                        if (language == "etl" || language == "flock") {
                            zip.file("source.flexmi", firstModelPanel.getValue());
                            zip.file("source.emf", firstMetamodelPanel.getValue());
                            zip.file("target.emf", secondMetamodelPanel.getValue());
                        }
                        else {
                            zip.file("model.flexmi", firstModelPanel.getValue());
                            zip.file("metamodel.emf", firstMetamodelPanel.getValue());
                        }
    
                        var format = document.getElementById("format").value;
                        
                        var templateData = {
                            language: language, 
                            task: language == "egx" ? "egl" : language,
                            extension: extension,
                            etl: language == "etl",
                            flock: language == "flock",
                            etlOrFlock: language == "etl" || language == "flock",
                            egl: language == "egl"
                        };
    
                        if (format == "gradle") {
                            var template = Handlebars.compile(self.fetchTemplate("build.gradle"));
                            zip.file("build.gradle", template(templateData));
                            zip.file("readme.md", self.fetchTemplate("readme-gradle.txt"));
                        }
                        else if (format == "maven") {
                            var template = Handlebars.compile(self.fetchTemplate("pom.xml"));
                            zip.file("pom.xml", template(templateData));
                            zip.file("readme.md", self.fetchTemplate("readme-maven.txt"));
                        }
    
                        // var img = zip.folder("images");
                        zip.generateAsync({type:"blob"})
                        .then(function(content) {
                            var blob = new Blob([content], { type: "application/zip" });
                            var url = window.URL || window.webkitURL;
                            var link = url.createObjectURL(blob);
                            var a = document.createElement("a");
                            a.setAttribute("download", "playground-example.zip");
                            a.setAttribute("href", link);
                            a.click();
                        });
                    }
                },
               {
                    caption: "Cancel",
                    cls: "js-dialog-close"
                }
            ]
        });
    }

    /**
     * Fetches the content of a file under the templates folder
     */
    fetchTemplate(name) {
        var xhr = new XMLHttpRequest();
        var url = "templates/" + name;
        xhr.open("GET", url, false);
        xhr.send();
        if (xhr.status === 200) {    
            return xhr.responseText;
        }
    }
}

export { DownloadDialog };