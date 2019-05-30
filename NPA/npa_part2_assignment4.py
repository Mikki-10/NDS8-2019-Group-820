import numpy as np
import scipy.linalg as lin

l=1
T=1
mu=l/5
a=1/(9*T)
b=1/(T)

A=np.array([[-(mu+a),a],[b,-(mu+b+l)]])
B=np.array([[mu,0],[0,mu]])
C=np.array([[0,0],[0,l]])

A0=np.array([[-a,a],[b,-(b+l)]])

def find_R():
    R=np.array([[0,0],[0,0]])
    for i in range(1000):
        R=-np.dot((C+np.dot(np.dot(R,R),B)),np.linalg.inv(A))
    return(R)

R=find_R()

P0=lin.null_space(np.transpose(A0+np.dot(R,B)))
print(A0+np.dot(R,B))

P0 = P0/np.sum(P0)
print(P0)

Pi=lin.null_space(np.transpose(C+np.dot(R,B)+np.dot(R,np.dot(R,B))))
print(Pi)
