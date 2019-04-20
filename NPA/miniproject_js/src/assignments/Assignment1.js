/**
 * Assignment 1 source file.
 * 
 * Modifies the constructor of NetGraph
 */

class Assignment1 extends Assignment {

    constructor() {
        super();
        this.app = 'app0';
    }

    /** @param {NetGraph} netgraph */
    setupGraph(netgraph) {

        // Generate 20 nodes!
        for (let i = 1; i <= 20; i++) {
            netgraph._nodes.push(new NetNode(i, genRandomCoord(), genRandomCoord()));
        }

        // Create first 19 connections
        for (let i = 1; i < 20; i++) {
            netgraph._edges.push( NetGraph.connectNodes( netgraph._nodes[i-1], netgraph._nodes[i] ) );
        }

        // Add other until 50, randomly
        while (netgraph._edges.length < 50) {
            let shouldRepeat = false;

            let rand_from = pickRandomNode();
            let rand_to = pickRandomNode();
            while ( rand_from === rand_to ) { rand_to = pickRandomNode(); }

            // Prevent creating identical edges
            for (let i = 0; i < netgraph._edges.length; i++) {
                let eq = NetEdge.equals(netgraph._edges[i], rand_from, rand_to);
                if (eq) {
                    shouldRepeat = true;
                    break;
                }
            }

            if (shouldRepeat) continue;

            netgraph._edges.push( NetGraph.connectNodes( netgraph._nodes[rand_from], netgraph._nodes[rand_to] ) );

        }

        for (let i = 90; i < 95; i++) {
            netgraph._dispnodes.push(new NetNode(i));
        }

        // Select root randomly
        netgraph._root = netgraph._nodes[pickRandomNode()];

    }

    execute() {
        super.execute();
        let vg = this.visgraph;
        
        // Number of nodes & edges generated, print & highlight root
        getElem('npa_nodes').innerHTML = this.graph.nodes.length;
        getElem('npa_edges').innerHTML = this.graph.edges.length;
        getElem('npa_rootnode').innerHTML = this.graph.root.id;
        getElem('npa_longest').innerHTML = this.graph.longestEdge();            // Find the longest edge
        getElem('npa_xconnected').innerHTML = this.graph.graphConnection();     // Graph is N-connected. N=?
        vg.printer.highlightRootNode(this.graph.root);

        // Program shortest path spanning tree and minimum spanning tree algorithms.
        // Calculate the trenching and fiber required in both cases with point-to-point architecture.
        // Modify the root and re-run the experiment.

        // Runs whole sub-app
        let runApp = () => {
            this.graph.edges.forEach((e) => vg.printer.highlightEdge(e.id, true));
            getElem('npa_trenching').innerHTML = "";
            getElem('npa_fiber').innerHTML = "";
            getElem('npa_algorithm').innerHTML = "";

            switch(this.app) {
                case 'app1': recolorGraph(spst());    break; // dijkstra
                case 'app2': recolorGraph(mst());     break; // kruskal
            }
        }

        // Button for changing root
        getElem('npa_chroot').addEventListener('click', () => {
            var result = prompt("Pick new node root, ID's scaling from 1 to 20.", "");
            let newRootIndex = parseInt(result);
            if (newRootIndex !== NaN && newRootIndex <= 20 && newRootIndex >= 1) {
                vg.printer.highlightRootNode(this.graph.root, true);
                this.graph.root = this.graph.findNodeById(newRootIndex);
                vg.printer.highlightRootNode(this.graph.root);

                runApp(); // if dijkstra or kruskal is currently running, recalculate with new root
            }
            else {
                alert("Please enter number 1-20.");
            }
        });


        /**
         * Recolors edges according to results with spst or mst.
         * @param { {tree:{trench: number, fiber: number, results: {dist: number, edge: NetEdge}[]}, desc: string} } algorithm 
         */
        let recolorGraph = (algorithm) => {
            getElem('npa_algorithm').innerHTML = algorithm.desc;
            getElem('npa_trenching').innerHTML = Math.floor(algorithm.tree.trench);
            getElem('npa_fiber').innerHTML = Math.floor(algorithm.tree.fiber);

            // Hide all first
            this.graph.edges.forEach((e) => { vg.printer.hideEdge(e.id); });

            // Highlight the spanning tree edges
            algorithm.tree.results.forEach((item) => {
                if (item.edge === undefined) return;
                vg.printer.highlightEdge(item.edge.id);
            });

        }

        let spst = () => { // spst == shortest-path spanning tree
            return {
                tree: this.shortest_path_spanning_tree(this.graph),
                desc: "Shortest path spanning tree"
            }
        }
        
        let mst = () => { // mst == minimum spanning tree
            return {
                tree: this.minimum_spanning_tree(this.graph),
                desc: "Minimum spanning tree"
            }
        }

        // Bind the App radio buttons
        $(document).on("click", "#app_control", (event) => {
            let clicked = event.target.firstElementChild.id;
            if (this.app === clicked) return;
            this.app = clicked;
            runApp();
        });

    }

    /** 
     * @param {NetGraph} graph 
     * @returns {{trench: number, fiber: number, results: {dist: number, edge: NetEdge}[]}} trench length, fiber length and edges to be highlighted
     */
    shortest_path_spanning_tree(graph) {

        /** @param {{dist: number, edge: NetEdge}[]} distances */
        let getFiberLength = (distances) => {
            let l = 0;
            distances.forEach((item) => l += item.dist);
            return l;
        }

        /** @param {{dist: number, edge: NetEdge}[]} distances */
        let getTrenchLength = (distances) => {
            let l = 0;
            distances.forEach((item) => { if (item.edge !== undefined) l += item.edge.len });
            return l;
        }

        let dist = this.dijkstra(graph.root, graph.nodes);
        return {
            trench: getTrenchLength(dist), 
            fiber: getFiberLength(dist), 
            results: dist
        };
    }

    /**
     * @param {NetGraph} graph 
     * @returns {{trench: number, fiber: number, results: {dist: number, edge: NetEdge}[]}} trench length, fiber length and edges to be highlighted
     */
    minimum_spanning_tree(graph) {

        /**
         * @param {number} id 
         * @param {NetNode[]} nodes 
         */
        let mst_getNodeById = (id, nodes) => {
            for (let n of nodes) {
                if (id === n.id) return n;
            }
            return undefined;
        }

    
        let mst_edges = this.kruskal(graph);

        let mst_nodes = [];
        // Remove unnecessary edges from original nodes.. by creating copies, ew
        for (let i = 0; i < graph.nodes.length; i++) { mst_nodes.push(new NetNode(i + 1)); }

        let mst_root = mst_getNodeById(graph.root.id, mst_nodes);
        
        mst_edges.forEach((edge) => {

            let nf = mst_getNodeById(edge.from.id, mst_nodes);
            let nt = mst_getNodeById(edge.to.id, mst_nodes);

            nf._edges.push(edge);
            nf._n_connected++;
            edge._connection_from = nf;

            nt._edges.push(edge);
            nt._n_connected++;
            edge._connection_to = nt;

        });

        let min_graph = {
            root: mst_root,
            nodes: mst_nodes,
            edges: mst_edges
        };

        return this.shortest_path_spanning_tree(min_graph);
    }

    /**
     * @param {NetNode} root 
     * @param {NetNode[]} nodes 
     * @param {NetEdge[]} edges 
     * @returns {{dist: number, edge: NetEdge}[]}
     */
    dijkstra(root, nodes) {

        /**
         * Initialises array of prototype objects {dist,edge}
         * @param {number} length number of nodes
         */
        let initDistances = (length) => {
            /** @type { {dist: number, edge: NetEdge}[] } */ let d = [];
            for (let i = 0; i < length; i++) {
                d.push({ dist: Infinity, edge: undefined });
            }
            return d;
        };

        /** 
         * Transforms array of nodes into set of nodes.
         * @param {NetNode[]} nodes 
         */
        let makeSet = (nodes) => {
            /** @type {Set<NetNode>} */ let s = new Set();
            for (let i = 0; i < nodes.length; i++) s.add(nodes[i]);
            return s;
        };

        /**
         * Helper function for inspecting the presence of complex object within set.
         * @param {Set<NetNode} nnset 
         * @param {NetNode} node 
         */
        let hasSet = (nnset, node) => {
            for (let i of nnset) if (i.id === node.id) return true;
            return false;
        };

        /**
         * Compares whole sets against them - true if they contain identical objects 
         * (does not rely on reference equality)
         * @param {Set<NetNode>} complete_set 
         * @param {Set<NetNode>} in_tree_set 
         */
        let equalSet = (complete_set, in_tree_set) => {
            if (complete_set.size !== in_tree_set.size) return false;
            for (let item of complete_set) {
                if (!hasSet(in_tree_set, item)) {
                    return false;
                }
            }
            return true;
        };

        /**
         * Creates new set, containing items that are not in second set.
         * @param {Set<NetNode>} major 
         * @param {Set<NetNode>} minor
         * @returns {Set<NetNode>}
         */
        let diffSet = (major, minor) => {
            let rs = new Set();
            for (let i of major) {
                if (!hasSet(minor, i)) rs.add(i);
            }
            return rs;
        };

        // --- Code

        let distances = initDistances(nodes.length);    // For each node, create proto-object {dist, edge}
        let complete_set = makeSet(nodes);              // Transform the nodes into a set (for later manipulation)
        let in_tree_set = new Set();                    // Create empty set, which will be iteratively filled with inspected nodes
        distances[root.ri].dist = 0;                    // Set distance to root to zero. Logic boy

        // For each node (but in pseudorandom order - the reason to use the two sets)
        while (!equalSet(complete_set, in_tree_set)) {

            let inspected = new NetNode(99);    // pointer to to-be-chosen node. initial value just for type-check (nothing serious)
            let minValue = Infinity;            // Comparation value which decides whenever new inspected node is picked

            for (let item of diffSet(complete_set, in_tree_set)) {
                let itemValue = distances[item.ri].dist
                minValue = min(minValue, itemValue);
                if (minValue === itemValue) { // yes, remember the node we want to inspect
                    inspected = item;
                }
            }

            // consider it inspected by now
            in_tree_set.add(inspected);

            // For each neighbor: find new shortest distances
            for (let neighbour of inspected.getNeighbours()) {
                let calc = distances[inspected.ri].dist + neighbour.edgeref.len;
                if (distances[neighbour.reference.ri].dist > calc) {
                    distances[neighbour.reference.ri].dist = calc;
                    distances[neighbour.reference.ri].edge = neighbour.edgeref;
                }
            }
        }
        return distances;

    }

    /**
     * Applies Kruskal's algorithm to graph, resulting with new one with minimum number of edges
     * @param {NetGraph} graph 
     * @returns {NetEdge[]} edges contained minimum spanning tree graph
     */
    kruskal(graph) {
        /**
         * Sorts array of edges by length, ascending.
         * @param {NetEdge[]} original_edges 
         */
        let sortEdges = (original_edges) => {

            /** @param {any} arr */
            let makeCopy = (arr) => {
                let a = []; let l = arr.length;
                for (let i = 0; i < l; i++) {
                    a.push(arr[i]);
                }
                return a;
            }
                
            /** 
             * Pops out the shortest edge from the input array.
             * @param {NetEdge[]} edges 
             */
            let popMinEdge = (edges) => {
                let min = Infinity;
                let min_edge = undefined;
                let min_index = undefined;

                let l = edges.length;
                for (let i = 0; i < l; i++) {
                    if (edges[i] == undefined) continue;
                    if (edges[i].len < min) {
                        min = edges[i].len;
                        min_edge = edges[i];
                        min_index = i;
                    }
                }

                if (min_index != undefined) {
                    edges[min_index] = undefined;
                }
                return min_edge;
            }

            let edges = makeCopy(original_edges);
            let sorted = [];

            let me = popMinEdge(edges);
            while(me !== undefined) {
                sorted.push(me);
                me = popMinEdge(edges);
            }
            return sorted;
        }

        /**
         * Finds the current value for given node (whether it's in the tree or not yet).
         * 
         * Initially, djs contains array of -1's.
         * if the value at index node.realindex is under zero, it means 
         * it hasn't been added to the spanning tree yet (I suppose, not sure tho)
         * if its positive int, it acts as new index and we repeat the search process
         * 
         * @param {number[]} djs disjoint set
         * @param {NetNode} node to be checked for
         */
        let find = (djs, node) => {
            let index = node.ri;  // initial value
            let val = djs[index];
            while (val > -1) {
                index = val;
                val = djs[index];
            }
            return index;
        }

        /**
         * Adds edge to the minimum spanning tree and modifies the disjoint set
         * @param {NetEdge[]} tree the minimum spanning tree
         * @param {number[]} djs disjoint set
         * @param {NetEdge} edge the edge to be added
         */
        let addToTree = (tree, djs, edge) => {
            tree.push(edge);

            let find_from_index = find(djs, edge.from);
            let find_to_index = find(djs, edge.to);

            let val1 = djs[find_from_index];
            let val2 = djs[find_to_index];

            if (val1 >= val2) {
                djs[find_from_index] = find_to_index;
                djs[find_to_index] = val1 + val2;
            }
            else {
                djs[find_from_index] = val1 + val2;
                djs[find_to_index] = find_from_index;
            }
            
        }

        let edgecopy = sortEdges(graph.edges); // graph.edges;
        let disjointset = [];
        let mst_edges = [];
        for (let i = 0; i < graph.nodes.length; i++) disjointset.push(-1);

        while(edgecopy.length > 0) {
            let current = edgecopy.shift();

            let f1 = find(disjointset, current.from);
            let f2 = find(disjointset, current.to);
            if (f1 !== f2) {
                addToTree(mst_edges, disjointset, current);
            }
        }

        return mst_edges;
    }

    content() {
        return String.raw`
            <div class="text-center">
            <div id="app_control" class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-secondary active">
                    <input type="radio" name="options" id="app0" autocomplete="off" checked> Basic 
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="app1" autocomplete="off"> Dijkstra (SPST)
                </label>
                <label class="btn btn-secondary">
                    <input type="radio" name="options" id="app2" autocomplete="off"> Kruskal (MST)
                </label>
            </div>
            </div>

            <br>

            <div class="row">
                <div class="col-md-6 text-center"> <b>No. of Nodes:</b> <span id="npa_nodes"></span> </div>
                <div class="col-md-6 text-center"> <b>No. of Edges:</b> <span id="npa_edges"></span> </div>
            </div>

            <div class="row">
                <div class="col-md-6 text-center"><b>N-connected:</b> <span id="npa_xconnected"></span></div>
            </div>
            <div class="row">                
                <div class="col-md-12 text-center"><b>Longest edge:</b> <span id="npa_longest"></span></div>
            </div>           

            <br>
            <div class="row">
                <div class="col-md-12 text-center align-middle"><b>Root node:</b> <span id="npa_rootnode"></span></div>
            </div>
            <div class="row">                
                <div class="col-md-12 text-center"><button class="btn btn-secondary" id="npa_chroot">Pick another root</button></div>
            </div>
            <br>

            <div class="row">                
                <div class="col-md-12 text-center"><b>Results with:</b> <span id="npa_algorithm"></span></div>
            </div>    
            <div class="row">
                <div class="col-md-6 text-center"><b>Trench:</b> <span id="npa_trenching"></span></div>
                <div class="col-md-6 text-center"><b>Fiber:</b> <span id="npa_fiber"></span></div>
            </div>

            <div class="container jumbotron">
                <h6 class="text-center">Information for <span id="current_id">?</span></h6>

                <div class="row">
                    <div class="col-md-6 text-center"><b id="current_L1D"></b></div>
                    <div class="col-md-6 text-center"><span id="current_L1C"></span></div>
                </div>

                <div class="row">
                    <div class="col-md-6 text-center"><b id="current_L2D"></b></div>
                    <div class="col-md-6 text-center"><span id="current_L2C"></span></div>
                </div>

                <div class="row">
                    <div class="col-md-6 text-center"><b id="current_L3D"></b></div>
                    <div class="col-md-6 text-center"><span id="current_L3C"></span></div>
                </div>
            </div>
        `;
    }

}
