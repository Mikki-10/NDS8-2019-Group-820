import random as rd
import math

N=[]

for i in range (20) : #create the nodes
    N.append([rd.random()*50,rd.random()*50])

E=[] #initialization of the edges matrix
for i in range (20):
    E.append([])
    for j in range(20):
        E[i].append(0)
        
for i in range(19): #first set of links to make the graph connected
    E[i][i+1]=math.sqrt((N[i][0]-N[i+1][0])**2+(N[i][1]-N[i+1][1])**2)
    E[i+1][i]=math.sqrt((N[i][0]-N[i+1][0])**2+(N[i][1]-N[i+1][1])**2)
    
for cpt in range (31): #other randomly chosen links
    i=0
    j=0
    while i==j or E[i][j]!=0 :
        i=rd.randint(0,19)
        j=rd.randint(0,19)
    E[i][j]=math.sqrt((N[i][0]-N[j][0])**2+(N[i][1]-N[j][1])**2)
    E[j][i]=math.sqrt((N[i][0]-N[j][0])**2+(N[i][1]-N[j][1])**2)

nb_usagers=[] #numbers of users
for i in range (20):
    nb_usagers=rd.randint(1,100)
    
root=rd.randint(1,19) #picking a root  
    
def take_first(A):
    return A[0]
    
def Kruskal_alg(E): 
    V=[]
    L=[]
    A=[]
    Sets=[]
    for i in range(0,20):
        Sets.append({i})
        for j in range(i+1,20):
            if E[i][j]!=0:
                V.append([E[i][j],(i,j)])
    V.sort(key=take_first)
    for i in range(len(V)):
        if V[i][1][0] not in Sets[V[i][1][1]] :
            for j in Sets[V[i][1][0]]:
                Sets[j]=Sets[j].union(Sets[V[i][1][1]])
            for j in Sets[V[i][1][1]]:
                Sets[j]=Sets[j].union(Sets[V[i][1][0]])
            A.append(V[i])
    return A
            
def Dijkstra_alg (E):
    complete_set = {i for i in range(20)}
    in_tree_set= set()
    A=[]
    W=[]
    for i in range(20):
        W.append([1000000,()])
    W[root]=[0,(0,0)]
    while (in_tree_set!=complete_set):
        m=100000000000
        index=-1
        for i in complete_set.difference(in_tree_set) :
            m=min(m,W[i][0])
            if m==W[i][0]:
                index=i            
        A.append(W[index][1])
        in_tree_set.add(index)
        for j in range(20):
            if E[index][j] != 0 :
                if W[j][0] > W[index][0]+E[index][j] :
                    W[j][0]=W[index][0]+E[index][j]
                    W[j][1]=(index,j)
    A_bonneforme=[]
    for i in range(20):
        A_bonneforme.append([E[A[i][0]][A[i][1]],A[i]])
    return A_bonneforme,W
    
            
        
def trench_length(A) : 
    T=0
    for i in range(len(A)):
        T+=A[i][0]
    return T
    
def fiber_length(W):
    return trench_length(W)
    
A,W=Dijkstra_alg(E)
print("Dijkstra trench length",trench_length(A))
print("Dijkstra fiber length",fiber_length(W))
        
A=Kruskal_alg(E)



K_E=[]



for i in range(20): #this is necessary to use Dijkstra on the MST 
    K_E.append([])
    for j in range(20):
        K_E[i].append(0)
for a in A :
    K_E[a[1][0]][a[1][1]]=a[0]
    K_E[a[1][1]][a[1][0]]=a[0]

print("Kruskal trench length",trench_length(A))
print("Kruskal fiber length",fiber_length(Dijkstra_alg(K_E)[1]))



        
