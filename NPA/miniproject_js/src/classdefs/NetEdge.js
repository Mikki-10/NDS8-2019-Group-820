
/** 
 * Class definition for edge: 
 *      connecting two nodes 
 *      (and it remembers them) 
 */
class NetEdge {
    
    /** @type {string} 5-hex digit identificator */
    get id() { return this._id; }

    /** @type {NetNode} */
    get from() { return this._connection_from; }

    /** @type {NetNode} */
    get to() { return this._connection_to; }

    /** @type {number} distance between "from"-"to" */
    get len() { return this._length; }    

    /** @type {number} distance, but rounded up to integer */
    get displen() { return Math.floor(this._length); }

    constructor() {
        this._id = Math.random().toString(16).substr(2, 5);
        this._connection_from = null;
        this._connection_to = null;
        this._length = 0;
    }

    /** 
     * Callback of registerEdge 
     * @param {NetNode} node_id 
     */
    performRegister(node) {
        if (this._connection_from === null) {
            this._connection_from = node;
            return true;
        }
        else if (this._connection_to === null) {
            this._connection_to = node;
            // We got the second node, let's calculate distance for once
            this._length = NetGraph.distanceBetween(this._connection_from, this._connection_to);
            return true;
        }
        return false;
    }

    get data() {
        // label: '...', 
        return { id: this._id, from: this._connection_from.id, to: this._connection_to.id };
    }

    /**
     * Checks whether two edges would be identical (according to nodes connecting)
     * this is for existing edge and "to-be-created" edge
     * @param {NetEdge} edge the existing edge
     * @param {number} from the originating node
     * @param {number} to the destination node
     */
    static equals(edge, from, to) {

        if (edge.from.id === from) {
            return (edge.to.id === to);
        }
        else if (edge.to.id === from) {
            return (edge.from.id === to);
        }
        return false;

    }

}
