var assignment = undefined;
var main = undefined;

// --- --- --- --- --- --- --- --- --- ---
// Pick an assignment

assignment = new Assignment1();
//assignment = new AssignmentX();

// --- --- --- --- --- --- --- --- --- ---

if (assignment != undefined) {
    assignment.execute();
    main = assignment.debug();
}
