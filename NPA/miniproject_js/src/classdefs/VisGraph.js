
/**
 * 
 */
class VisGraph {

    /** @type { {vis.DataSet, vis.DataSet} } */ _vis_data;
    /** @type {HTMLElement} */ _vis_container;
    /** @type {vis.Network} */ _vis_network;
    /** @type {{}} */ _vis_options;
    /** @type {NetGraph} */ _ref_netgraph;      
    /** @type {Printer} */ _printer;            get printer() { return this._printer; }


    /**
     * Get the object { nodes: vis_nodes, edges: vis_edges }
     * this can be used for updating the GUI
     * 
     * further (inner) getters:
     *      nodes
     *      edges
     */
    get data() { return this._vis_data; }


    /**
     * Reference to NetGraph object
     */
    get netgraph() { return this._ref_netgraph; }


    constructor() {}

    /**
     * @param {NetGraph} netgraph 
     */
    setupVisGraph(netgraph) {

        // Keep the reference to NetGraph
        this._ref_netgraph = netgraph;

        this.setOptions();

        let vis_nodes = new vis.DataSet([]);
        let vis_edges = new vis.DataSet([]);

        this._ref_netgraph.displaynodes.forEach((node) => {
            vis_nodes.add(node.data);
        });

        this._ref_netgraph.edges.forEach((edge) => {
            vis_edges.add(edge.data);
        });
        
        // assign it to the data container
        this._vis_data = { nodes: vis_nodes, edges: vis_edges };

        // assign this to html element
        this._vis_container = getElem('network');

        // create the main gui network object
        this._vis_network = new vis.Network(this._vis_container, this._vis_data, this._vis_options);

        this._printer = new Printer(this);
        this.setCallBack();

    }

    /**
     * Sets GUI Network options.
     */
    setOptions() {

        this._vis_options = {
            physics: false,
            nodes: {
                shape: 'circle'
            },
            manipulation: {
                enabled: false
            },
            interaction: {
                dragNodes: false,
                hover: true
            }
        };

    }

    /**
     * Sets UI callback for user clicks:
     *  -> clicking on a node
     *  -> clicking on an edge
     */
    setCallBack() {

        this._vis_network.on('click', (event) => {
            if (event.nodes.length === 1) {
                this._printer.printCurrentNode(event.nodes[0]);
            }
            else if (event.nodes.length === 0 && event.edges.length === 1) {
                this._printer.printCurrentEdge(event.edges[0]);
            }
        });

    }

}