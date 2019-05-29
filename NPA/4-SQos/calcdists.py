import networkx as nx
import numpy as np
import matplotlib.pyplot as plt
import pprint as pp

def radius(graph):
    max_length = 0

    paths = nx.shortest_path(graph)

    max_length = 0
    for _, val in paths.items():
        sorted_p = sorted(val.items(), key=lambda x: len(x[1]) , reverse=True)
        length = len(sorted_p[0][1])-1
        if length > max_length:
            max_length = length

    return max_length


def avg_hop_path(graph):
    paths = nx.shortest_path(graph)

    N = graph.number_of_nodes()

    path_sum = 0
    for _, source in paths.items():
        for _, p in source.items():
            path_sum = path_sum + len(p)-1

    return path_sum/(N*(N-1))

def main():
#    singleRingConn = np.array([ [0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0 ,1],
#                                [1, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0 ,0],
#                                [0, 1, 0, 1, 0, 0, 0, 0, 0, 0, 0 ,0],
#                                [0, 0, 1, 0, 1, 0, 0, 0, 0, 0, 0 ,0],
#                                [0, 0, 0, 1, 0, 1, 0, 0, 0, 0, 0 ,0],
#                                [0, 0, 0, 0, 1, 0, 1, 0, 0, 0, 0 ,0],
#                                [0, 0, 0, 0, 0, 1, 0, 1, 0, 0, 0 ,0],
#                                [0, 0, 0, 0, 0, 0, 1, 0, 1, 0, 0 ,0],
#                                [0, 0, 0, 0, 0, 0, 0, 1, 0, 1, 0 ,0],
#                                [0, 0, 0, 0, 0, 0, 0, 0, 1, 0, 1 ,0],
#                                [0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 0 ,1],
#                                [1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1 ,0]])
#
#    g = nx.DiGraph(singleRingConn)
    
    g = nx.cycle_graph(12)

    rad = radius(g)

    print("Radius: ", rad)

    ahp = avg_hop_path(g)

    print("Average hop path: ", ahp)

    nx.draw(g, with_labels=True, font_weight='bold')
    plt.show()



if __name__ == "__main__":
    main()