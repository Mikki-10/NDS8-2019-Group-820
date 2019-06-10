import matplotlib.pyplot as plt
from pprint import pprint
import random
import numpy as np
import math
from scipy.sparse import csr_matrix
from scipy.sparse import csgraph
from scipy.sparse.csgraph import breadth_first_tree
import time
import pickle


class Message():
    def __init__(self, dest):
        self.dest = dest
        self.prev = []

class Node():
    def __init__(self, id, areaSize, p):
        self.id = id
        self.neighbours = []
        self.messages = []
        self.X = random.randint(0, areaSize)
        self.Y = random.randint(0, areaSize)
        self.p = p

    def addNeighbour(self, n):
        self.neighbours.append(n)

    def broadcast(self, dest):
        msg = Message(dest)
        msg.prev.append(self.id)
        for neighbour in self.neighbours:
            neighbour.messages.append(msg)

    def propergate(self):
        if len(self.messages) > 0:
            msg = self.messages.pop()
            #print("Node: ", self.id, " has message with dest: ", msg.dest)
            if msg.dest != self.id:
                msg.prev.append(self.id)
                
                for neighbour in self.neighbours:
                    #print("\tChecking if neighbour: ", neighbour.id, " has already seen this message.")
                    if neighbour.id not in msg.prev:
                        #print("\t\tNot seen yet.")
                        # Random propagation
                        if random.random() < self.p:
                            #print("\t\tPropergating to: ", neighbour.id)
                            neighbour.messages.append(msg)
                        #else:
                            #print("\t\tDropping message.")
                    #else:
                        #print("\t\tHas already been seen.")
                return False # Normal return after propergating
            else:
                print("\n\n******* BINGO ********")
                return True # Message has arrived

    def newPosition(self, areaSize):
        self.X = random.randint(0, areaSize)
        self.Y = random.randint(0, areaSize)

def dist(n1, n2):
    return math.sqrt( (n1.X-n2.X)**2 + (n1.Y-n2.Y)**2 )

def checkConnections(nodes, N, tx_dist):
    connections = np.zeros( (N,N), dtype=np.int8)
    for row in range(0, N):
        for col in range(0, N):
            # Make sure we don't try to connect to our selves
            if row != col:
                d = dist(nodes[row], nodes[col]) 
                if d < tx_dist:
                    connections[row][col] = 1
    #print(distances)
    #print("\n---------")
    return connections

def gen_map():
    pass

def main():
    num_nodes = 200
    nodes = []
    areaSize = 1000
    tx_dist = 100
    connected = False
    p = 0.4

    attempts = 0

    for i in range(0, num_nodes):
        nodes.append( Node(i, areaSize, p) )

    last = time.time()
    # Create nodes
    while not connected:
        attempts = attempts + 1

        for node in nodes:
            node.newPosition(areaSize)

        # Build adjacency matrix
        connections = checkConnections(nodes, num_nodes, tx_dist)
        
        n_components = csgraph.connected_components(csr_matrix(connections), directed=False, return_labels=False)
        #print(n_components)
        #print(connections)
        #print(labels)
        #exit()

        if n_components == 1:
            connected = True
        
        if attempts % 1000 == 0:
            print("Current attempt count: ", attempts)
            print("loop time: ", time.time() - last)
            last = time.time()

    print("\n----Did it in: ", attempts, "attempts.\n")
    #print("search_result:")
    #print(search_result.toarray().astype(int))

    print("\n----")
    print(connections)

    # Save everything
    with open('map.pkl', 'wb') as f:
        pickle.dump([
                num_nodes,
                nodes,
                areaSize,
                tx_dist,
                connected,
                connections,
                p ], f)

    fig, ax = plt.subplots()
    ax.set_xlim(0, areaSize)
    ax.set_ylim(0, areaSize)
    ax.set_aspect('equal')
    for node in nodes:
        ax.scatter(node.X, node.Y)
        if num_nodes <= 10:
            circ = plt.Circle((node.X, node.Y), radius=tx_dist, fill=False)
            ax.annotate(node.id, (node.X, node.Y))
            ax.add_artist(circ)

    ax.grid()
    #plt.show()

    #
    #
    #   Gossiping
    #
    #



    for row in range(0, num_nodes):
        for col in range(0, num_nodes):
            if connections[row][col] == 1:
                nodes[row].addNeighbour(nodes[col])
                ax.plot((nodes[row].X, nodes[col].X), (nodes[row].Y, nodes[col].Y), 'r-')


    plt.show()
    
    propergations = 0
    broadcasts = 0
    msg_received = False
    while not msg_received:
        nodes[0].broadcast(num_nodes-1)
        broadcasts = broadcasts + 1

        for _ in range(10):
            for node in nodes:
                propergations = propergations + 1
                if node.propergate() == True:
                    msg_received = True
                    break

            if msg_received:
                break
            

            #print("nr_messages: ", n_msg)
    
    # Count number of messages in play
    n_msg = 0
    for node in nodes:
        n_msg = n_msg + len(node.messages)

    print("\n-----")
    print("Number of nodes: ", num_nodes)
    print("Number of re-broadcasts: ", broadcasts)
    print("Number of propergations: ", propergations)
    print("Messages in play: ", n_msg)
    print("Ratio of nodes in possesion: ", n_msg/num_nodes)

    #print(nodes[0].neighbours)
    #print(connMat)

if __name__ == "__main__":
    main()