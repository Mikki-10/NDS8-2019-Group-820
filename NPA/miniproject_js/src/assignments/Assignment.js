/**
 * Generic Assignment class.
 */

class Assignment {

    /** @type {VisGraph} */ get visgraph() { return this._v; };
    /** @type {NetGraph} */ get graph() { return this._g; };
    /** @type {Printer} */  get printer() { return this._p; };

    constructor() {
        this._v = new VisGraph(this);
        this._g = new NetGraph(this);
        this._v.setupVisGraph(this._g);
        this._p = this._v.printer;
    }
   
    setupGraph() {}

    execute() {
        $('#content').html(this.content());
    }

    content() { return ''; }

    debug() {
        return this._v;
    }

}