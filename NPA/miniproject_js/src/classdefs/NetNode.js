
/** 
 * Class definition for node: 
 *      connected with edges to other nodes 
 *      (remembers the edges coming from itself) 
 * 
 * Represents an access point in the graph.
 * 
 */
class NetNode {

    /** @type {number} */   _id;            get id() { return this._id; }
    /** @type {string} */   _label;
    /** @type {number} */   _x_coordinate;  get x() { return this._x_coordinate; }
    /** @type {number} */   _y_coordinate;  get y() { return this._y_coordinate; }

    /** Number of connections made from this node */
    /** @type {number} */   _n_connected;   get c() { return this._n_connected; }
    
    /** Array of edges coming out of this node. (array of references) */
    /** @type {NetEdge[]} */ _edges;        get e() { return this._edges; }

    /** Number of users connected to this node () */
    /** @type {number} */ _users;           get u() { return this._users; }
    
    /** @type {number} real index in NetGraph array of nodes */
    get ri() { return this._id - 1; }

    /**
     * @param {number} id
     * @param {number} x
     * @param {number} y
     */
    constructor(id, x = 0, y = 0) {
        this._id = id;
        this._label = " " + id + " ";
        this._x_coordinate = x;
        this._y_coordinate = y;

        this._n_connected = 0; // 0 connected edges so far
        this._edges = []; // no edges coming from this node 

        this._users = genRandomUsers();

        this.checkForSpecialNode(id);
    }

    /**
     * Modifies attributes for special nodes
     * @param {number} id 
     */
    checkForSpecialNode(id) {

        switch(id) {
            case 90:
                this._label = ' [ 0 ; 0 ] ';
                this._x_coordinate = 0;
                this._y_coordinate = 0;
            break;

            case 91: 
                this._label = ' [ K ; K ] ';
                this._x_coordinate = 1000;
                this._y_coordinate = 1000;
            break;

            case 92:
                this._label = ' [ -K; K ] ';
                this._x_coordinate = -1000;
                this._y_coordinate = 1000;
            break;

            case 93:
                this._label = ' [ K ;-K ] ';
                this._x_coordinate = 1000;
                this._y_coordinate = -1000;
            break;

            case 94:
                this._label = ' [ -K;-K ] ';
                this._x_coordinate = -1000;
                this._y_coordinate = -1000;
            break;

        }

    }

    isDisconnected() {
        return this._n_connected === 0;
    }

    /** @param {NetEdge} edge */
    registerEdge(edge) {
        if (edge.performRegister(this)) {
            this._edges.push(edge);
            this._n_connected += 1;
        }
    }

    /**
     * 
     * @param {NetNode} other 
     */
    equals(other) {
        return this._id === other.id;
    }
    
    get data() {
        return { id: this._id, label: this._label, x: this._x_coordinate, y: this._y_coordinate };
    }

    /** @returns {NetNeighbour[]} */
    getNeighbours() {
        let neighbours = [];
        this._edges.forEach((edge) => {

            // Let's find node reference from this Edge object - we want the other one (not "this" node)
            let ref = this.equals(edge.from) ? edge.to : edge.from;
            let n = new NetNeighbour(ref, edge);
            neighbours.push(n);

        });
        return neighbours;
    }

}

/** Helper class for easier access to these two attributes. */
class NetNeighbour {
    /**
     * @param {NetNode} noderef reference to the neighbour
     * @param {NetEdge} edge the reference to the edge connecting THIS node to the neighbour (noderef)
     */
    constructor(noderef, edge) {
        this.reference = noderef;
        this.edgeref = edge;
    }
}