
/** 
 * Class representing the whole graph 
 */
class NetGraph {

    /** @type {NetNode[]} */    _nodes;     get nodes() { return this._nodes; }
    /** @type {NetEdge[]} */    _edges;     get edges() { return this._edges; }
    /** @type {NetNode} */      _root;      get root() { return this._root; }       set root(node) { this._root = node; }
    /** @type {NetNode[]} */    _dispnodes;

    /** @param {Assignment} generator */
    constructor(generator = null) {

        if (generator === null) {
            return;
        }

        // 1st assignment
        /** @type {NetNode[]} */
        this._nodes = [];
        this._edges = [];
        this._root = null;
        this._dispnodes = [];

        // Let the assignment generator fill the rest
        generator.setupGraph(this);
    }
    
    /**
     * Nodes getter: creates deep copy & adds placeholder node at [0, 0]
     * @returns {NetNode[]}
     */
    get displaynodes() {
        return this._nodes.concat(this._dispnodes);
    }
    
    /**
     * @param {number} node_id between 0 and 19
     * @returns {NetNode}
     */
    findNodeById(node_id) {
        return this._nodes[node_id - 1];
    }

    /**
     * @param {string} edge_id 
     * @returns {NetEdge}
     */
    findEdgeById(edge_id) {
        for (let i = 0; i < this._edges.length; i++) {
            if (this._edges[i].id === edge_id) {
                return this._edges[i];
            }
        }
    }



    /**
     * Finds the longest edge in the graph.
     * @returns {string}
     */
    longestEdge() {
        let longest_dist = 0;
        let longest_id = new NetEdge();
        this._nodes.forEach((node) => {
            for (let i = 0; i < node.c; i++) {
                let ce = node.e[i]; // current edge
                if (ce.len >= longest_dist) {
                    longest_dist = ce.len;
                    longest_id = ce;
                }
            }
        });
        return Math.floor(longest_dist) + " ("+ longest_id.from.id + ", " + longest_id.to.id + ")";
    }

    /**
     * Calculates the number of K-connectionness of this graph.
     * @returns {number}
     */
    graphConnection() {

        let n_connectionness = 100;
        this._nodes.forEach((node) => {
            if (n_connectionness > node.c) {
                n_connectionness = node.c;
            }
        });
        return n_connectionness;
    
    }

    /**
     * Euclidean distance between two nodes in 2D space
     * https://www.calculatorsoup.com/calculators/geometry-plane/distance-two-points.php
     * @param {NetNode} from 
     * @param {NetNode} to 
     * @param {boolean} display should I cut off the decimal part?
     */
    static distanceBetween(from, to, display = false) {
    
        let dist_x = Math.pow(Math.abs(to.x - from.x), 2);
        let dist_y = Math.pow(Math.abs(to.y - from.y), 2);
        let distance = Math.sqrt(dist_x + dist_y);
        if (display) {
            distance = Math.floor(distance);
        }
        return distance;
    
    }

    /**
     * @param {NetNode} from 
     * @param {NetNode} to 
     * @returns {NetEdge}
     */
    static connectNodes(from, to) {
        let edge = new NetEdge();
        from.registerEdge(edge);
        to.registerEdge(edge);
        return edge;
    }

}
