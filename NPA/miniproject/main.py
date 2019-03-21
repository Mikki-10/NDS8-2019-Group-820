import numpy as np
import copy
from scipy.sparse import csr_matrix
from scipy.sparse.csgraph import shortest_path
    

# Build path from previous destinations
def get_path(Pr, i, j):
    path = [j]
    k = j
    while Pr[i, k] != -9999:
        path.append(Pr[i, k])
        k = Pr[i, k]
    return path[::-1]


def main():
    # Setup network with adjacency matrix (connections)
                                  #1  2  3  4  5  6
    adjacency_matrix =           [[0, 1, 1, 0, 0, 0], #1
                                  [0, 0, 0, 0, 0, 0], #2
                                  [0, 0, 0, 1, 0, 0], #3
                                  [0, 1, 0, 0, 0, 1], #4
                                  [0, 0, 1, 0, 0, 1], #5
                                  [0, 0, 0, 0, 0, 0]] #6

    # service matrix
                                 # 1  2  3  4  5  6
    capacity_matrix =            [[0, 2, 2, 0, 0, 0], #1
                                  [0, 0, 0, 0, 0, 0], #2
                                  [0, 0, 0, 2, 0, 0], #3
                                  [0, 2, 0, 0, 0, 2], #4
                                  [0, 0, 2, 0, 0, 2], #5
                                  [0, 0, 0, 0, 0, 0]] #6

    load_matrix = np.zeros((6, 6))  # Make a emty load matrix

    graph = csr_matrix(adjacency_matrix) # convert the adjacency_matrix to a graph

    dist_matrix, predecessors = shortest_path(csgraph=graph, directed=True, return_predecessors=True)  # calculate the shortest path matrix
    print("Predecessors:")
    print(np.array(predecessors)+1)
    # Calculate shortest paths for inital routes (dijsktra)
    path_1 = np.array(get_path(predecessors,0,1)) # get the shortest path from 1 to 2
    path_2 = np.array(get_path(predecessors,4,5)) # get the shortest path from 5 to 6
    dist_1 = dist_matrix[0,1] # get the shortest distance from 1 to 2
    dist_2 = dist_matrix[4,5] # get the shortest distance from 5 to 6

    print(dist_matrix)
    print("\n")
    #print(predecessors)
    #print("\n")
    print(path_1 + 1)
    print(path_2 + 1)
    print(dist_1)
    print(dist_2)
    print("\n") 
    

    # Calculate the load for the inital route
    row = path_1[0]
    for i in range(1, len(path_1)):
        load_matrix[row][path_1[i]] = 1

    row = path_2[0]
    for i in range(1, len(path_2)):
        load_matrix[row][path_2[i]] = 1

    print(load_matrix)


    # Calculate latency/waiting time for the inital route
    # 1/input_trafic[(load_1/capacity_1-load_1)+(load_2/capacity_2-load_2)]
    input_trafic = 1 # set the input trafic

    print(1/input_trafic*(
    (load_matrix[0][1]/(capacity_matrix[0][1]-load_matrix[0][1]))+
    (load_matrix[2][3]/(capacity_matrix[2][3]-load_matrix[2][3]))))

    print(1/input_trafic*(
    (load_matrix[4][5]/(capacity_matrix[4][5]-load_matrix[4][5]))+
    (load_matrix[2][3]/(capacity_matrix[2][3]-load_matrix[2][3]))))


    # Do some calculations
    iterations = 10000
    #print("Optimize waiting time:\n")
    #load_matrix_1 = copy.deepcopy(load_matrix) 
    load_matrix_2 = copy.deepcopy(load_matrix)
    #waiting_time(iterations, load_matrix_1, capacity_matrix, input_trafic)
    print("Loadbalance:\n")
    loadbalance(iterations, load_matrix_2, capacity_matrix, input_trafic)

def loadbalance(iterations, load_matrix, capacity_matrix, input_trafic):
    # Move trafic by loadbalance and calculate the waiting time
    delta = load_matrix[0][1]/iterations # Set a delta used to define how much data there is moved
    delta_2 = load_matrix[4][5]/iterations # Set a delta used to define how much data there is moved

    for _ in range(1, iterations):
        # Move trafic
        a_1 = capacity_matrix[0][1] / ((capacity_matrix[0][1] - load_matrix[0][1])**2) # Calculate the load on the link
        a_3 = capacity_matrix[2][3] / ((capacity_matrix[2][3] - load_matrix[2][3])**2) # Calculate the load on the link
        a_2 = capacity_matrix[4][5] / ((capacity_matrix[4][5] - load_matrix[4][5])**2) # Calculate the load on the link

        if a_1 > a_3:
            load_matrix[0][1] = load_matrix[0][1] - delta # Move trafic from the link between 1 and 2 to "someware else"
        if a_3 > a_1:
            load_matrix[0][1] = load_matrix[0][1] + delta # Move trafic to the link between 1 and 2
        if a_2 > a_3:
            load_matrix[4][5] = load_matrix[4][5] - delta_2 # Move trafic from the link between 5 and 6 to "someware else"
        if a_3 > a_2:
            load_matrix[4][5] = load_matrix[4][5] + delta_2 # Move trafic to the link between 5 and 6

        load_matrix[0][2] = input_trafic - load_matrix[0][1] # Calculate the load on the link between 1 to 3
        load_matrix[3][1] = input_trafic - load_matrix[0][1] # Calculate the load on the link between 4 to 2
        load_matrix[4][2] = input_trafic - load_matrix[4][5] # Calculate the load on the link between 5 to 3
        load_matrix[3][5] = input_trafic - load_matrix[4][5] # Calculate the load on the link between 4 to 6
        load_matrix[2][3] = (input_trafic-load_matrix[0][1])+(input_trafic-load_matrix[4][5]) # Calculate the load on the link between 3 to 4

        # Calculate waiting time 
        w12 = 1 / input_trafic * ( # w12 = waiting time from all links from 1 to 2 
            ( load_matrix[0][1] / (capacity_matrix[0][1] - load_matrix[0][1]) ) +
            ( load_matrix[2][3] / (capacity_matrix[2][3] - load_matrix[2][3]) ) +
            ( load_matrix[0][2] / (capacity_matrix[0][2] - load_matrix[0][2]) ) +
            ( load_matrix[3][1] / (capacity_matrix[3][1] - load_matrix[3][1]) ))

        w56 = 1 / input_trafic * ( # w56 = waiting time from all links from 5 to 6
            ( load_matrix[4][5] / (capacity_matrix[4][5] - load_matrix[4][5]) ) +
            ( load_matrix[2][3] / (capacity_matrix[2][3] - load_matrix[2][3]) ) +
            ( load_matrix[4][2] / (capacity_matrix[4][2] - load_matrix[4][2]) ) +
            ( load_matrix[3][5] / (capacity_matrix[3][5] - load_matrix[3][5]) ))

        wait_times_12 = w12 # w12 = waiting time from all links from 1 to 2
        wait_times_56 = w56 # w56 = waiting time from all links from 5 to 6

        #print(load_matrix)

    print("Waiting time from 1 to 2 times:")
    print(wait_times_12)
    print("\nWaiting time from 5 to 6 times:")
    print(wait_times_56)
    print("\nLoad matrix:")
    print(load_matrix)





if __name__ == "__main__":
  main()









# Calculate the load and latency for the inital route
       # Calculate load
    # Define route

    # Put load on the route

    # Calculate latency

# Calculate shortest paths for inital routes (dijsktra)

# Calculate the load and latency for the inital route
    # Calculate load
    # Calculate latency

# Do Flow deviation and recalulate the load and latency

# Show results
    # Visual repsnation here
