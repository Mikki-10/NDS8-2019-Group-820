/**
 *       Header file for JS sources
 * 
 *             Class diagram
 *
 * ----------------------------------------
 *   
 *   
 *          NetNode <-> NetEdge
 *                   |
 *                   v
 *                NetGraph
 *                   ^
 *                   |
 *                   v
 *                VisGraph
 *
 * ----------------------------------------
 *
 */


// ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- Frontend

/** @param {string} id */
function getElem(id) { return document.getElementById(id); }

/** @returns {number} random coordinate on the graph */
function genRandomCoord() { 
    // return Math.floor(Math.random() * 1e3); 
    return Math.floor(Math.random() * 2e3) - 1e3; 
}

/** @returns {number} picks random node between 1 and 20 */
function pickRandomNode() { return Math.floor(Math.random() * 19) + 1; }

/** @returns {number} random number of users per access point */
function genRandomUsers() { return Math.floor(Math.random() * 10) + 1; }

// ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- ---- Other functions

/**
 * @param {Set<number>} set1 
 * @param {Set<number>} set2 
 */
function isSetEqual(set1, set2) {

    if (set1.size !== set2.size) return false;
    for (let x of set1) {
        if (!set2.has(x)) {
            return false;
        }
    }
    return true;

}

/**
 * @param {Set<number>} majorSet 
 * @param {Set<number>} minorSet 
 */
function setDifference(majorSet, minorSet) {

    let retset = new Set();
    for (let item of majorSet) {
        if (!minorSet.has(item)) {
            retset.add(item);
        }
    }
    return retset;

}

/**
 * 
 * @param {number} a 
 * @param {number} b 
 */
function min(a, b) {
    if (a < b) return a; else return b;
}