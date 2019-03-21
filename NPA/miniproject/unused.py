def waiting_time(iterations, load_matrix, capacity_matrix, input_trafic):
    # Move trafic and optimize the waiting time
    delta=load_matrix[0][1]/iterations
    delta_2 = load_matrix[4][5]/iterations
    wait_times_12 = 999999
    wait_times_56 = 999999

    for i in range(1, iterations):
        # Move trafic
        load_matrix[0][1] = load_matrix[0][1] - delta # Move trafic from the link between 1 and 2 to "someware else"
        load_matrix[4][5] = load_matrix[4][5] - delta_2 # Move trafic from the link between 5 and 6 to "someware else"
        
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

        # Check if the new wating time is better and save the state
        if wait_times_12 > w12:
            wait_times_12 = w12
            wait_times_56 = w56
            opti_load_matrix = load_matrix
            opti_iterations = i


    print("Waiting time from 1 to 2 times:")
    print(wait_times_12)
    print("\nWaiting time from 5 to 6 times:")
    print(wait_times_56)
    print("Iterations: ", opti_iterations)
    print("\nLoad matrix:")
    print(opti_load_matrix)
