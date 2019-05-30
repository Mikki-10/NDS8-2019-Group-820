import numpy as np
import scipy.linalg as lin

l=1
mu=l/5
a=l
b=l/9

A=np.array([[-(mu+a),a],[b,-(mu+b+l)]])
B=np.array([[mu,0],[0,mu]])
C=np.array([[0,0],[0,l]])

A0=np.array([[-a,a],[b,-(b+l)]])

def find_R():
    R=np.array([[0,0],[0,0]])
    for i in range(100):
        R=-np.dot((C+np.dot(np.dot(R,R),B)),np.linalg.inv(A))
    return(R)

R=find_R()

P0=lin.null_space(np.transpose(A0+np.dot(R,B)))
print(A0+np.dot(R,B))
print(P0)