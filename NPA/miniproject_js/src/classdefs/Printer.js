
/**
 * 
 */
class Printer {

    /** @type {NetGraph} */ n;
    /** @type {VisGraph} */ v;
    /** @type {string} */ c = 'current_';    

    /**
     * @param {VisGraph} graph 
     */
    constructor(graph) {
        this.v = graph;
        this.n = graph.netgraph;
    }

    // ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- Manual filler

    /**
     * Fills in information about Node | Edge inside the gray box
     * @private
     * @param {number} lineNumber
     * @param {string} description 
     * @param {any} content
     */
    fillInfo(lineNumber, description = '', content = '') {
        getElem(this.c+'L'+lineNumber+'D').innerHTML = description;
        getElem(this.c+'L'+lineNumber+'C').innerHTML = content;
    }


    /** 
     * @param {number} node_id 
     */
    printCurrentNode(node_id) {

        let node = this.n.findNodeById(node_id);

        if (node === null || node === undefined) {
            // user clicked on 
            getElem(this.c+'id').innerHTML = '?';
            [1,2,3].forEach((i) => this.fillInfo(i));
        }
        else {
            getElem(this.c+'id').innerHTML = 'Node ' + node_id;
            this.fillInfo(1, 'Connections', node.c);
            this.fillInfo(2, 'X coordinate', node.x);
            this.fillInfo(3, 'Y coordinate', node.y);
        }

    }

    /** 
     * @param {string} edge_id 
     */
    printCurrentEdge(edge_id) {

        let edge = this.n.findEdgeById(edge_id);
        getElem(this.c+'id').innerHTML = 'Edge ' + edge.from.id + ' <-> ' + edge.to.id;
        this.fillInfo(1, 'Distance', edge.displen);
        [2,3].forEach((i) => this.fillInfo(i));

    }

    // ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- Node & Edge Highlighter

    /**
     * @param {number} id 
     * @param {boolean} cancel should this function restore the original color?
     */
    highlightNode(id, cancel = false) {

        let color = '#FF0000'; 
        if (cancel) color = '#98C2FC'; // the original blue

        let picked = this.v.data.nodes.get(id);
        picked.color = color;
        this.v.data.nodes.update(picked);

    }


    /**
     * @param {string} id 
     * @param {boolean} cancel should this function restore the original color?
     */
    highlightEdge(id, cancel = false) {

        let json = {
            color: '#848484',
            highlight: '#848484',
            hover:	'#848484'
        };
    
        let color = '#FF0000'; 
        if (cancel) color = '#2B7CE8'; // the original blue
        
        json.color = color;
        json.hover = color;
        json.highlight = color;
    
        let picked = this.v.data.edges.get(id);
    
        picked.color = json;
        this.v.data.edges.update(picked);
    
    }

    /**
     * @param {string} id 
     * @param {boolean} cancel should this function restore the original color?
     */
    hideEdge(id, cancel = false) {

        let json = {
            color: '#848484',
            highlight: '#848484',
            hover:	'#848484'
        };
    
        let color = '#EEEEEE'; 
        if (cancel) color = '#2B7CE8'; // the original blue
        
        json.color = color;
        json.hover = color;
        json.highlight = color;
    
        let picked = this.v.data.edges.get(id);
    
        picked.color = json;
        this.v.data.edges.update(picked);
    
    }



    /**
     * @param {NetNode} node 
     */
    highlightRootNode(node, cancel = false) {

        if (cancel) {
            this.highlightNode(node.id, cancel);
        }
        else {
            this.highlightNode(node.id);
            node.e.forEach((edge) => {
                this.highlightEdge(edge.id, true);
            });
        }
    }

}