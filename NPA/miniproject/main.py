import numpy as np
from scipy.sparse import csr_matrix
from scipy.sparse.csgraph import shortest_path

#def getPath(dest, pred):
#    path = []
#    
#    if pred[dest] == dest
#        return path.append(dest)
#    
#    dest2 = pred.index(dest)
#    return path.append( getPath(dest, pred) )
#    

def get_path(Pr, i, j):
    path = [j]
    k = j
    while Pr[i, k] != -9999:
        path.append(Pr[i, k])
        k = Pr[i, k]
    return path[::-1]


def main():                     
    # Get input                    1  2  3  4  5  6
    adjacency_matrix = np.array( [[0, 1, 1, 0, 0, 0], #1
                                  [0, 0, 0, 0, 0, 0], #2
                                  [0, 0, 0, 1, 0, 0], #3
                                  [0, 1, 0, 0, 0, 1], #4
                                  [0, 0, 1, 0, 0, 1], #5
                                  [0, 0, 0, 0, 0, 0]] ) #6

                                #  1  2  3  4  5  6
    graph =                      [[0, 2, 2, 0, 0, 0], #1
                                  [0, 0, 0, 0, 0, 0], #2
                                  [0, 0, 0, 2, 0, 0], #3
                                  [0, 2, 0, 0, 0, 2], #4
                                  [0, 0, 2, 0, 0, 2], #5
                                  [0, 0, 0, 0, 0, 0]] #6

    graph = csr_matrix(adjacency_matrix)

    dist_matrix, predecessors = shortest_path(csgraph=graph, directed=True, return_predecessors=True)
    
    path = get_path(predecessors,0,5) 
    dist = dist_matrix[0,5]


    print(dist_matrix)
    print("\n")
    #print(predecessors)
    #print("\n")
    print(np.array(path) + 1)
    print(dist)

    # Calculate the load and latency for the inital route
        # Calculate load
            # Define route
    
            # Put load on the route

        # Calculate latency


if __name__== "__main__":
  main()


# Calculate shortest paths for inital routes (dijsktra)

# Calculate the load and latency for the inital route
    # Calculate load
    # Calculate latency

# Do Flow deviation and recalulate the load and latency

# Show results
    # Visual repsnation