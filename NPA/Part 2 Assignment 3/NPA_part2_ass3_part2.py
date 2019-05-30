import math
import numpy as np
import matplotlib.pyplot as plt
import random as rd


def renewal(l,alpha):
    t=0
    state=1
    end_of_state=100
    S=[1]
    p=rd.random()
    end_of_state=(-math.log(1-p)/alpha)
    while (t<1000):
        if (t>end_of_state):
            if state==1:
                p=rd.random()
                end_of_state=t+(-math.log(1-p)/l)
                state=0
            else:
                p=rd.random()
                end_of_state=t+(-math.log(1-p)/alpha)
                state=1
        S.append(state)
        t+=1/100
    return S

S=renewal(0.2,1)
plt.close("all")
plt.plot([t/100 for t in range(100002)],S)
plt.show()

def auto_corr(l,alpha):
    sim=[]
    for i in range(1000):
        sim.append(renewal(l,alpha))
    S=0
    for s in sim:
        S+=s[1000]
    EI=S/1000
    A=[]
    for t in range(500):
        S=0
        for s in sim:
            S+=s[500]*s[500+t]
        A.append(S/1000-EI**2)
    return A

A=auto_corr(0.2,1)
plt.close("all")
plt.plot([t for t in range(500)],A)
plt.show()

P=np.fft.fft(A)
plt.close("all")
plt.loglog([t/100 for t in range(250)],[abs(p) for p in P[0:250]])
plt.show()







