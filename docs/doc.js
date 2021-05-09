window.addEventListener('load', () => {
    fetch(DocumentationReadMePage.getDocumationDir() + "/../README.MD").then((response) => {
        response.text().then((text) => {
            let page = new DocumentationReadMePage();
            page.init(text);
        });
    });
});
class DocumentationReadMePage {
    static getDocumationDir() {

        let url = location.href;
        return url.substr(0, url.lastIndexOf('/'));

    }

    init(text) {
        this.initReadMe(text);
    }
    initReadMe(readMe) {
        let html = marked(readMe);
        let content = document.getElementById('content');
        content.innerHTML = html;
        let navBar = new DocumentationNavBar();
        navBar.init();
        navBar.initButtons(content.getElementsByTagName('h2'));
    }
}
class DocumentationNavBar
{
    init() {
        this.maxCharPerLine = 60.0;
        this.links = [{href: DocumentationReadMePage.getDocumationDir() + '/../../../', label: "Home"}];
        this.charCount = 0.0 + this.links[0].label.length;
        this.totalWeight = 1.0;
        this.navBar = document.getElementById('NavBar');
    }
    initButtons(subheadings)
    {
        for (let elem of subheadings) {
            let label = elem.innerHTML.toString();
            this.charCount += label.length;
            this.links.push({href: DocumentationReadMePage.getDocumationDir() + '/doc.html#' + elem.id, label: label});
        }
        this.numLines = Math.round(this.charCount / this.maxCharPerLine);
        for (let link of this.links)
        {
            this.addButton(link, this.calcWeight(link.label));
        }
    }
    calcWeight(label) {
        return this.numLines / this.charCount * label.length;
    }
    addButton(link, weight) {
        this.totalWeight += weight;
        if (this.totalWeight > 1 + 0.5 * weight) {
            this.currentRow = document.createElement("div");
            this.currentRow.setAttribute("class", "NavRow multi-button");
            this.totalWeight -= 1;
            this.navBar.appendChild(this.currentRow);
        }
        let button = document.createElement("a");
        button.href = link.href;
        button.innerHTML = link.label;
        this.currentRow.appendChild(button);
    }
}