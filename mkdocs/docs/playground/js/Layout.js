import {programPanel, secondProgramPanel, consolePanel, firstModelPanel, firstMetamodelPanel, secondModelPanel, secondMetamodelPanel, outputPanel} from './Playground.js';

class Layout {

    create(rootId, language) {
        var root = document.getElementById(rootId);
        root.innerHTML = "";

        var splitter;
        
        if (language == "eol") {
            splitter = Layout.createHorizontalSplitter(
                [
                    Layout.createVerticalSplitter([programPanel.getElement(), consolePanel.getElement()]),
                    Layout.createVerticalSplitter([firstModelPanel.getElement(), firstMetamodelPanel.getElement()])
                ]
            );
        }
        else if (language == "evl" || language == "epl" || language == "egl") {
            splitter = Layout.createHorizontalSplitter(
                [
                    Layout.createVerticalSplitter([programPanel.getElement(), consolePanel.getElement()]),
                    Layout.createHorizontalSplitter([
                        Layout.createVerticalSplitter([firstModelPanel.getElement(), firstMetamodelPanel.getElement()]),
                        Layout.createVerticalSplitter([outputPanel.getElement()])
                    ], "50, 50")
                ], "33, 64"
            );
        }
        else if (language == "etl" || language == "flock") {
            splitter = Layout.createHorizontalSplitter(
                [
                    Layout.createVerticalSplitter([programPanel.getElement(), consolePanel.getElement()]),
                    Layout.createHorizontalSplitter([
                        Layout.createVerticalSplitter([firstModelPanel.getElement(), firstMetamodelPanel.getElement()]),
                        Layout.createVerticalSplitter([secondModelPanel.getElement(), secondMetamodelPanel.getElement()])
                    ], "50, 50")
                ], "33, 64"
            );
        }
        else if (language == "egx") {
            splitter = Layout.createHorizontalSplitter(
                [
                    Layout.createVerticalSplitter([Layout.createVerticalSplitter([programPanel.getElement(), secondProgramPanel.getElement()]), consolePanel.getElement()]),
                    Layout.createHorizontalSplitter([
                        Layout.createVerticalSplitter([firstModelPanel.getElement(), firstMetamodelPanel.getElement()]),
                        Layout.createVerticalSplitter([outputPanel.getElement()])
                    ], "50, 50")
                ], "33, 64"
            );
        }

        splitter.setAttribute("class", "h-100");
        splitter.setAttribute("id", "splitter");
        splitter.setAttribute("style", "min-height:800px");
        root.appendChild(splitter);
    }

    static createHorizontalSplitter(components, split = "50, 50") {
        return Layout.createSplitter(true, components, split);
    }

    static createVerticalSplitter(components, split = "50, 50") {
        return Layout.createSplitter(false, components, split);
    }

    static createSplitter(horizontal, components, split) {
        var splitter = document.createElement("div");
        splitter.setAttribute("data-role", "splitter");
        splitter.setAttribute("data-on-resize-stop", "fit()");
        splitter.setAttribute("data-on-resize-split", "fit()");
        splitter.setAttribute("data-on-resize-window", "fit()");
        splitter.setAttribute("data-split-sizes", split);

        if (!horizontal) splitter.setAttribute("data-split-mode", "vertical");
        components.forEach(component => splitter.appendChild(component));
        return splitter;
    }

}

export { Layout };